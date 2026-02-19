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

class EndOfKnottingController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.machine.end_of_knotting.",
        "enable_status" => [ "011" ],
        "button"        => [ "caption" => "پایان گره زنی", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.end_of_knotting.",
        "message"       => [ "confirm" => "آیا از پایان گره زنی اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfKnottingController::$info["route"];
        $this->view_path  = EndOfKnottingController::$info["view_path"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 170;

        $design_form=FabricRawDesignForm::getDesignFormFromMachine($machine);

        if($design_form->design_available){ // A=>T

            if($design_form->it_has_pinning){ // B => T

                $machine->on_status_id          = 53002;
                $machine->machine_off_reason_id = 1610;
                $machine->production_status_id  = DashboardController::$perfix_production_status_code . "012";
                $machine->save();
            }else{
                // B =>F
                $machine->setStatus(
                    null,
                    53002,
                    7003039,
                    1720,
                    "Fabric_Raw"
                );
            }
        }else{
            //A => F
            $machine->on_status_id          = 53002;
            $machine->machine_off_reason_id = 1611;
            $machine->production_status_id  = DashboardController::$perfix_production_status_code . "013";
            $machine->save();
        }



        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfKnottingController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
