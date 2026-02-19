<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\SpecialProduction\Machine;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function view;

class LogController extends Controller
{
    public static $info = [
        "route"         => "fabric.special_production.machine.log.",
        "enable_status" => ["001","002","003" ],
        "button"        => [ "caption" => "مشاهده سابقه ماشین", "class" => "btn-info" ],
        "view_path"     => "goods_kind_process.fabric.special_production.machine.log.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric.special_production.machine.dashboard.";
    public function __construct() {
        $this->route_path = LogController::$info["route"];
        $this->view_path = LogController::$info["view_path"];
    }

    public function index( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $list=MachineLog::where("machine_id",$machine->id)->orderByDesc("id")->paginate(50);

        return view( $this->view_path . "index", compact(  "machine" ,"list") );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, LogController::$info,true );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
