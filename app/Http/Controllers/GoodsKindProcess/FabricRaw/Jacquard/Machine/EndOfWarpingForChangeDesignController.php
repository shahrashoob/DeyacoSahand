<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use Illuminate\Http\Request;


class EndOfWarpingForChangeDesignController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.end_of_warping_for_change_design.",
        "enable_status" => [ "032" ],
        "button"        => [ "caption" => "پایان گره زنی (تغییر کالیته)", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.jacquard.machine.end_of_warping_for_change_design.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfWarpingForChangeDesignController::$info["route"];
        $this->view_path  = EndOfWarpingForChangeDesignController::$info["view_path"];
    }

    public function index( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        return view( $this->view_path . "index", compact( "machine" ) );
    }

    public function submit( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 390;

        // آیا لامل ریزی دارد؟
        if ( $request->has_pining == 1 ) {
            $machine->setStatus(
                null,
                null,
                7003014,
                1609 );
        } else if ( $request->has_pining == -1 ) {

            // آیا طراحی عوض شده
            $allocation = $machine->getFirstReserveAllocation();
            if(!$allocation){
                return back()->withErrors("تخصیص رزرو برای ماشین یافت نشد.");
            }

            if($allocation->has_design_change){
                $machine->setStatus(
                    null,
                    null,
                    7003049,// در انتظار شروع شانه کشی
                    1790 );
            }
            else{
                $machine->setStatus(
                    null,
                    null,
                    7003043, // در انتظار شروع تغییر کالیته
                    1770 );
            }



        } else {
            return back()->withErrors( "لطفا به سوال پاسخ مناسب بدهید." );
        }

//


        event( new MachineLogEvent( $machine, $machineLog ) );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfWarpingForChangeDesignController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
