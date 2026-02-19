<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class BeginWarpingForChangeWarpsController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.begin_warping_for_change_warps.",
        "enable_status" => [ "024" ],
        "button"        => [ "caption" => "شروع گره زنی تعویض چله", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.jacquard.machine.begin_warping_for_change_warps.",
        "message"       => [ "confirm" => "آیا از شروع گره زنی تعویض چله اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginWarpingForChangeWarpsController::$info["route"];
        $this->view_path  = BeginWarpingForChangeWarpsController::$info["view_path"];
    }

    public function submit( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 300;
//
        $machine->setStatus(
            null,
            null,
            DashboardController::$perfix_production_status_code . "025",
            1620 );

        event( new MachineLogEvent( $machine, $machineLog ) );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, BeginWarpingForChangeWarpsController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
