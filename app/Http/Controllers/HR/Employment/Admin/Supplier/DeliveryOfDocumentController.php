<?php

namespace  App\Http\Controllers\HR\Employment\Admin\Supplier;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Selection\SelectionSelector;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Post\PostDocumentType;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use App\Models\Utility\Document\DocumentType;
use App\Models\Worker;
use Illuminate\Http\Request;

/*
این کنترلر برای تحویل مدارک به باگانی است.
 * */

class DeliveryOfDocumentController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.supplier.delivery_of_document.",
        "enable_status" => ["128"],
        "button" => ["caption" => "تایید تحویل قراداد (تامین کننده)", "class" => "btn-primary"],
        "view_path" => "",
        "message" => ["confirm" => "آیا از تایید تحویل قراداد تامین کننده اطمینان دارید؟"],


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
        $employment->status_id = 4640106;//آغاز همکاری
        $employment->save();
        $employment->supplier->active_status_id=1200;
        $employment->supplier->save();

        event(new EmploymentLogEvent($employment, 4640034));//تایید تحوبل مدارک

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => " تایید تحویل قراداد با موفقیت ثبت گردید."]);

    }

    public function checkPermission(Employment $employment)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }

}
