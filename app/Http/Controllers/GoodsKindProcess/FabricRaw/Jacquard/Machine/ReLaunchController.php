<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\Post\PostStatus;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;

use Illuminate\Http\Request;

class ReLaunchController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.re_launch.",
        "enable_status" => [ "048" ],
        "button"        => [ "caption" => "راه اندازی مجدد شیفت", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.jacquard.machine.re_launch.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = ReLaunchController::$info["route"];
        $this->view_path  = ReLaunchController::$info["view_path"];
    }

    public function index( Machine $machine ) {

//return   $allowed_status_ids = PostStatus::getAllowedStatus();
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        $shift_work_option = Option::get( "shift_work" );

        return view( $this->view_path . "index", compact( "machine", "shift_work_option" ) );

    }

    public function submit( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $message      = "";
        $last_row_log = MachineLog::getLastLogWithContour( $machine );

        $contour_result = $last_row_log->checkMinContour(
            $request->contour_1_value,
            $request->contour_2_value,
            $request->contour_3_value,
            $request->contour_4_value,
            $request->contour_5_value );
        if (
            isset( $last_row_log ) && ! $contour_result["result"]
        ) {
            $message .= $contour_result["error"];
        }

        if ( $message != "" ) {
            return back()->withErrors( $message );
        }


        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 238; // راه اندازی مجدد
        $machineLog->contour_1_value       = $request->contour_1_value * $contour_result["ratio"];
        $machineLog->contour_2_value       = $request->contour_2_value * $contour_result["ratio"];
        $machineLog->contour_3_value       = $request->contour_3_value * $contour_result["ratio"];
        $machineLog->contour_4_value       = $request->contour_4_value * $contour_result["ratio"];
        $machineLog->contour_5_value       = $request->contour_5_value * $contour_result["ratio"];
        $machineLog->shift_work_id         = $request->shift_work_id;

        $machine->production_status_id  = DashboardController::$perfix_production_status_code . "013";
        $machine->save();
        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, ReLaunchController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
