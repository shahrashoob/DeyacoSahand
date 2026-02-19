<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class EndOfPinningController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.end_of_pinning.",
        "enable_status" => [ "018" ],
        "button"        => [ "caption" => "پایان لامل ریزی", "class" => "btn-primary" ],
        "message"       => [ "confirm" => "آیا از پایان لامل ریزی اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfPinningController::$info["route"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 190;

        // آیا طراحی عوض شده
        $allocation = $machine->getFirstReserveAllocation();
        if ( ! $allocation ) {
            return back()->withErrors( "تخصیص رزرو برای ماشین یافت نشد." );
        }

        if ( $allocation->has_design_change ) {
            $machine->setStatus(
                null,
                null,
                7003049,// در انتظار شروع شانه کشی
                1790 );
        } else {

            $machine->setStatus(
                null,
                null,
                7003043,//در انتظار شروع تغییر کالیته
                1770 );
        }

        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfPinningController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
