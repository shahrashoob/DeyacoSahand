<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class RejectQualityControlKnottingController extends Controller {
    public static $info = [
        "route"                    => "fabric_raw.machine.reject_quality_control_knotting.",
        "enable_status"            => [ "015" ],
        "button"                   => [ "caption" => "عدم تایید کنترل ( گره زنی)", "class" => "btn-danger" ],
        "view_path"                => "goods_kind_process.fabric_raw.machine.reject_quality_control_knotting.",
        "message"                  => [ "confirm" => "آیا از عدم تایید کنترل اطمینان دارید؟" ],
        "enable_special_condition" => 1
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = RejectQualityControlKnottingController::$info["route"];
        $this->view_path  = RejectQualityControlKnottingController::$info["view_path"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 220;


        $machine->on_status_id          = 53002;
        $machine->machine_off_reason_id = 1607;
        $machine->production_status_id  = DashboardController::$perfix_production_status_code . "010";


        $machine->save();


        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, RejectQualityControlKnottingController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
