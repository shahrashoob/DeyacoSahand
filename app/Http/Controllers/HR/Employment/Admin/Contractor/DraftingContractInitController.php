<?php

namespace App\Http\Controllers\HR\Employment\Admin\Contractor;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\contractor\Definition\DashboardController;
use App\Models\Accounting\Contract\Contract;
use App\Models\Accounting\Contract\ContractClauseType;
use App\Models\Accounting\Contract\ContractKeyword;
use App\Models\HR\Employment\Employment;
use App\Models\contractor\contractor;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;

class DraftingContractInitController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.contractor.drafting_contract_init.",
        "enable_status" => ["130"],
        "button" => ["caption" => " تایید اولیه پیش نویس قرارداد (پیمانکار)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.contractor.drafting_contract_init.",
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
//ایجادا ارایه برای توکن های مرتبط در پیمامکار
        $keys = \App\Http\Controllers\HR\Employment\Register\Contractor\ConfirmDraftingContractController::CreateKeysContractor($employment);
        if (!$keys["result"]) {
            return back()->withErrors($keys["error"]);
        }
        $contract = Contract::where('contract_type_id', 4)->where('active_status_id', 1200)->first();
        //بند
        if (!$contract) {
            return redirect()->back()->withErrors("قراردادی  برای  پیمانکار انتخاب نشده است. با دفتر کارگزینی تماس بگیرید.");
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

        return view($this->view_path . "index", compact("employment", 'contract', 'contract_clause_type_list', 'keys_id', 'article_list'));
    }

    public function submit(Employment $employment, Request $request)
    {
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        Employment::NextStatus($employment);

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => " تایید قرارداد هوشمند پیمانکار با موفقیت تنظیم گردید."]);

    }

    public function checkPermission(Employment $employment)
    {

        $result = \App\Http\Controllers\HR\Employment\Admin\DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }
}
