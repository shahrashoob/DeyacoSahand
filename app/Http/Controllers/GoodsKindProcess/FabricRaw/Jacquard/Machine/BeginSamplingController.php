<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class BeginSamplingController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.begin_sampling.",
        "enable_status" => [ "053" ],
        "button"        => [ "caption" => "شروع نمونه گیری", "class" => "btn-primary" ],
        "message"       => [ "confirm" => "آیا از شروع نمونه گیری اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginSamplingController::$info["route"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 680;



        $machine->setStatus(
            null,
            53001,
            7003054,
            null );


        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, BeginSamplingController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
