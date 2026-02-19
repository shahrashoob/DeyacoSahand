<?php

namespace App\Http\Controllers\HR\Employment\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Models\Accounting\Payment\PaymentMethodType;
use App\Models\HR\Employment\Employment;
use Illuminate\Http\Request;

class ConfirmDraftInformationController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.customer.confirm_draft_information.",
        "enable_status" => ["136"],
        "button" => ["caption" => "تایید پیش نویس اطلاعات (مشتری)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.customer.confirm_draft_information.",
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

        return view($this->view_path . "index", compact("employment",'payment_method_types','payment_method_min_percentage'
            ,'payment_method_max_percentage','payment_method_max_check_delivery_time_in_days'));
    }

    public function submit(Employment $employment)
    {
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        Employment::NextStatus($employment);


        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => " تایید پیش نویس اطلاعات با موفقیت ثبت گردید."]);

    }
    public function checkPermission(Employment $employment)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }
}
