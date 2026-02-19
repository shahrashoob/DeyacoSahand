<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class EndOfChangeYarnForStopOrderController extends Controller {

    public static $info = [
        "route"         => "fabric_raw.machine.end_of_change_yarn_for_stop_order.",
        "enable_status" => [ "037" ],
        "button"        => [ "caption" => "پایان تغییر نخ پود (دستور توقف)", "class" => "btn-warning" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.end_of_change_yarn_for_stop_order.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfChangeYarnForStopOrderController::$info["route"];
        $this->view_path  = EndOfChangeYarnForStopOrderController::$info["view_path"];
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

        $shift_work_option = Option::get( "shift_work" );

        return view( $this->view_path . "index", compact( "machine", "shift_work_option" ) );
    }

    public function submit( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        $allocation = $machine->getCurrentAllocation();

        // دریافت قطب ها
        $last_row_log = MachineLog::where( [
            "machine_id" => $machine->id,
        ] )->
        whereNotNull( "contour_1_value" )->
        orderByDesc( "id" )->first();

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


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 430;
        $machineLog->contour_1_value       = $request->contour_1_value;
        $machineLog->contour_2_value       = $request->contour_2_value;
        $machineLog->contour_3_value       = $request->contour_3_value;
        $machineLog->contour_4_value       = $request->contour_4_value;
        $machineLog->contour_5_value       = $request->contour_5_value;
        $machineLog->shift_work_id         = $request->shift_work_id;

        $machine->setStatus(
            null,
            53002,
            7003038,
            1710,
            "Fabric_Raw"
        );

        event( new MachineLogEvent( $machine, $machineLog ) );

        // تغییر وضعیت فرم تولید
        ProductionForm::ChangeStatusFromTo(
            $allocation,
            7002009, // در حال تغییر نخ پود (دستور توقف)
            7002010 // در انتظار استخراج پارچه پایانی دستور توقف
        );


        // کارت تولید رزرو به کارت تولید اصلی تبدیل می شود.
        foreach ( $allocation->items as $item ) {
            $item->production_id         = $item->reserve_production_id;
            $item->reserve_production_id = null;
            $item->save();
        }

        return redirect()->route( $this->route_path . "confirmation", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );


    }

    public function confirmation( Machine $machine ) {
        return view( $this->view_path . "confirmation", compact( "machine" ) );
    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfChangeYarnForStopOrderController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
