<?php

namespace App\Http\Controllers\HR\Employment\Admin\Customer;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Http\Controllers\HR\Employment\Register\Customer\ConfirmDraftingContractController;
use App\Models\Accounting\Contract\Contract;
use App\Models\Accounting\Contract\ContractClauseType;
use App\Models\Accounting\Contract\ContractKeyword;
use App\Models\Accounting\Payment\PaymentMethodType;
use App\Models\HR\Employment\Employment;
use App\Models\customer\customer;
use App\Models\customer\customerAddress;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;

class DraftingContractFinalController extends Controller
{// این قسمت مرتبط با تایید نهایی قرارداد هوشمند مشتری می باشد
    public static $info = [
        "route" => "hr.employment.admin.customer.drafting_contract_final.",
        "enable_status" => ["125"],
        "button" => ["caption" => "  تایید نهایی پیش نویس قرارداد هوشمند (مشتری)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.customer.drafting_contract_final.",

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
//ایجادا ارایه برای توکن های مرتبط در مشتری
        $keys = ConfirmDraftingContractController::CreateKeysCustomer($employment);
        if (!$keys["result"]) {
            return back()->withErrors($keys["error"]);
        }
        //در صورتی که  قراردای که  ایجاد نشده بود باید خطا دهد که قراداد ایجاد نشده است
        $contract = Contract::where('contract_type_id', 2)->where('active_status_id', 1200)->first();
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
// نشان دادن اطلاعات برگ خروج و تایید های مشتری مشخصات فاکتور در پیش نویس قرارداد
        $payment_method_types = PaymentMethodType::get();
        $payment_method_min_percentage = PaymentMethodType::leftJoin("customer_payment_method", "payment_method_types.id", "payment_method_type_id")->
        where("customer_id", $employment->customer_id)->
        pluck("min_percentage", "payment_method_types.id")->toArray();

        $payment_method_max_percentage = PaymentMethodType::leftJoin("customer_payment_method", "payment_method_types.id", "payment_method_type_id")->
        where("customer_id", $employment->customer_id)->
        pluck("max_percentage", "payment_method_types.id")->toArray();

        $payment_method_max_check_delivery_time_in_days = PaymentMethodType::leftJoin("customer_payment_method", "payment_method_types.id", "payment_method_type_id")->
        where("customer_id", $employment->customer_id)->
        pluck("max_check_delivery_time_in_days", "payment_method_types.id")->toArray();

        return view($this->view_path . "index", compact("employment", 'contract',
            'payment_method_types', 'payment_method_max_percentage', 'payment_method_min_percentage', 'payment_method_max_check_delivery_time_in_days',
            'contract_clause_type_list', 'keys_id', 'article_list'));
    }

    public function submit(Employment $employment, Request $request)
    {
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $customer_draft_contract_confirm = Setting::getIntegerValue("customer_draft_contract_confirm");
        if ($customer_draft_contract_confirm) {

            Employment::SendSmsConfirmContract($employment, 'employmentconfirmcontract');
            $employment->status_id = 4640123;//در انتظار تایید قرارداد توسط مشتری
        } else {
            DraftingContractController::CreateCostCenter($employment);//ثبت مرکز هزینه
        }
        $employment->save();
        event(new EmploymentLogEvent($employment, 4640030));//تایید نهایی پیش نویس قراداد


        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "قرارداد هوشمند مشتری با موفقیت تنظیم گردید."]);

    }

    public function download_contract(Employment $employment)
    {

        $controller = new ConfirmDraftingContractController();
        return $controller->download($employment->key);
    }

    public function checkPermission(Employment $employment)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }
}
