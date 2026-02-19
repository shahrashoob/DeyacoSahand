<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class EndOfChangeWarpsForChangeDesignController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.machine.end_of_change_warps_for_change_design.",
        "enable_status" => [ "030" ],
        "button"        => [ "caption" => "پایان تعویض چله (جهت تغییر کالیته)", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.end_of_change_warps_for_change_design.",
        "message"       => [ "confirm" => "آیا از پایان تعویض چله اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfChangeWarpsForChangeDesignController::$info["route"];
        $this->view_path  = EndOfChangeWarpsForChangeDesignController::$info["view_path"];
    }

    public function submit( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 380;
//
        $machine->setStatus(
            null,
            null,
            7003031,
            1660 );


        event( new MachineLogEvent( $machine, $machineLog ) );


        // ورودی ها چله بروز می شوند
        Warps::updateCarrierInCurrentInputOutputBand1($machine);

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfChangeWarpsForChangeDesignController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
