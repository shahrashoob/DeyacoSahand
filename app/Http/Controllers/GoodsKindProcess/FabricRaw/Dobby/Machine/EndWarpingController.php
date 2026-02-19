<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class EndWarpingController extends Controller
{
    //
    public static $info = [
        "route"         => "fabric_raw.machine.end_warping.",
        "enable_status" => [ "009" ],
        "button"        => [ "caption" => "پایان چله گذاری", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.end_warping.",
        "message"       => [ "confirm" => "آیا از پایان چله گذاری اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndWarpingController::$info["route"];
        $this->view_path  = EndWarpingController::$info["view_path"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 150;

        $machine->setStatus(
            null,
            53002,
            DashboardController::$perfix_production_status_code . "010",
            1607,
            "Fabric_Raw"
        );

        event( new MachineLogEvent( $machine, $machineLog ) );

        // تولید لات پارچه
        $allocation= $machine->getCurrentAllocation();
        FabricRaw:: ChangeLot( $allocation );

        // ورودی ها چله بروز می شوند
        Warps::updateCarrierInCurrentInputOutputBand1($machine);

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndWarpingController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
