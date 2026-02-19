<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class EndOfIntroChangeLotController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.machine.end_of_intro_change_lot.",
        "enable_status" => [ "003" ],
        "button"        => [ "caption" => "پایان مقدمات تغییر کالیته", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.end_of_intro_change_lot.",
        "message"=>["confirm"=>"آیا از پایان مقدمات تغییر کالیته اطمینان دارید؟"],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginIntroChangeLotController::$info["route"];
        $this->view_path  = BeginIntroChangeLotController::$info["view_path"];
    }

    public function submit(Machine $machine){
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        $design_form=FabricRawDesignForm::getDesignFormFromMachine($machine);
        if(!isset($design_form)){
            return back()->withErrors("فرم طراحی مربوطه یافت نشد.");
        }
        $machineLog                        = new MachineLog();
        // پایان یافته
        if($design_form->status_id == FabricRawDesignForm::$perfix_status_code."004" ){

            $machineLog->machine_event_type_id = 110;

            $machine->on_status_id          = 53002;
            $machine->machine_off_reason_id = 1602;
            $machine->production_status_id  = DashboardController::$perfix_production_status_code . "004";
            $machine->save();

            event( new MachineLogEvent( $machine, $machineLog ) );

        }
        // پایان یافته نیست
        else{

            $machineLog->machine_event_type_id = 110;

            $machine->on_status_id          = 53002;
            $machine->machine_off_reason_id = 1603;
            $machine->production_status_id  = DashboardController::$perfix_production_status_code . "020";
           $machine->save();

            event( new MachineLogEvent( $machine, $machineLog ) );
        }


        return redirect()->route($this->dashboard_route."view",compact("machine"))->with(["success"=>"عملیات با موفقیت انجام شد."]);

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine ,EndOfIntroChangeLotController::$info);
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

}
