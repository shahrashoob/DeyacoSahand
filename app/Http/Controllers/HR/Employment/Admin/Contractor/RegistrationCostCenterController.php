<?php

namespace App\Http\Controllers\HR\Employment\Admin\contractor;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Models\HR\Employment\Employment;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;

class RegistrationCostCenterController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.contractor.registration_cost_center.",
        "enable_status" => ["133"],
        "button" => ["caption" => "ثبت مرکز تامین (پیمانکار)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.contractor.registration_cost_center.",

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

        $cost_center_option = Option::get("cost_center");
        $company_have_separate_warehousing_software = Setting::getIntegerValue("company_have_separate_warehousing_software");
        $company_have_separate_financial_software = Setting::getIntegerValue("company_have_separate_financial_software");


        return view($this->view_path . "index", compact('cost_center_option', "employment", 'company_have_separate_financial_software',
            'company_have_separate_warehousing_software'
        ));
    }


    public function submit(Employment $employment, Request $request)
    {
        $contractor = $employment->contractor;
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $company_have_separate_warehousing_software = Setting::getIntegerValue("company_have_separate_warehousing_software");
        $company_have_separate_financial_software = Setting::getIntegerValue("company_have_separate_financial_software");// نرم افزار مالی
        if ($company_have_separate_warehousing_software) {
            $contractor->cost_center_id = $request->cost_center_id;
        }
        if ($company_have_separate_financial_software) {// در صورتی که نرم افزار حسابداری داشته باشیم
            $contractor->detailed_code = $request->detailed_code;
        }
        $contractor->save();

        Employment::AddUserToPost($employment);
        $contractor_draft_contract_confirm = Setting::getIntegerValue("contractor_draft_contract_confirm");
        $contractor_draft_contract_required_init_confirm = Setting::getIntegerValue("contractor_draft_contract_required_init_confirm");
        $contractor_draft_contract_required_final_confirm = Setting::getIntegerValue("contractor_draft_contract_required_final_confirm");


        if ($contractor_draft_contract_confirm &&($contractor_draft_contract_required_init_confirm || $contractor_draft_contract_required_final_confirm )) {
            Employment::SendSmsNextStatusForPost($employment);
            $employment->status_id = 4640134;//در انتظار تحویل مدارک به بایگانی
        }else{
            $employment->status_id = 4640106;//آغاز همکاری
            $employment->contractor->active_status_id=1200;
            $employment->contractor->save();
        }
        $employment->save();

        event(new EmploymentLogEvent($employment, 4640038));//ثبت مرکز هزینه


        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => " مرکز تامین با موفقیت تنظیم گردید."]);

    }

    public function checkPermission(Employment $employment)
    {

        $result = \App\Http\Controllers\HR\Employment\Admin\DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }
}
