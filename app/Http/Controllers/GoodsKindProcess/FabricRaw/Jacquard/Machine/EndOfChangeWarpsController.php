<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialReturnToWarehouseController;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineInputLog;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;

class EndOfChangeWarpsController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.end_of_change_warps.",
        "enable_status" => [ "023" ],
        "button"        => [ "caption" => "پایان تعویض چله", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.jacquard.machine.end_of_change_warps.",
        // "message"       => [ "confirm" => "آیا از پایان تعویض چله اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfChangeWarpsController::$info["route"];
        $this->view_path  = EndOfChangeWarpsController::$info["view_path"];
    }

    public function index( Machine $machine ) {


        return view( $this->view_path . "index", compact( "machine", ) );

    }

    public function submit( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


// دریافت قطب ها
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

        // در تعویض چله یک کانال تولید مشابه کانال تولید جاری ماشین داریم که بعد از آن رزرو است،
        // کانال تولید جاری را تولید شده و کانال رزرو را به جاری تبدیل می کنیم.
        $current_production_channel = $machine->getCurrentProductionChannel();
        if ( ! $current_production_channel ) {
            return back()->withErrors( "کانال تولید جاری برای ماشین، وجود ندارد، لطفا با پشتیبانی تماس بگیرید." );
        }

        $reserve_production_channel = $machine->ReserveProductionChannel()->first();
        if ( ! $reserve_production_channel ) {
            return back()->withErrors( "کانال تولید رزور برای ماشین، وجود ندارد، لطفا با پشتیبانی تماس بگیرید." );
        }
        if (
            $current_production_channel->production_channel_type_id
            !=
            $reserve_production_channel->production_channel_type_id
        ) {
            return back()->withErrors( "نوع کانال تولید جاری و کانال تولید رزرو با هم متفاوت است، لطفا با پشتیبانی تماس بگیرید." );
        }

        $current_production_channel->status_id=3358003;// تولید شده
        $current_production_channel->save();

        $reserve_production_channel->status_id=3358001;// کانال جاری
        $reserve_production_channel->save();

//
//        // تغییر وضعیت حامل چله قبلی به خالی
//        $result = WarpsRequestForm::setEmptyBeforeWarpsCarrier( $machine, 1 );
//        if ( ! $result["result"] ) {
//            return back()->withErrors( $result["message"] );
//        }

        // لاگ ماشین

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 290;
        $machineLog->contour_1_value       = $request->contour_1_value * $contour_result["ratio"];
        $machineLog->contour_2_value       = $request->contour_2_value * $contour_result["ratio"];
        $machineLog->contour_3_value       = $request->contour_3_value * $contour_result["ratio"];
        $machineLog->contour_4_value       = $request->contour_4_value * $contour_result["ratio"];
        $machineLog->contour_5_value       = $request->contour_5_value * $contour_result["ratio"];

//
        $machine->setStatus(
            null,
            null,
            DashboardController::$perfix_production_status_code . "024",
            1619 );

        event( new MachineLogEvent( $machine, $machineLog ) );

        // ظرفیت کانال تولید را به روز می کنیم.
        Warps::updateCarrierInCurrentInputOutputBand( $machine, "updateProductionChannel" );


        $production_form = $machine->getCurrentProductionForm();
        if ( $production_form ) {
            // بروزرسانی مقادیر فرم تولید
            $last_machine_log = MachineLog::getLastLogWithContour( $machine );
            ProductionForm::UpdateAmountWithLastContour( $production_form, $last_machine_log );
        }

        $allocation = $machine->getCurrentAllocation();
        // ثبت مقدار مصرف
        MachineAllocationMaterialConsumed::registerNewConsumed( $allocation, $machine, $last_row_log, $machineLog );



        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfChangeWarpsController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
