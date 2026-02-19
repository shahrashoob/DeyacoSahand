<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Production\ProductionForm;
use Illuminate\Http\Request;

class BeginCombingController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.begin_combing.",
        "enable_status" => [ "049" ],
        "button"        => [ "caption" => "شروع شانه کشی", "class" => "btn-primary" ],
        "message"       => [ "confirm" => "آیا از شروع شانه کشی اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginCombingController::$info["route"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation = $machine->getFirstReserveAllocation();
        if ( ! $allocation ) {
            return back()->withErrors( "تخصیص رزرو برای ماشین یافت نشد." );
        }

        $production_form_list = ProductionForm::
        where( "machine_id", $machine->id )->
        where( "status_id",7002001 )-> // در حال بافت پارچه
        get();

        foreach ($production_form_list as $production_form){
            foreach ($production_form->items as $item){
                if($allocation->id != $item->allocation_id){
                    return back()->withErrors( "لطفا غلطک پارچه خام را استخراج و ثبت نمایید." );
                }
            }
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 600;

        $machine->setStatus(
            null,
            null,
            7003050,// در  حال شانه کشی
            1800 );


        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, BeginCombingController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
