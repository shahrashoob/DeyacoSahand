<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class BeginPinningController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.machine.begin_pinning.",
        "enable_status" => [ "014" ],
        "button"        => [ "caption" => "شروع لامل ریزی", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.begin_pinning.",
        "message"       => [ "confirm" => "آیا از شروع لامل ریزی اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginPinningController::$info["route"];
        $this->view_path  = BeginPinningController::$info["view_path"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 180;



                $machine->on_status_id          = 53002;
                $machine->machine_off_reason_id = 1612;
                $machine->production_status_id  = DashboardController::$perfix_production_status_code . "018";
                $machine->save();


        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, BeginPinningController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
