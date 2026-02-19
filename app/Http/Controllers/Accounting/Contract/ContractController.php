<?php

namespace App\Http\Controllers\Accounting\Contract;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Contract\ClauseArticle;
use App\Models\Accounting\Contract\ClauseType;
use App\Models\Accounting\Contract\Contract;
use App\Models\Accounting\Contract\ContractClauseType;
use App\Models\Accounting\Contract\ContractKeyword;
use App\Models\Accounting\Contract\ContractRegisterClauseType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

//این کنترلر مرتبط با قراداد می باشد.
class ContractController extends Controller
{
    private $view_path = "accounting.contract.contract.";
    private $route_path = "accounting.contract.contract.";

    public function index()
    {

        $list = Contract::paginate(50);


        return view($this->view_path . "index", compact('list'));
    }

    public function create()
    {
        $contract_types_option = Option::get("contract_types");
        return view($this->view_path . "create", compact('contract_types_option'));
    }

    public function store(Request $request)
    {

        $existing_contract = Contract::whereIn('contract_type_id', [2, 3, 4])->where('contract_type_id', $request->contract_type_id)->first();

        if ($existing_contract) {
            return back()->withErrors("قرارداد با این نوع تکراری می باشد و نمی‌توان بیشتر از یکبار تعریف کرد.");
        }


        $exist = Contract::where('caption', $request->caption)->exists();
        if ($exist) {
            return back()->withErrors("عنوان قرارداد تکراری می باشد.");
        }
        Contract::create([
            'contract_type_id' => $request->contract_type_id,
            "caption" => $request->caption,
            "number_of_contract" => $request->number_of_contract,
            "description" => $request->description ?? "",
            "guide"=>$request->guide??"",
        ]);

        return redirect()->route($this->route_path . "index")->with(["success" => "یک قراداد با موفقیت اضافه شد."]);
    }

    public function edit(Contract $contract)
    {
        $existing_contract = ContractClauseType::where([
            "contract_id" => $contract->id,
        ])->get();
        $active_status_option = Option::get("active_status");
        return view($this->view_path . "edit", compact('contract', 'active_status_option', 'existing_contract'));
    }

    public function update(Contract $contract, Request $request)
    {
        $exist = Contract::where('caption', $request->caption)->where('id', '!=', $contract->id)->exists();
        if ($exist) {
            return back()->withErrors("عنوان قرارداد تکراری می باشد.");
        }
        $existing_contract = ContractClauseType::where([
            "contract_id" => $contract->id,
        ])->get();

        if (count($existing_contract) > 0) {
            $contract->active_status_id = $request->active_status_id;
        }
        $contract->description = $request->description;
        $contract->caption = $request->caption;
        $contract->number_of_contract = $request->number_of_contract;
        $contract->guide = $request->guide;
        $contract->save();
        return redirect()->route($this->route_path . "edit", $contract)->with(["success" => "اطلاعات با موفقیت ذخیره شد."]);
    }

//افزودن بند به قراداد
    public function add_clause(Contract $contract, $clause_type_id = 0)
    {
        $contract_clause_types = ContractClauseType::where('contract_id', $contract->id)
            ->orderBy('priority_number')
            ->get();

        $clause_types_option = Option::get("clause_types", $clause_type_id);
        $clause_article_list = ClauseArticle::where('clause_type_id', $clause_type_id)->get();

        return view($this->view_path . "add_clause", compact('contract', 'clause_types_option', 'contract_clause_types', 'clause_article_list'));
    }

    public function store_clause(Contract $contract, Request $request)
    {

        if (empty($request->clause_article_id)) {
            return back()->withErrors("لطفا حداقل یک بند را انتخاب نمایید");
        }
        $existing_priority_number = ContractClauseType::where([
            "contract_id" => $contract->id,
            "priority_number" => $request->priority_number,

        ])->exists();

        if ($existing_priority_number) {
            return back()->withErrors(" اولویت برای این قراداد قبلاً ثبت شده است.");
        }
        $existing_contract_clause = ContractClauseType::where([
            "contract_id" => $contract->id,
            'clause_type_id' => $request->clause_type_id,
        ])->
        whereIn('clause_article_id', array_keys($request->clause_article_id))
            ->get();

        if (count($existing_contract_clause) > 0) {
            return back()->withErrors("بند انتخاب شده برای این ماده در قراداد قبلاً ثبت شده است.");
        }

        $clause_article_list = $request->clause_article_id;
        if (!empty($clause_article_list)) {
            foreach ($clause_article_list as $clause_article_id => $value) {

                ContractClauseType::create([
                    'clause_type_id' => $request->clause_type_id,
                    "priority_number" => $request->priority_number,
                    "contract_id" => $contract->id,
                    'clause_article_id' => $clause_article_id,

                ]);

            }
        }


        return redirect()->route($this->route_path . "add_clause", $contract)->with(["success" => "یک ماده برای قراداد  " . $contract->caption . " با موفقیت اضافه شد."]);

    }

    public function destroy_contract_clause_type(Contract $contract, ContractClauseType $contract_clause_type)
    {
        if ($contract_clause_type->contract_id != $contract->id) {
            return back()->withErrors("اطلاعات بند قراداد جهت حذف نامعتبر است.");
        }

        $contract_clause_type->delete();

        $existing_contract = ContractClauseType::where([
            "contract_id" => $contract->id,
        ])->get();

        if (count($existing_contract) == 0) {
            $contract->active_status_id = 1210;
            $contract->save();
        }


        return redirect()->route($this->route_path . 'add_clause', $contract)->with(["success" => "یک  بند قرارداد با موفقیت حذف گردید."]);
    }


    public function print(Contract $contract)
    {
        $keys = [
            "Name_A" => "*نام و نام خانوادگی(طرف اول)*",
            "Name_B" => "*نام و نام خانوادگی(طرف دوم)*",
            "Father_Name_A" => "*فرزند(طرف اول)*",
            "Father_Name_B" => "*فرزند(طرف دوم)*",
            "National_Code_A" => "*شماره ملی(طرف اول)*",
            "National_Code_B" => "*شماره ملی(طرف دوم)*",
            "Address_A" => "*نشانی(طرف اول)*",
            "Address_B" => "*نشانی(طرف دوم)*",
            "Date_A" => "*تاریخ تولد(طرف اول)*",
            "Date_B" => "*تاریخ تولد(طرف دوم)*",
            "Mobile_A" => "*شماره همراه(طرف اول)*",
            "Mobile_B" => "*شماره همراه(طرف دوم)*",
            "Salary_Date" => "*مزد روزانه(طرف اول)*",
            "Salary_Month" => "*حقوق ماهانه*",
            "Right_Work" => "*حق شاغل*",
            "Number_Of_Contract" => "*تعداد نسخه قرارداد*",
            "Start_Date" => "*تاریخ شروع قرارداد*",
            "End_Date" => "*تاریخ پایان قرارداد*",
            "Company_name_A" => "*نام شرکت(طرف اول)*",
            "Company_name_B" => "*نام شرکت(طرف دوم)*",
            "Birth_Certificate_Number_A" => "*شماره شناسنامه(طرف اول)*",
            "Birth_Certificate_Number_B" => "*شماره شناسنامه(طرف دوم)*",
            "Academic_Degree_A" => "*میزان تحصیلات*",
            "Company_National_Code_A" => "*شناسه ملی(طرف اول)*",
            "Company_National_Code_B" => "*شناسه ملی(طرف دوم)*",
            "Representative_A" => "*به نمایندگی(طرف اول)*",
            "Representative_B" => "*به نمایندگی(طرف دوم)*",
            "Post_A" => "*پست سازمانی(طرف اول)*",
            "Post_B" => "*پست سازمانی(طرف دوم)*",
            "Duties_A" => "*کار یا حرفه یا حجم کار یا وظیفه ای که کارگر به آن اشتغال می یابد*",
            "Account_Number" => "*شماره حساب*",
            "Bank_Branch" => "*شعبه بانک*",
            "Bank_Name" => "*نام بانک*",
            "Number_Of_Days_Before_Termination_Of_Contract" => "*مدت زمان اعلام فسخ قرارداد*",
            "Representation_A" => "*به نمایندگی(طرف اول)*",
            "Representation_B" => "*به نمایندگی(طرف دوم)*",
        ];


        PrintController::PrintContract($contract, 'contract', $keys);
    }


}
