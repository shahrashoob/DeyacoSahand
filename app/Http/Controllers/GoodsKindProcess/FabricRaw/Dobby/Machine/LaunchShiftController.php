<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class LaunchShiftController extends Controller {
    public static $info = [
        "route"                    => "fabric_raw.machine.launch_shift.",
        "enable_status"            => [ "013" ],
        "button"                   => [ "caption" => "راه اندازی شیفت", "class" => "btn-primary" ],
        "view_path"                => "goods_kind_process.fabric_raw.machine.launch_shift.",
        "enable_special_condition" => 1
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = LaunchShiftController::$info["route"];
        $this->view_path  = LaunchShiftController::$info["view_path"];
    }

    public function index( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $shift_work_option = Option::get( "shift_work" );
        return view( $this->view_path . "index", compact( "machine", "shift_work_option" ) );

    }

    public function submit(Request $request, Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


// دریافت قطب ها
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
            return back()->withErrors( "مقدار قطب ها به درستی وارد نشده است" );
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 230;
        $machineLog->contour_1_value       = $request->contour_1_value;
        $machineLog->contour_2_value       = $request->contour_2_value;
        $machineLog->contour_3_value       = $request->contour_3_value;
        $machineLog->contour_4_value       = $request->contour_4_value;
        $machineLog->contour_5_value       = $request->contour_5_value;
        $machineLog->shift_work_id         = $request->shift_work_id;

        $machine->on_status_id          = 53001;
        $machine->machine_off_reason_id = null;
        $machine->production_status_id  = DashboardController::$perfix_production_status_code . "016";
        $machine->save();
        event( new MachineLogEvent( $machine, $machineLog,"",false,$last_row_log ) );

        // تولید لات پارچه
        $allocation= $machine->getCurrentAllocation();
        FabricRaw:: ChangeLot( $allocation );


        // Change Production Card Status
        $perfix_status_production = \App\Http\Controllers\GoodsKindProcess\Fabric_Raw\Dobby\ProductionCard\DashboardController::$perfix_status_code;
        $allocation               = $machine->getCurrentAllocation();
        foreach ( $allocation->items as $item ) {
            if ( $item->production->waiting_status_id == $perfix_status_production . "002" ) {
                $item->production->waiting_status_id = $perfix_status_production . "003";
                $item->production->save();
                event( new ProductionCardLogEvent( $machine->production ) );
            }
        }

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, LaunchShiftController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
