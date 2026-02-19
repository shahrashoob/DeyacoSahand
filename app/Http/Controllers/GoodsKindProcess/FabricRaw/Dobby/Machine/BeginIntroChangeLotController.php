<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class BeginIntroChangeLotController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.machine.begin_intro_change_lot.",
        "enable_status" => [ "001" ],
        "button"        => [ "caption" => "شروع مقدمات تغییر کالیته", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.begin_intro_change_lot.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginIntroChangeLotController::$info["route"];
        $this->view_path  = BeginIntroChangeLotController::$info["view_path"];
    }

    public function index( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        };

        $shift_work_option = Option::get( "shift_work" );

        return view( $this->view_path . "index", compact( "machine", "shift_work_option" ) );

    }

    public function submit( Request $request, Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        };

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
        $machineLog->machine_event_type_id = 100;
        $machineLog->contour_1_value       = $request->contour_1_value;
        $machineLog->contour_2_value       = $request->contour_2_value;
        $machineLog->contour_3_value       = $request->contour_3_value;
        $machineLog->contour_4_value       = $request->contour_4_value;
        $machineLog->contour_5_value       = $request->contour_5_value;
        $machineLog->shift_work_id         = $request->shift_work_id;

        $machine->on_status_id          = 53002;
        $machine->machine_off_reason_id = 1601;
        $machine->production_status_id  = DashboardController::$perfix_production_status_code . "003";
        $machine->save();

        foreach ($machine->getCurrentAllocation()->items as $item){
            $item->production_id = $item->reserve_production_id;
            $item->reserve_production_id = null;
            $item->save();
        }

        event( new MachineLogEvent( $machine, $machineLog,"",false,$last_row_log ) );

        return redirect()->route( $this->dashboard_route . "view", $machine )->with(["success"=>"عملیات با موفقیت انجام شد."]);
    }

    public function checkPermission( Machine $machine ) {


        $result = DashboardController::checkPermissionConditions( $machine,BeginIntroChangeLotController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }


}
