<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class BeginChangeYarnForStopOrderController extends Controller {

    public static $info = [
        "route"         => "fabric_raw.machine.begin_change_yarn_for_stop_order.",
        "enable_status" => [ "036" ],
        "button"        => [ "caption" => "شروع تغییر نخ پود (دستور توقف کارت تولید)", "class" => "btn-warning" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.begin_change_yarn_for_stop_order.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginChangeYarnForStopOrderController::$info["route"];
        $this->view_path  = BeginChangeYarnForStopOrderController::$info["view_path"];
    }

    public function index( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        if($machine->maintenance_status_id != 6002001){

            $machineLog                        = new MachineLog();
            $machineLog->machine_event_type_id = 400;

            $machine->setStatus(
                null,
                53002,
                null,
                1690, // عملیات تعمیرات و نگهداری
                "Fabric_Raw"

            );
            event( new MachineLogEvent( $machine, $machineLog ) );
            return back()->withErrors( "به علت عملیات تعمیرات و نگهداری امکان راه اندازی ماشین وجود ندارد، لطفا به واحد فنی مراجعه فرمایید." );

        }

        $allocation               = $machine->getCurrentAllocation();
        $machine_input_share_band = CurrentMachineInput::
        where( [
            "allocation_id" => $allocation->id,
            "goods_kind_id" => 2 // نخ
        ] )->get();


        $shift_work_option = Option::get( "shift_work" );

        return view( $this->view_path . "index", compact( "machine", "machine_input_share_band", "shift_work_option" ) );
    }

    public function submit( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        $allocation               = $machine->getCurrentAllocation();
        $machine_input_share_band = CurrentMachineInput::
        where( [
            "allocation_id" => $allocation->id,
            "goods_kind_id" => 2 // نخ
        ] )->get();

        // چک کردن اینکه لات ها با لات های قبلی یکسان نباشد.
        $i        = 1;
        $is_equal = true;
        foreach ( $machine_input_share_band as $item ) {
            $lot_code = "lot_" . $item->id . "_code";
            if ( ! isset( $request->$lot_code ) ) {
                return back()->withErrors( "همه همبافت های مورد نیاز وارد نشده است." );
            }
            $lot_number = LotNumber::firstOrCreate( [
                "product_id" => $item->material_id,
                "code"       => $request->$lot_code
            ] );
            if ( $item->lot_number_id != $lot_number->id ) {
                $is_equal = false;
            }
        }

        if ( $is_equal ) {
            return redirect()->route( $this->dashboard_route . "view", $machine )->withErrors( "لات های نخ  وارد شده با لات های قبلی برابر هستند، تنها در صورتی که لات نخ تغییر کرد، اقدام نمایید." );
        }


// دریافت قطب ها
        $last_row_log =MachineLog::getLastLogWithContour($machine);

        if (
            isset( $last_row_log ) &&
            ! $last_row_log->checkMinContour(
                $request->contour_1_value,
                $request->contour_2_value,
                $request->contour_3_value,
                $request->contour_4_value,
                $request->contour_5_value )
        ) {
            return back()->withErrors( "مقدار قطب ها به درستی وارد نشده است" );
        }



        //  (ورودی اشتراکی ) دریافت لات نخ
        $message_lot = "همبافت (لات) وارد شده: ";
        foreach ( $machine_input_share_band as $item ) {

            $lot_code = "lot_" . $item->id . "_code";
            if ( ! isset( $request->$lot_code ) ) {
                return back()->withErrors( "همه همبافت های مورد نیاز وارد نشده است." );
            }
            $lot_number          = LotNumber::firstOrCreate( [
                "product_id" => $item->material_id,
                "code"       => $request->$lot_code
            ] );
            $item->lot_number_id = $lot_number->id;
            $item->save();
            $message_lot .= $request->$lot_code . " - ";
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 420;
        $machineLog->contour_1_value       = $request->contour_1_value;
        $machineLog->contour_2_value       = $request->contour_2_value;
        $machineLog->contour_3_value       = $request->contour_3_value;
        $machineLog->contour_4_value       = $request->contour_4_value;
        $machineLog->contour_5_value       = $request->contour_5_value;
        $machineLog->shift_work_id         = $request->shift_work_id;

        $machine->setStatus(
            null,
            53001,
            7003037, // در حال تغیر نخ پود (دستور توقف)
            null,
            "Fabric_Raw"
        );

        event( new MachineLogEvent( $machine, $machineLog, $message_lot,false,$last_row_log  ) );

        // تغییر وضعیت فرم تولید
        ProductionForm::ChangeStatusFromTo(
            $allocation,
            7002001, // در حال تکمیل
            7002009 // در حال تغییر نخ پود (دستور توقف)
        );

        return redirect()->route( $this->route_path . "confirmation", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );


    }

    public function confirmation( Machine $machine ) {
        return view( $this->view_path . "confirmation", compact( "machine" ) );
    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, BeginChangeYarnForStopOrderController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
