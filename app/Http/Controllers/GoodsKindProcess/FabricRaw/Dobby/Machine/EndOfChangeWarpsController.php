<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class EndOfChangeWarpsController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.machine.end_of_change_warps.",
        "enable_status" => [ "023" ],
        "button"        => [ "caption" => "پایان تعویض چله", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.end_of_change_warps.",
        "message"       => [ "confirm" => "آیا از پایان تعویض چله اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfChangeWarpsController::$info["route"];
        $this->view_path  = EndOfChangeWarpsController::$info["view_path"];
    }

    public function submit( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        // تغییر وضعیت حامل چله قبلی به خالی
    //  $result= WarpsRequestForm::setEmptyBeforeWarpsCarrier($machine,1);
        if(!$result["result"]){
            return back()->withErrors($result["message"]);
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 290;
//
        $machine->setStatus(
            null,
            null,
            DashboardController::$perfix_production_status_code . "024",
            1619 );

        event( new MachineLogEvent( $machine, $machineLog ) );

        // ورودی ها چله بروز می شوند
        Warps::updateCarrierInCurrentInputOutputBand1($machine);

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfChangeWarpsController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
