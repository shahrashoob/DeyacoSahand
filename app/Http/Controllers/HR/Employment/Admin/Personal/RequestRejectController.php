<?php

namespace App\Http\Controllers\HR\Employment\Admin\Personal;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Models\HR\Employment\Employment;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use Illuminate\Http\Request;
/*
این کنترلر برای عدم تایید در خواست است
 * */
class RequestRejectController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.personal.request_reject.",
        "enable_status" => ["100","139","140","141","108","116"],
        "button" => ["caption" => " رد درخواست   ", "class" => " btn-danger"],
        "view_path" => " ",
        "message" => ["confirm" => "آیا از  عدم تایید درخواست اطمینان دارید؟"]


    ];
    var $view_path;
    var $route_path;
    protected $dashboard_path = "hr.employment.admin.dashboard.";


    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
    }

    public function submit(Employment $employment)
    {
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $employment->status_id = 4640105; //عدم تایید
        $employment->save();

        event(new EmploymentLogEvent($employment, 4640023));
        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "عدم تایید درخواست با موفقیت ثبت گردید."]);

    }

    public function checkPermission(Employment $employment)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }
}
