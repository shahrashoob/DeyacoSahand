<?php

namespace App\Http\Controllers\HR\Employment\Admin\Personal;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentSelection;
use App\Models\HR\Selection\SelectionPostSetting;
use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
/*
این کنترلر برای ثبت هماهنگی گزینش می باشد.
 * */

class CoordinationSelectionController extends Controller
{

    public static $info = [
        "route" => "hr.employment.admin.personal.coordination_selection.",
        "enable_status" => ["102"],
        "button" => ["caption" => "هماهنگی گزینش (پرسنل)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.personal.coordination_selection.",

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
        $employment_selections = EmploymentSelection::
        where("employment_id", $employment->id)->
        whereIn("status_id", [4640102, 4640101])-> //  شروع نشده ودر انتظار هماهنگی
        orderBy("priority_number")->
        orderBy("id")->
        get();

        if (!$employment_selections) {
            return back()->withErrors("اطلاعات گزینش مورد نظر یافت نشد، لطفا با پشتیبانی تماس بگرید.");
        }

        $current_employment_selection = EmploymentSelection::
        where("employment_id", $employment->id)->
        where("priority_number", $employment->current_priority_number)->
        where("status_id", 4640102)-> // در انتظار هماهنگی
        orderByDesc("priority_number")->
        orderBy("id")->
        first();

        $worker=Worker::find(Auth::id());

        return view($this->view_path . "index", compact('employment', 'employment_selections', "current_employment_selection","worker"));


    }

    public function submit(Employment $employment, Request $request)
    {
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $employment_selections = $employment->employment_selections()->
        whereIn("status_id", [4640102, 4640101])-> //  شروع نشده ودر انتظار هماهنگی
        get();

        if (count($employment_selections) == 0) {
            return redirect()->route($this->route_path, "index", $employment)->withErrors("اطلاعات گزینش مورد نظر یافت نشد، لطفا با پشتیبانی تماس بگرید.");
        }

        $current_employment_selection = EmploymentSelection::
        where("employment_id", $employment->id)->
        where("priority_number", $employment->current_priority_number)->
        where("status_id", 4640102)-> // در انتظار هماهنگی
        orderBy("priority_number")->
        orderBy("id")->
        first();

//        foreach ($employment_selections as $item) {
//            if (empty($request->input("coordination_time_" . $item->id))) {
//                return back()->withErrors("لطفا تاریخ و ساعت خود را به طور کامل تکمیل نمایید.");
//            }
//        }

        if (empty($request->input("coordination_time_" . $current_employment_selection->id))) {
            return back()->withErrors("لطفا ساعت و تاریخ " . $current_employment_selection->selection->caption . " را انتخاب نمایید.");
        }

        $address = Setting::getStringValue("company_address");
        $company_location = Setting::getStringValue("company_location");
        $company_name = Setting::getStringValue("company_name");

        foreach ($employment_selections as $item) {

            //در یافت زمان گزینش تغییر وضعیت گزینش به در انتظار انجام
            if (!empty($request->input("coordination_time_" . $item->id))) {
                $item->coordination_time = $request->input("coordination_time_" . $item->id);

                if ($item->id == $current_employment_selection->id) {
                    $item->status_id = 4640103; // در انتظار انجام
                }

                $item->save();
                //تغیر وضعیت همکاری به در انتطار انجام
                Employment::SendSmsNextStatusForPost($employment);
                $employment->status_id = 4640103;
                $employment->save();
                event(new EmploymentLogEvent($employment, 4640003, $request->input("text_" . $item->id), $item->id));

                $token10 = $employment->worker->fullname("with_gender_2");
                $token20 = "انجام " . $item->selection->caption;
                $token2 = $item->get_coordination_time_date() . "ساعت" . $item->get_coordination_time_time() . "به آدرس : " . $address;
                $token = $company_name;
                $token3 = $company_location;

                Notification::send("00" . ($employment->mobile_country->area_code ?? "98") . $employment->mobile,
                    new SMSNotification("employmentconfirmselection",
                        $token,
                        $token2,
                        $token3,
                        $token10,
                        $token20));
            }

        }

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "هماهنگی زمان گزینش (ها) با موفقیت ثبت گردید."]);

    }


    public function checkPermission(Employment $employment)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }
}
