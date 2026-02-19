<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypeProperty;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\Post\Post;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;

use Illuminate\Http\Request;


class FailureToLaunchController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.failure_to_launch.",
        "enable_status" => [ "048", "054" ],
        "button"        => [ "caption" => "عدم راه اندازی شیفت", "class" => "btn-danger" ],
        "view_path"     => "goods_kind_process.fabric_raw.jacquard.machine.failure_to_launch.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = FailureToLaunchController::$info["route"];
        $this->view_path  = FailureToLaunchController::$info["view_path"];
    }

    public function index( Machine $machine ) {

        $shift_work_option = Option::get( "shift_work" );

        $packing_type_option = Option::get( "packing_type_from_output_band", 0, $machine->machine_type_id );
        $carrier_id          = session( "carrier_id" );

        return view( $this->view_path . "index", compact( "machine", "shift_work_option", "packing_type_option", "carrier_id" ) );

    }

    public function submit( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $checklist  = [
            290, //پایان تعویض چله
            530, // شروع استخراج چله و چله گذاری (جهت تغییر کالیته)
            570, // پایان تغییر کالیته
        ];
        $before_log = MachineLog::
        where( "machine_id", $machine->id, )->
        whereIn( "machine_event_type_id", $checklist )->
        orderByDesc( "id" )->
        first();
        if ( ! $before_log ) {
            return back()->withErrors( "پیک قبل از وضعیت راه اندازی شیف یافت نشد، لطفا با پشتیبانی تماس بگیرد." );
        }

        if ( $before_log->machine_event_type_id == 290 ) {
            return back()->withErrors( "ماژول عدم راه اندازی شیفت برای تعویض چله پیاده سازی نشده است." );
        }

        $no_doff_is_force = $request->no_doff_is_force;
        // چک کردن اینکه در ماژول پایان بافت خطایی نداشته باشیم
        $result = EndOfProductionCardTextureController::submitHasAnError( $request, $machine, $no_doff_is_force, false );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["error"] );
        }


        $allocation = $machine->getCurrentAllocation();
        $new_amount = 0;
        ChangeAllocationAmountController::ChangeAllocationAmount(  $allocation,$new_amount);

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 235;//عدم راه اندازی شیفت
        event( new MachineLogEvent( $machine, $machineLog, $request->description ) );

        // اجرا کردن یک پایان بافت کارت تولید برای کارت جاری
        EndOfProductionCardTextureController::submitConfirm( $request, $result );

        Allocation::updatePriorityNumber($machine);

        $message = $request->description . " بر روی ماشین راه اندازی نشد.";
        MachineModuleTypeProperty::SendSms( $message, $allocation, 70030021403 );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );


    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, FailureToLaunchController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
