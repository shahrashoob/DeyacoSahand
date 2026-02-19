<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class BeginWarpingForChangeDesignController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.begin_warping_for_change_design.",
        "enable_status" => [ "031" ],
        "button"        => [ "caption" => "شروع گره زنی (تغییر کالیته)", "class" => "btn-primary" ],
        "message"       => [ "confirm" => "آیا از شروع گره زنی جهت تغییر کالیته اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginWarpingForChangeDesignController::$info["route"];
    }

    public function submit( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 370;

//
        $machine->setStatus(
            null,
            null,
            7003032,
            1670 );

        event( new MachineLogEvent( $machine, $machineLog ) );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, BeginWarpingForChangeDesignController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
