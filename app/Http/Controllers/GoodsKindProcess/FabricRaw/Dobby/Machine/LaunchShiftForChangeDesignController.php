<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class LaunchShiftForChangeDesignController extends Controller {
    public static $info = [
        "route"                    => "fabric_raw.machine.launch_shift_for_change_design.",
        "enable_status"            => [ "033" ],
        "button"                   => [ "caption" => "راه اندازی شیفت جهت تغییر کالیته", "class" => "btn-primary" ],
        "view_path"                => "goods_kind_process.fabric_raw.machine.launch_shift_for_change_design.",
        "enable_special_condition" => 1
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = LaunchShiftForChangeDesignController::$info["route"];
        $this->view_path  = LaunchShiftForChangeDesignController::$info["view_path"];
    }

    public function index( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $shift_work_option = Option::get( "shift_work" );
        $allocation        = $machine->getCurrentAllocation();

        if ( $allocation->has_product_change ) {
            $form_open_count = ProductionForm::
            where( [ "machine_id" => $machine->id ] )->
            whereIn( "status_id", [
                7002001, // در حال تکمیل
                7002007 // در انتظار استخراج پارچه پایانی
            ] )->count();
            if ( $form_open_count ) {
                return back()->withErrors( "لطفا ابتدا غلطک پارچه خام را استخراج و در سیستم ثبت نمایید." );
            }
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

        return view( $this->view_path . "index", compact( "machine", "shift_work_option", "allocation" ) );

    }

    public function submit( Request $request, Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

         $allocation = $machine->getCurrentAllocation();

// اگر فرم تولید در وضعیت در حال تکمیل یا درحال بافت پارچه پایانی نباشد خطا دهد


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

        if ( $allocation->has_product_change ) {
            // بررسی حامل
            $result = Carrier::firstOrCreate( $request->carrier_id, 4, 5320001, null );
            if ( ! $result["result"] ) {
                return redirect()->back()->withErrors( $result["message"] );
            }
            $carrier = $result["carrier"];
            if ( $carrier->status_id != 5320001 ) {
                return redirect()->back()->withErrors( "شماره غلطک وارد شده خالی نیست، لطفا یک شماره غلطک خالی وارد کنید." );
            }
        }
        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 400;
        $machineLog->contour_1_value       = $request->contour_1_value;
        $machineLog->contour_2_value       = $request->contour_2_value;
        $machineLog->contour_3_value       = $request->contour_3_value;
        $machineLog->contour_4_value       = $request->contour_4_value;
        $machineLog->contour_5_value       = $request->contour_5_value;
        $machineLog->shift_work_id         = $request->shift_work_id;
        $machineLog->save();


        // آیا کد کالا در تخصیص جدید تغییر یافته است.
        if ( $allocation->has_product_change ) {
            // حامل در حال تکمیل
            $carrier->SetStatus( 5320006,$machine->fullCaption());

            // ایجاد فرم تولید
            $production_form = ProductionForm::AddNewForm(
                $allocation->id,
                $machine->id,
                $carrier->id,
                $machineLog->id
            );


            foreach ( $allocation->items as $item ) {
                ProductionFormItem::AddNewItem(
                    $allocation->id,
                    $production_form->id,
                    $item->production_id,
                    $item->product_id,
                    $item->band_code,
                    7002001, // در حال تکمیل
                    0,
                    $item->version_code??null

                );
                $carrier->addProduct( $item->product_id );
            }

            $machine->setStatus(
                null,
                53001,
                7003016, // در حال بافت
                null,
                "Fabric_Raw"
            );

        } else { // کالا عوض نشده
            $has_open_form = ProductionForm::
            where( "machine_id", $machine->id )->
            whereIn( "status_id",
                [
                    7002001, // در حال تکمیل)
                    7002008 // در حال تکمیل پارچه پایانی
                ] )->
            exists();
            if ( !$has_open_form ) {
                return back()->withErrors( "وضعیت فرم تولید نادرست است، لطفا با واحد پشتیبانی تماس بگیرید." );
            }

            $machine->setStatus(
                null,
                53001,
                7003035, // در حال بافت پارچه پایانی
                null,
                "Fabric_Raw"

            );
        }


        event( new MachineLogEvent( $machine, $machineLog ,"",false,$last_row_log) );

        // تولید لات پارچه
        FabricRaw:: ChangeLot( $allocation );


        // تغییر وضعیت کارت تولید
        $perfix_status_production = \App\Http\Controllers\GoodsKindProcess\Fabric_Raw\Dobby\ProductionCard\DashboardController::$perfix_status_code;
        foreach ( $allocation->items as $item ) {
            if ( $item->production->waiting_status_id == $perfix_status_production . "002" ) {
                $item->production->waiting_status_id = $perfix_status_production . "003";
                $item->production->save();
                event( new ProductionCardLogEvent( $item->production ) );
            }
        }

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, LaunchShiftForChangeDesignController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
