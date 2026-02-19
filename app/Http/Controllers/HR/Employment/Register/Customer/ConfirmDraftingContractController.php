<?php

namespace App\Http\Controllers\HR\Employment\Register\Customer;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Accounting\Contract\PrintController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Register\ConfirmMobileController;
use App\Models\Accounting\Contract\Contract;
use App\Models\Accounting\Contract\ContractClauseType;
use App\Models\Accounting\Contract\ContractKeyword;
use App\Models\Accounting\Contract\ContractRegister;
use App\Models\Accounting\Contract\ContractRegisterClauseType;
use App\Models\Accounting\Contract\ContractRegisterToken;
use App\Models\Accounting\CostCenter;
use App\Models\HR\Employment\Employment;
use App\Models\Post\PostUser;
use App\Models\Utility\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConfirmDraftingContractController extends Controller
{// این مرتبط به تایید قرارداد در همکاری با ما مرتبط با توسط مشتری می باشد
    protected $view_path = "hr.employment.register.customer.confirm_drafting_contract.";
    protected $route_path = "hr.employment.register.customer.confirm_drafting_contract.";


    public function index($key)
    {
        $employment = Employment::where("key", $key)->
        where("status_id", 4640123)-> //در انتظار تایدد قرارد دارد پیش نویس
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $result = ConfirmMobileController::CheckEmploymentKey($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $keys = self::CreateKeysCustomer($employment);
        if (!$keys["result"]) {
            return back()->withErrors($keys["error"]);
        }
        $contract = Contract::where('contract_type_id', 2)->where('active_status_id', 1200)->first();//قرارداد مشتری
        //بند
        if (!$contract) {
            return redirect()->back()->withErrors("قرارداد مشتریان مشخص نشده است، لطفا با پشتیبانی تماس بگیرید.");
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
            if (isset($keys['keys'][$item->keyword])) {
                $keys_id[$item->id] = $keys['keys'][$item->keyword];
            } else {
                $keys_id[$item->id] = "...............";
            }
        }
        return view($this->view_path . "index", compact('employment', 'contract', 'contract_clause_type_list', 'keys_id', 'article_list'));

    }

    public function submit($key, Request $request)
    {

        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640123])-> //در انتظار تایدد قرارد دارد پیش نویس
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $result = ConfirmMobileController::CheckEmploymentKey($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $contract = Contract::where('contract_type_id', 2)->first();//قرارداد مشتری
        if (!$contract) {
            return redirect()->back()->withErrors("قراردادی  برای  شما انتخاب نشده است. با دفتر کارگزینی تماس بگیرید.");
        }

        //ذخیره قراداد های تایید شده
        $result_contract=self::CreateContractRegister($employment, $contract);
        if (!$result_contract["result"]) {
            return back()->withErrors($result_contract["error"]);
        }
        $company_have_separate_financial_software = Setting::getIntegerValue("company_have_separate_financial_software");
        $company_have_separate_warehousing_software = Setting::getIntegerValue("company_have_separate_warehousing_software");
        $customer_draft_contract_confirm = Setting::getIntegerValue("customer_draft_contract_confirm");
        $customer_draft_contract_required_init_confirm = Setting::getIntegerValue("customer_draft_contract_required_init_confirm");
        $customer_draft_contract_required_final_confirm = Setting::getIntegerValue("customer_draft_contract_required_final_confirm");
        $customer = $employment->customer;

        //آیا شرکت نرم افزار مالی مجزا دارد؟مرکز هزینه را طبق این ایجاد می کنیم
        if ($company_have_separate_financial_software || $company_have_separate_warehousing_software) {
            $employment->status_id = 4640126; // در انتظار ثبت مرکزمشتری
            event(new EmploymentLogEvent($employment, 4640032, null, null, $employment->user_id));//تایید قرارداد توسط
        }
        if (!$company_have_separate_warehousing_software) {// نرم افزار انبار داری مرتبط با کد هزینه می باشد.
            self::CreateCostCenter($employment);
        }
        if (!$company_have_separate_financial_software && !$company_have_separate_warehousing_software) {// در صورتی که هیچ کدام وجود نداشته باشد هم مرکر هزینه ایجاد می شود
            self::CreateCostCenter($employment);
            Employment::AddUserToPost($employment);
            //در صورتی که قراداد داشت می تواند تحویل دهد.
            if ($customer_draft_contract_confirm && ($customer_draft_contract_required_init_confirm || $customer_draft_contract_required_final_confirm)) {
                $employment->status_id = 4640127;//در انتظار تحویل مدارک به بایگانی

            } else {
                $employment->status_id = 4640106;//آغاز همکاری

            }
            event(new EmploymentLogEvent($employment, 4640031, null, null, $employment->user_id));//ثبت مرکز هزینه

        }
        $employment->save();
        return redirect()->route("login")->with(["success" =>
            " قرارداد با موفقیت تایید شد. <br/>اقدامات بعدی برای شما از طریق پیامک ارسال خواهد شد." .
            "<br/>" . ""]);

    }

    //تابع نمایش پی دی اف
    public function print($key)
    {

        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640123])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $contract = Contract::where('contract_type_id', 2)->first();
        //بند
        if (!$contract) {
            return redirect()->back()->withErrors("قراردادی  برای  شما انتخاب نشده است. با دفتر کارگزینی تماس بگیرید.");
        }
        $keys = self::CreateKeysCustomer($employment);
        if (!$keys["result"]) {
            return back()->withErrors($keys["error"]);
        }
        $keys = $keys["keys"];
        PrintController::CreatePdfFile($contract, $employment, "قراداد" . $employment->worker->fullname(), $keys);

    }
    public function download($key)
    {

        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640123])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $contract = Contract::where('contract_type_id', 2)->first();
        //بند
        if (!$contract) {
            return redirect()->back()->withErrors("قراردادی  برای  شما انتخاب نشده است. با دفتر کارگزینی تماس بگیرید.");
        }
        $keys = self::CreateKeysCustomer($employment);
        if (!$keys) {
            return back()->withErrors($keys["error"]);
        }
        PrintController::CreatePdfFile($contract, $employment, "قراداد" . $employment->worker->fullname(), $keys["keys"]);

    }

    public static function CreateContractRegister($employment, $contract)
    {
        $contract_register = ContractRegister::create([
            'contract_id' => $contract->id,
        ]);
        $code = "DCCT/" . (1000 + $contract_register->id);;
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
        $keys = self::CreateKeysCustomer($employment);
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

    public static function CreateKeysCustomer($employment)
    {
        $company_name_setting = Setting::getStringValue("company_name");
        $company_address_setting = Setting::getStringValue("company_address");
        $national_code_setting = Setting:: getIntegerValue("national_code");
        $post_id_for_contract_setting = Setting:: getIntegerValue("post_id_for_contract");
        $post_user = PostUser::where('post_id', $post_id_for_contract_setting)->first();
        if (!$post_user) {
            return [
                "result" => false,
                "error" => "تنظیمات کد پست سازمانی جهت عقد قرارداد نامعتبر است."
            ];
        }
        $address_B=$employment->personal_type_id == 1?$employment->worker->user_address()->first()->address:
                    $employment->company->user_address()->first()->address;

        $startTime = Carbon::parse($employment->customer->start_date_of_contract);
        $endTime = Carbon::parse($employment->customer->end_date_of_contract);

        $contractPeriodMonths = $endTime->diffInMonths($startTime);
        $keys = [
            "Name_B" => $employment->worker->fullname(),
            "FullName_B" => $employment->fullname(),
            "Father_Name_B" => $employment->worker->father_name,
            "National_Code_B" => $employment->national_code,
            "National_Code_Full_B" => ($employment->personal_type_id == 1 ? "کد ملی " : "شناسه ملی ") . $employment->national_code,
            "Address_B" => $address_B->address,
            "Date_B" => $employment->worker->get_date_of_birth(),
            "Mobile_B" => ($employment->personal_type_id == 1 ?
                ("0" . $employment->worker->user_address()->first()->address->mobile . " - " .
                    $employment->worker->user_address()->first()->address->phone) :
                ("0" . $employment->company->user_address()->first()->address->mobile . " - " .
                    $employment->company->user_address()->first()->address->phone)
            ),

            "Address_A" => $company_address_setting,

            "Right_Work" => $employment->worker->right_to_work,
            "Company_name_A" => $company_name_setting,
            "FullName_A" => $company_name_setting . "(" . $post_user->worker->fullname("with_gender_2") . ")",

            "Start_Date" => $employment->customer->get_start_date_of_contract(),
            "End_Date" => $employment->customer->get_end_date_of_contract(),
            "ContractPeriod" => $contractPeriodMonths . " ماه شمسی ",

            "Birth_Certificate_Number_B" => $employment->worker->birth_certificate_number,
            "Company_National_Code_A" => $national_code_setting,
            "Representative_A" => $post_user->worker->fullname("with_gender_2"),
            "Post_A" => $post_user->post->caption,
            "Duties_A" => $employment->worker->right_to_work,

            "GuaranteeAmount" => $employment->customer->bail_amount,

        ];
        return [
            "result" => true,
            "keys" => $keys
        ];
    }

    public static function CreateCostCenter($employment)
    {
        $customer = $employment->customer;
        $cost_center = CostCenter::create([
            "code" => 0,
            "caption" => $employment->customer->caption,
            "status_id" => 1200,
        ]);
        $cost_center->code = "001" . "/" . $cost_center->id;
        $cost_center->save();
        $customer->cost_center_id = $cost_center->id;
        $customer->code = $cost_center->code;
        $customer->save();
    }
}
