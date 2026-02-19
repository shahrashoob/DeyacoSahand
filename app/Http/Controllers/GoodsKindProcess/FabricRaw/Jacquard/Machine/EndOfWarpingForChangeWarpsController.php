<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class EndOfWarpingForChangeWarpsController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.end_of_warping_for_change_warps.",
        "enable_status" => [ "025" ],
        "button"        => [ "caption" => "پایان گره زنی تعویض چله", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.jacquard.fabric_raw.machine.end_of_warping_for_change_warps.",
        "message"       => [ "confirm" => "آیا از پایان گره زنی تعویض چله اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfWarpingForChangeWarpsController::$info["route"];
        $this->view_path  = EndOfWarpingForChangeWarpsController::$info["view_path"];
    }

    public function submit( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 310;
//
        $machine->setStatus(
            null,
            null,
            DashboardController::$perfix_production_status_code . "013",
            1611 );

        event( new MachineLogEvent( $machine, $machineLog ) );



        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfWarpingForChangeWarpsController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
