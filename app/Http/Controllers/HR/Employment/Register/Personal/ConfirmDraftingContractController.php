<?php

namespace App\Http\Controllers\HR\Employment\Register\Personal;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Accounting\Contract\PrintController;
use App\Http\Controllers\Controller;
use App\Models\Accounting\Contract\Contract;
use App\Models\Accounting\Contract\ContractClauseType;
use App\Models\Accounting\Contract\ContractKeyword;
use App\Models\Accounting\Contract\ContractRegister;
use App\Models\Accounting\Contract\ContractRegisterClauseType;
use App\Models\Accounting\Contract\ContractRegisterToken;
use App\Models\Accounting\CostCenter;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\Post\Post;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Post\PostUser;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

/*
این کنترلر برای  تایید قرارداد می باشد
 * */

class ConfirmDraftingContractController extends Controller
{
    protected $view_path = "hr.employment.register.personal.confirm_drafting_contract.";
    protected $route_path = "hr.employment.register.personal.confirm_drafting_contract.";


    public function index($key)
    {
        $employment = Employment::where("key", $key)->
        whereIn("status_id", [4640115, 4640121, 4640118])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $employment_document_type = EmploymentDocumentType::where([
            'employment_id' => $employment->id,
            'user_id' => $employment->user_id,
            'document_type_id' => 8,
            'receive_document_step_id' => 10,
        ])->first();
        if ($employment->worker->selling_type_id == 1) {
            if (empty($employment_document_type)) {
                return redirect()->back()->withErrors("لطفا ابتدا طب کار را بارگزاری نمایید.");
            }
        }
        if ($employment->worker->user_bank_accounts->count() == 0) {
            return redirect()->back()->withErrors("لطفا ابتدا اطلاعات بانکی خود را ثبت نمایید.");
        }


        $keys = self::CreateKeys($employment);
        if (!$keys["result"]) {
            return back()->withErrors($keys["error"]);
        }
        $contract = Contract::where('id', $employment->post->contract_id)->first();
        //بند
        if (!$contract) {
            return redirect()->back()->withErrors("قراردادی  برای پست شما انتخاب نشده است. با دفتر کارگزینی تماس بگیرید");
        }
        $contract_clause_type_list = ContractClauseType::join('clause_types', 'contract_clause_type.clause_type_id', 'clause_types.id')->
        where('contract_clause_type.contract_id', $contract->id)->
        orderBy('contract_clause_type.priority_number')->
        pluck('caption', "clause_type_id");
        $article_list = [];
        $clause_article_list = ContractClauseType::join('clause_articles', 'contract_clause_type.clause_article_id', 'clause_articles.id')->
        where('contract_clause_type.contract_id', $contract->id)->
        select('clause_articles.*', 'contract_clause_type.*')->
        get();
        //بند
        foreach ($clause_article_list as $item) {
            $article_list[$item->clause_type_id][$item->id] = $item;
        }

        $keys_id = [];
        foreach (ContractKeyword::all() as $item) {
            if (isset($keys["keys"][$item->keyword])) {
                $keys_id[$item->id] = $keys["keys"][$item->keyword];
            } else {
                $keys_id[$item->id] = "...............";
            }
        }

        return view($this->view_path . "index", compact('employment', 'contract_clause_type_list', 'keys_id', 'article_list', 'contract'));

    }

    public function submit($key, Request $request)
    {

        $employment = Employment::where("key", $key)->
        whereIn("status_id", [4640115, 4640121, 4640118])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $company_have_separate_warehousing_software = Setting::getIntegerValue("company_have_separate_warehousing_software");// نرم افزار انبار داری
        $company_have_separate_financial_software = Setting::getIntegerValue("company_have_separate_financial_software");// نرم افزار مالی

        $post_document_recieve_steps = PostDocumentReceiveStepConfirm::
        where([
            'is_necessary_to_deliver_document_to_archive' => 1,
            'post_id' => $employment->post_id,
        ])->
        get();
        //ایجاد جدول قرارداد ثبت شده
        $contract = Contract::where('id', $employment->post->contract_id)->first();
        if (!$contract) {
            return redirect()->back()->withErrors("قراردادی  برای پست شما انتخاب نشده است. با دفتر کارگزینی تماس بگیرید");
        }
        $result = self::CreateContractRegister($employment, $contract);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if ($post_document_recieve_steps->count() >= 1) {
            $employment->status_id = 4640117;//در انتظار تحویل مدارک به بایگانی
            $employment->save();
            event(new EmploymentLogEvent($employment, 4640020, null, null, $employment->user_id));
            return redirect()->route("login")->with(["success" =>
                "تایید قرارداد با موفقیت ثبت گردید. <br/>اقدامات بعدی به صورت پیامک برای شما از طریق ارسال خواهد شد." .
                "<br/>" . ""]);
        } else {
            Employment::AddUserToPost($employment);
            if ($company_have_separate_financial_software || $company_have_separate_warehousing_software) {
                $employment->status_id = 4640119;//آغاز همکاری در انتظار تعریف اطلاعات مالی
            }
            if (!$company_have_separate_warehousing_software && $company_have_separate_financial_software) {// نرم افزار انبار داری مرتبط با کد هزینه می باشد.
                self::CreateCostCenter($employment);
                $employment->status_id = 4640119;//آغاز همکاری در انتظار تعریف اطلاعات مالی
            }
            if (!$company_have_separate_financial_software && !$company_have_separate_warehousing_software) {
                self::CreateCostCenter($employment);
                $employment->status_id = 4640120;//آغاز همکاری در انتظار دریافت اکانت اینترنت
            }
            $employment->save();
            event(new EmploymentLogEvent($employment, 4640020, null, null, $employment->user_id));
            return redirect()->route("login")->with(["success" =>
                "تایید قرارداد با موفقیت ثبت گردید. <br/>نام کاربری و کلمه عبور برای ورود به سامانه به شماره همراه شما پیامک گردید." .
                "<br/>" . ""]);
        }


    }
    public static function CreateCostCenter($employment)
    {
        $worker = $employment->worker;
        $cost_center = CostCenter::create([
            "code" => 0,
            "caption" =>$employment->worker->fullname(),
            "status_id" => 1200,
        ]);
        $cost_center->code = "001" . "/" . $cost_center->id;
        $cost_center->save();
        $worker->cost_center_id = $cost_center->id;
        $worker->save();
    }
    public static function getCode($contract_register)
    {
        // افزودن شماره ثبت قرارداد به ۱۰۰۰ و سپس تبدیل به رشته
        $code = "DCCT/" . (1000 + $contract_register->id);

        return $code;
    }


    //تابع نمایش پی دی اف
    public function print($key)
    {

        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640115])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $keys = self::CreateKeys($employment);
        if (!$keys["result"]) {
            return back()->withErrors($keys["error"]);
        }
        $contract = Contract::where('id', $employment->post->contract_id)->first();


      return  PrintController::CreatePdfFile($contract, $employment, "قراداد" . $employment->worker->fullname(), $keys);

    }

    public static function CreateKeys($employment)
    {
        $company_name_setting = Setting::getStringValue("company_name");
        $company_address_setting = Setting::getStringValue("company_address");
        $national_code_setting = Setting:: getIntegerValue("national_code");
        $post_id_for_contract_setting = Setting:: getIntegerValue("post_id_for_contract");
        $post_user = PostUser::where('post_id', $post_id_for_contract_setting)->first();
        if (!$post_user && $employment->post_id!=$post_id_for_contract_setting) {
            return [
                "result" => false,
                "error" => "تنظیمات کد پست سازمانی جهت عقد قرارداد نامعتبر است."
            ];

        }
        $contract = Contract::where('id', $employment->post->contract_id)->first();
        if (!$contract) {
            return [
                "result" => false,
                "error" => "قراردادی  برای پست شما انتخاب نشده است. با دفتر کارگزینی تماس بگیرید"
            ];
        }
        if ($employment->worker->user_academic_degrees()->count() > 0) {
            $user_academic_degree = $employment->worker->user_academic_degrees()->orderByDesc('academic_degree_type_id')->first();
        }

        $keys = [
            "Name_B" => $employment->worker->fullname(),
            "Father_Name_B" => $employment->worker->father_name,
            "National_Code_B" => $employment->worker->national_code,
            "Address_A" => $company_address_setting,
            "Address_B" => $employment->worker->user_address()->first()->address->address,
            "Date_B" => $employment->worker->get_date_of_birth(),
            "Mobile_B" => $employment->worker->user_address()->first()->address->mobile,
            "Salary_Date" => $employment->post->basis_for_daily_salary == 1 ? "طبق قانون کار" : $employment->post->daily_salary,
            "Right_Work" => $employment->worker->right_to_work,
            "Number_Of_Contract" => $contract->number_of_contract,
            "Start_Date" => $employment->worker->get_start_date_of_contract(),
            "End_Date" => $employment->worker->get_end_date_of_contract(),
            "Company_name_A" => $company_name_setting,
            "Birth_Certificate_Number_B" => $employment->worker->birth_certificate_number,
            "Company_National_Code_A" => $national_code_setting,
            "Representative_A" => $post_user? $post_user->worker->fullname("with_gender_2") : "",
            "Name_A" =>$post_user? $post_user->worker->fullname("with_gender_2") : "",
            "Post_A" => $post_user->post->caption ?? "",
            "Post_B" => $employment->post->caption ?? "",
            "Academic_Degree_A" => $user_academic_degree->academic_degree_type->caption ?? "",
            "Duties_A" => $employment->worker->right_to_work ?? "",
            "Account_Number" => $employment->worker->user_bank_accounts()->first()->account_number ?? "",
            "Bank_Branch" => $employment->worker->user_bank_accounts()->first()->bank_branch ?? "",
            "Bank_Name" => $employment->worker->user_bank_accounts()->first()->bank->caption ?? "",
            "Number_Of_Days_Before_Termination_Of_Contract" => $employment->post->number_of_days_before_termination_of_contract ?? "",
        ];
        return [
            "result" => true,
            "keys" => $keys
        ];

    }

    public static function CreateContractRegister($employment, $contract)
    {
        $contract_register = ContractRegister::create([
            'contract_id' => $contract->id,
        ]);
        $code = self::getCode($contract_register);
        $contract_register->code = $code;
        $contract_register->save();
//ایجاد جدول بند های ثبت شده
        $clause_article_list = ContractClauseType::join('clause_articles', 'contract_clause_type.clause_article_id', 'clause_articles.id')->
        where('contract_clause_type.contract_id', $contract->id)->
        select('clause_articles.*', 'contract_clause_type.*')->
        get();
        //بند
        if (!$clause_article_list) {
            return [
                "result" => false,
                "error" => "بند های قرارداد جهت ثبت نامعتبر است .لطفا با پشتیبانی تماس بگیرید."
            ];
        }
        foreach ($clause_article_list as $item) {
            ContractRegisterClauseType::create([
                'contract_register_id' => $contract_register->id,
                'clause_article_id' => $item->clause_article_id,
                'priority_number' => $item->priority_number

            ]);
        }
        $keys = self::CreateKeys($employment);
        if (!$keys["result"]) {
            return [
                "result" => false,
                "error" => $keys["error"]
            ];
        }

        // ایجاد کلید-مقدار مورد نیاز برای ذخیره در جدول
        $data = [];
        foreach ($keys['keys'] as $keyword => $value) {
            // یافتن مطابقت کلید با اطلاعات مربوطه
            $contractKeyword = ContractKeyword::where('keyword', $keyword)->first();
            if ($contractKeyword) {
                // اضافه کردن اطلاعات مربوط به کلید به آرایه داده‌ها
                $data[] = [
                    'contract_register_id' => $contract_register->id,
                    'contract_keyword_id' => $contractKeyword->id,
                    'value' => $value
                ];
            }
        }
// ذخیره اطلاعات در جدول
        ContractRegisterToken::insert($data);

        //افزودن ایدی قراداد ثبت شده
        $employment->contract_register_id = $contract_register->id;
        $employment->save();
        return [
            "result" => true,
            "message" => "قرارداد با موفقیت ثبت شد."
        ];

    }
}
