<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\Matthys\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class StartWarpingController extends Controller {
    public static $info = [
        "route"         => "warps.matthys.machine.start_warping.",
        "enable_status" => [ "004" ],
        "button"        => [ "caption" => "شروع چله کشی", "class" => "btn-primary" ],
        "message"       => [ "confirm" => "آیا شروع چله گذاری اطمینان دارید" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.matthys.machine.dashboard.";

    public function __construct() {
        $this->route_path = StartWarpingController::$info["route"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();
        if ( ! $allocation ) {
            return back()->withErrors("تخصیص جاری برای ماشین وجود ندارد، لطفا با مسئول مربوطه تماس بگیرید.");
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 1020;
        $machineLog->save();


        $machine->setStatus(
            null,
            53001, // روشتن
            7203005, // در حال چله کشی
            null );

        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, StartWarpingController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
