<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class BeginWarpingController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.machine.begin_warping.",
        "enable_status" => [ "006" ],
        "button"        => [ "caption" => "شروع چله گذاری", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.begin_warping.",
        "message"       => [ "confirm" => "آیا از شروع چله گذاری اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginWarpingController::$info["route"];
        $this->view_path  = BeginWarpingController::$info["view_path"];
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

        $result = DashboardController::checkPermissionConditions( $machine, BeginWarpingController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
