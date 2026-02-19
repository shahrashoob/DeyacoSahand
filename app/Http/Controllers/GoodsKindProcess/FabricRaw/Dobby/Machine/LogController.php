<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function view;

class LogController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.machine.log.",
        "enable_status" => ["001","002","003","004","005","006","007","008","009","010","011","012","013","014","015","016","017","018","019","020","021","022","023","024","025","026","027","028","029","030","031","032","033","034","035","036","037","038","039","040","041","042" ],
        "button"        => [ "caption" => "مشاهده سابقه ماشین", "class" => "btn-info" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.log.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";
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

        $result = DashboardController::checkPermissionConditions( $machine, LogController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
