<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use Illuminate\Http\Request;

class EndOfChangeMachineBarController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.end_of_change_machine_bar.",
        "enable_status" => [ "052" ],
        "button"        => [ "caption" => "پایان تغییر عرض ماشین", "class" => "btn-primary" ],
        "message"       => [ "confirm" => "آیا از پایان تغییر عرض ماشین اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfChangeMachineBarController::$info["route"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 630;

        $machine->setStatus(
            null,
            null,
            7003043,// در  انتظار شروع تغییر کالیته
            1770 );


        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfChangeMachineBarController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
