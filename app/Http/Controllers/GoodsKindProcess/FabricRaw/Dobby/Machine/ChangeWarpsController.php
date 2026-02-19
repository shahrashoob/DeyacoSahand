<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Fabric_Raw\Machine\WarpsRequestForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Utility\Option;
use function back;
use function event;
use function redirect;
use function view;

class ChangeWarpsController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.machine.chane_warps.",
        "enable_status" => [ "016","021" ],
        "button"        => [ "caption" => "تعویض چله", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.chane_warps.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = ChangeWarpsController::$info["route"];
        $this->view_path  = ChangeWarpsController::$info["view_path"];
    }


    public function index( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $shift_work_option = Option::get( "shift_work" );

        return view( $this->view_path . "index", compact( "machine", "shift_work_option", "lot_list" ) );
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation=$machine->getCurrentAllocation();
        $warps_form=WarpsRequestForm::where(["machine_id"=>$machine->id,"allocation_id"=>$allocation->id])->first();
        if(!isset($warps_form) || $warps_form->status_id != "7005"."002" ){
            return back()->withErrors("لطفا ابتدا چله را از انبار تحویل گرفته و فرم مربوطه را تایید نمایید.");
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 130;

        $machine->on_status_id          = 53002;
        $machine->machine_off_reason_id = 1606;
        $machine->production_status_id  = DashboardController::$perfix_production_status_code . "009";
        $machine->save();

        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, ChangeWarpsController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
