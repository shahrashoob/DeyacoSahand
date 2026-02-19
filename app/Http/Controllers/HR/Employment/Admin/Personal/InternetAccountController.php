<?php

namespace App\Http\Controllers\HR\Employment\Admin\Personal;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Models\HR\Employment\Employment;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

/*
این کنترلر برای ثبت اکانت اینترنت است
 * */

class InternetAccountController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.personal.internet_account.",
        "enable_status" => ["120"],
        "button" => ["caption" => "تعریف حساب اینترنت (پرسنل)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.personal.internet_account.",


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


        return view($this->view_path . "index", compact('employment'));

    }

    public function submit(Employment $employment, Request $request)
    {
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $employment->worker->internet_account_username = $request->internet_account_username;
        $employment->worker->save();

        self::InternetAccount($employment,$request);//اسمس اینترنت
        $employment->status_id = 4640106;//آغاز همکاری
        $employment->save();
        Employment::SendSmsNextStatusForPost($employment);
        event(new EmploymentLogEvent($employment, 4640027));
        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "حساب اینترنت با موفقیت ثبت گردید."]);
    }

    public function checkPermission(Employment $employment)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }

    public static function InternetAccount(Employment $employment, $request)
    {
        switch ($employment->cooperation_type_id) {
            case 1:
            case 11:
                //پیامک دریافت اکانت
                $token10 = $employment->worker->fullname("with_gender_2");
                $token20 = Setting::getStringValue('company_name');
                $token = $employment->worker->internet_account_username;
                $token2 = $request->password;


                Notification::send("00" . ($employment->mobile_country->area_code ?? "98") . $employment->mobile,
                    new SMSNotification("employmentinternetaccount",
                        $token,
                        $token2,
                        null,
                        $token10,
                        $token20,
                    ));
                break;
        }
    }
}
