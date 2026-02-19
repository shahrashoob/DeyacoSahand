<?php

namespace App\Http\Controllers\HR\Employment\Admin\Contractor;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Models\Accounting\Contract\Contract;
use App\Models\Accounting\Contract\ContractClauseType;
use App\Models\Accounting\Contract\ContractKeyword;
use App\Models\HR\Employment\Employment;
use App\Models\contractor\contractor;
use App\Models\contractor\contractorAddress;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;

class DraftingContractFinalController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.contractor.drafting_contract_final.",
        "enable_status" => ["131"],
        "button" => ["caption" => "تایید نهایی پیش نویس قرارداد هوشمند (پیمانکار)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.contractor.drafting_contract_final.",


    ];
    var $view_path;
    var $route_path;
    protected $dashboard_path = "hr.employment.admin.dashboard.";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
    }
    public function index(Employment $employment)
    {
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

//ایجادا ارایه برای توکن های مرتبط در پیمانکار
        $keys =  \App\Http\Controllers\HR\Employment\Register\Contractor\ConfirmDraftingContractController::CreateKeyscontractor($employment);
        if (!$keys["result"]) {
            return back()->withErrors($keys["error"]);
        }
        $contract = Contract::where('contract_type_id', 4)->where('active_status_id', 1200)->first();
        //بند
        if (!$contract) {
            return redirect()->back()->withErrors("قراردادی  برای  شما انتخاب نشده است. با دفتر کارگزینی تماس بگیرید.");
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

        return view($this->view_path . "index",compact("employment",'contract', 'contract_clause_type_list', 'keys_id', 'article_list'));
    }
    public function submit(Employment $employment, Request $request)
    {
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $contractor_draft_contract_confirm = Setting::getIntegerValue("contractor_draft_contract_confirm");
        if( $contractor_draft_contract_confirm){
            $employment->status_id = 4640132;//در انتظار تایید قرارداد توسط پیمانکار
            $employment->save();
            Employment::SendSmsConfirmContract( $employment, 'employmentconfirmcontract');
            event(new EmploymentLogEvent($employment, 4640037));//ثبت پیش نویس قرارداد نهایی هوشمند پیمانکار

        }else{
            event(new EmploymentLogEvent($employment, 4640037));//ثبت پیش نویس قرارداد هوشمند پیمانکار
            ConfirmDraftInformationController::CreateCostCenterContractor($employment);//ثبت مرکز هزینه
        }

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "  تایید قرارداد هوشمند پیمانکار با موفقیت تنظیم گردید."]);

    }

    public function checkPermission(Employment $employment)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }
}
