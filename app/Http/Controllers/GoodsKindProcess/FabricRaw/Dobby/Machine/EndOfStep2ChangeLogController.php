<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class EndOfStep2ChangeLogController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.machine.end_of_step2_change_lot.",
        "enable_status" => [ "004" ],
        "button"        => [ "caption" => "پایان مرحله دوم تغییر کالیته", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.end_of_step2_change_lot.",
        "message"       => [ "confirm" => "آیا از پایان مرحله دوم تغییر کالیته اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginIntroChangeLotController::$info["route"];
        $this->view_path  = BeginIntroChangeLotController::$info["view_path"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        $design_form                       = FabricRawDesignForm::getDesignFormFromMachine( $machine );
        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 120;

        if ( ! isset( $design_form->design_available ) ) {
            return back()->withErrors( "به سوال 'آیا طراحی موجود است' پاسخ داده نشده است." );
        }
//return $design_form;
        if ( $design_form->design_available ==false ) {// A question

            $machine->setStatus(
                null,
                53002,
                7003039,
                1720,
                "Fabric_Raw"
            );

            event( new MachineLogEvent( $machine, $machineLog ) );

        } else {

            $allocation=$machine->getCurrentAllocation();
            // پیدا کردن کد چله
           // $warps_is_in_warehouse=Warps::warpsExistInWarehouse($allocation);

            if ( $warps_is_in_warehouse ) {
                // چله در انبار هست
                $machine->on_status_id          = 53002;
                $machine->machine_off_reason_id = 1605;
                $machine->production_status_id  = DashboardController::$perfix_production_status_code . "006";

            } else {
                // چله در انبار نیست
                $machine->on_status_id          = 53002;
                $machine->machine_off_reason_id = 1614;
                $machine->production_status_id  = DashboardController::$perfix_production_status_code . "019";
            }

            // ران شدن ماژول چله
            WarpsRequestForm::newRequest( $allocation,$machine, $warps_is_in_warehouse );

            $machine->save();
            event( new MachineLogEvent( $machine, $machineLog ) );

        }


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {
        $dc = new DashboardController();

        $result = $dc->checkPermissionConditions( $machine, EndOfStep2ChangeLogController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
