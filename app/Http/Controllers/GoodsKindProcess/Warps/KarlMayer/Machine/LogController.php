<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer\Machine;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralLogController;
use App\Models\LineProduct\Machine\Machine;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public static $info = [
        "route"         => "warps.karl_mayer.machine.log.",
        "enable_status" => ["001","002","003","004","005","006","007","008","009","010","011","012" ],
        "button"        => [ "caption" => "مشاهده سابقه ماشین", "class" => "btn-info" ],
        "view_path"     => "goods_kind_process.warps.karl_mayer.machine.log.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.karl_mayer.machine.dashboard.";
    public function __construct() {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
    }

    public function index(Request $request, Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        $publicController = $this->getGeneralController();
        return $publicController->index($request, $machine);
    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, self::$info,true );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
    public function getGeneralController()
    {
        $publicController = new GeneralLogController();
        $publicController->route_path = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }
}
