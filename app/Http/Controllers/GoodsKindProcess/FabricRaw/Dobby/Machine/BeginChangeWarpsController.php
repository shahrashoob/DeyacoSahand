<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;

class BeginChangeWarpsController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.machine.begin_change_warps.",
        "enable_status" => [ "022" ],
        "button"        => [ "caption" => "شروع تعویض چله", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.begin_change_warps.",
        "message"       => [ "confirm" => "آیا از شروع تعویض چله اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginChangeWarpsController::$info["route"];
        $this->view_path  = BeginChangeWarpsController::$info["view_path"];
    }

    public function submit( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation=$machine->getCurrentAllocation();

        $warps_form = WarpsRequestForm::where( [
            "machine_id"    => $machine->id,
            "allocation_id"    => $allocation->id
        ] )->whereNotIn( "status_id",[  7005002,7005006])->
        orderByDesc("id")->first();

        if(isset($warps_form)  ){
            return back()->withErrors("لطفا ابتدا چله را از انبار تحویل گرفته و فرم مربوطه را تایید نمایید.");
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 280;
//
        $machine->setStatus(
            null,
            53002,
            DashboardController::$perfix_production_status_code . "023",
            1618 );

        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, BeginChangeWarpsController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
