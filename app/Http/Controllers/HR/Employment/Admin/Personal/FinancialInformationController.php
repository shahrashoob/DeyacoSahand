<?php

namespace App\Http\Controllers\HR\Employment\Admin\Personal;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Models\Accounting\CostCenter;
use App\Models\HR\Employment\Employment;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;

/*
این کنترلر برای ثبت اطلاعات مالی است
 * */

class FinancialInformationController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.personal.financial_information.",
        "enable_status" => ["119"],
        "button" => ["caption" => "ثبت اطلاعات مالی(پرسنل)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.personal.financial_information.",


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
        return view($this->view_path . "index", compact('employment','cost_center_option','company_have_separate_warehousing_software',
        'company_have_separate_financial_software'));

    }

    public function submit(Employment $employment, Request $request)
    {
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
//        // بررسی تکراری بودن کد
//        if (CostCenter::where('code', $request->code)->exists()) {
//            return back()->withErrors("کدمرکز هزینه تکراری می باشد.");
//        }
//        $cost_center = CostCenter::create([
//            'code' => $request->code,
//            'status_id' => 1200,//فعال
//            'caption' => $employment->worker->fullname() . " " . "(" . $employment->national_code . ")",
//        ]);
//        $employment->worker->detailed_code = $request->detailed_code;//کد تفضیلی
//        $employment->worker->cost_center_id = $cost_center->id;//مرکز هزینه
//        $employment->worker->save();
        $company_have_separate_warehousing_software = Setting::getIntegerValue("company_have_separate_warehousing_software");// نرم افزار انبار داری
        $company_have_separate_financial_software = Setting::getIntegerValue("company_have_separate_financial_software");// نرم افزار مالی

        if ($company_have_separate_warehousing_software) {// در صورتی که نرم افزار انبارداری داشته باشد
            $employment->worker->cost_center_id = $request->cost_center_id;
        }
        if ($company_have_separate_financial_software) {// در صورتی که نرم افزار حسابداری داشته باشیم
            $employment->worker->detailed_code = $request->detailed_code;
        }
        $employment->worker->save();

        $company_have_it_unit = Setting::getIntegerValue("company_have_it_unit");// واحد آیتی

        if($company_have_it_unit) {
            $employment->status_id = 4640120;//آغاز همکاری در انتظار دریافت اکانت اینترنت
        }
        else{
            $employment->status_id = 4640106; // آغاز همکاری
        }

        $employment->save();
        Employment::SendSmsNextStatusForPost($employment);
        event(new EmploymentLogEvent($employment, 4640026));
        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "اطلاعات مالی با موفقیت ثبت گردید."]);
    }

    public function checkPermission(Employment $employment)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }

}
