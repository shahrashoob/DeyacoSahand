<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;

class BeginChangeWarpsForChangeDesignController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.machine.begin_change_warps_for_change_design.",
        "enable_status" => [ "028" ],
        "button"        => [ "caption" => "شروع تعویض چله (جهت تغییر کالیته)", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.begin_change_warps_for_change_design.",
        "message"       => [ "confirm" => "آیا از شروع تعویض چله اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginChangeWarpsForChangeDesignController::$info["route"];
        $this->view_path  = BeginChangeWarpsForChangeDesignController::$info["view_path"];
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
        ] )->where( "status_id","!=",  WarpsRequestForm::$perfix_status_code . "002" )->
        orderByDesc("id")->first();

        if(isset($warps_form)  ){
            return back()->withErrors("لطفا ابتدا چله را از انبار تحویل گرفته و فرم مربوطه را تایید نمایید.");
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 360;
//
        $machine->setStatus(
            null,
            53002,
            DashboardController::$perfix_production_status_code . "030",
            1650,
            "Fabric_Raw");

        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, BeginChangeWarpsForChangeDesignController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
