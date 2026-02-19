<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class ConfirmQualityControlController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.machine.confirm_quality_control.",
        "enable_status" => [ "015" ],
        "button"        => [ "caption" => "تایید کنترل کیفیت", "class" => "btn-success" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.confirm_quality_control.",
        "message"       => [ "confirm" => "آیا از تایید کنترل کیفیت اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = ConfirmQualityControlController::$info["route"];
        $this->view_path  = ConfirmQualityControlController::$info["view_path"];
    }


    public function submit( Request $request, Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 215;

        $design_form = FabricRawDesignForm::getDesignFormFromMachine( $machine );
        $last_row_log=null;
        if (
            $design_form->design_available ||
            ( ! $design_form->design_available && $design_form->warps_is_in_warehouse ) ) { // A=>T || (A=>F & C=>T)

            if ( ! isset( $request->contour_1_value ) ) {
                return redirect()->route( $this->route_path . "get_contour", $machine );
            }

            $last_row_log = MachineLog::getLastLogWithContour($machine);

            if (
                isset( $last_row_log ) &&
                ! $last_row_log->checkMinContour(
                    $request->contour_1_value,
                    $request->contour_2_value,
                    $request->contour_3_value,
                    $request->contour_4_value,
                    $request->contour_5_value )
            ) {
                return redirect()->route( $this->route_path . "get_contour", $machine )->withErrors( "مقدار قطب ها به درستی وارد نشده است" );
            }

            $machineLog->contour_1_value = $request->contour_1_value;
            $machineLog->contour_2_value = $request->contour_2_value;
            $machineLog->contour_3_value = $request->contour_3_value;
            $machineLog->contour_4_value = $request->contour_4_value;
            $machineLog->contour_5_value = $request->contour_5_value;
            $machineLog->shift_work_id   = $request->shift_work_id;

            $machine->on_status_id          = 53001;
            $machine->machine_off_reason_id = null;
            $machine->production_status_id  = DashboardController::$perfix_production_status_code . "016";
            $machine->save();

            // Change Production Card Status
            $perfix_status_production = \App\Http\Controllers\GoodsKindProcess\Fabric_Raw\Dobby\ProductionCard\DashboardController::$perfix_status_code;

            $allocation = $machine->getCurrentAllocation();
            foreach ( $allocation->items as $item ) {
                if ( $item->production->waiting_status_id == $perfix_status_production . "002" ) {

                    $item->production->waiting_status_id = $perfix_status_production . "003";
                    $item->production->save();
                    event( new ProductionCardLogEvent( $item->production ) );

                }
            }

        } else { // A=> F & C=>F

            // پیدا کردن کد چله
            $allocation = $machine->getCurrentAllocation();
         //   $warps_is_in_warehouse = Warps::warpsExistInWarehouse( $allocation );

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
            WarpsRequestForm::newRequest( $allocation, $machine, $warps_is_in_warehouse );

            $machine->save();

        }

        event( new MachineLogEvent( $machine, $machineLog,"",false,$last_row_log ) );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function get_contour( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        $shift_work_option = Option::get( "shift_work" );

        return view( $this->view_path . "get_contour", compact( "machine", "shift_work_option" ) );
    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, ConfirmQualityControlController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
