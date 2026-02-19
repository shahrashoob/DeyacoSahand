<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use Illuminate\Http\Request;

class EndOfCombingController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.end_of_combing.",
        "enable_status" => [ "050" ],
        "button"        => [ "caption" => "پایان شانه کشی", "class" => "btn-primary" ],
        "message"       => [ "confirm" => "آیا از پایان شانه کشی اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfCombingController::$info["route"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 610;

        // آیا عرض ماشین عوض شده
        $allocation = $machine->getFirstReserveAllocation();
        if ( ! $allocation ) {
            return back()->withErrors( "تخصیص رزرو برای ماشین یافت نشد." );
        }

        if ( $allocation->has_bar_fabric_change ) {
            $machine->setStatus(
                null,
                null,
                7003051,// در انتظار شروع تغییر عرض
                1810 );
        } else {

            $machine->setStatus(
                null,
                null,
                7003043,
                1770 );
        }


        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfCombingController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
