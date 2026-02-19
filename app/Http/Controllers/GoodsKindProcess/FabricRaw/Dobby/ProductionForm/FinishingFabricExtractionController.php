<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\ProductionForm;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class FinishingFabricExtractionController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.production_form.finishing_fabric_extraction.",
        "enable_status" => [ "007", "008" ],
        "button"        => [ "caption" => "استخراج پارچه (پایانی)", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.production_form.finishing_fabric_extraction.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.production_form.dashboard.";

    public function __construct() {
        $this->route_path = FinishingFabricExtractionController::$info["route"];
        $this->view_path  = FinishingFabricExtractionController::$info["view_path"];
    }

    public function index( ProductionForm $production_form ) {

        $result = $this->checkPermission( $production_form );
        if ( $result != "" ) {
            return $result;
        }
        $shift_work_option  = Option::get( "shift_work" );
        $type_of_cut_option = Option::get( "fabric_raw_type_of_cut" );

        return view( $this->view_path . "index", compact( "production_form", "shift_work_option", "type_of_cut_option" ) );

    }

    public function submit( Request $request, ProductionForm $production_form ) {

        $result = $this->checkPermission( $production_form );
        if ( $result != "" ) {
            return $result;
        }

        // دریافت قطب ها
        $last_row_log = MachineLog::getLastLogWithContour( $production_form->machine );

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

        if ( $production_form->status_id == 7002008 ) { // در حال تکمیل پارچه پایانی
            $result = Carrier::firstOrCreate( $request->carrier_id, 4, 5320001, null );
            if ( ! $result["result"] ) {
                return redirect()->back()->withErrors( $result["message"] );
            }
            $new_carrier = $result["carrier"];
            if ( $new_carrier->status_id != 5320001 ) {
                return redirect()->back()->withErrors( "شماره غلطک وارد شده خالی نیست، لطفا یک شماره غلطک خالی وارد کنید." );
            } else if ( $new_carrier->carrier_type_id != 4 ) {
                return redirect()->back()->withErrors( "حامل قبلا با یک کالای دیگری بارگیری شده است" );

            }

            // حامل در حال تکمیل
            $new_carrier->SetStatus( 5320006, $production_form->machine->fullCaption() );


        }

//        // ویرایش وضعیت ماشین
//        $production_form->machine->setStatus(
//            null,
//            1210, // روشن
//            7003016, // در حال بافت
//            null
//        );


        $machineLog                        = MachineLog::create( [] );
        $machineLog->machine_event_type_id = 460;
        $machineLog->contour_1_value       = $request->contour_1_value;
        $machineLog->contour_2_value       = $request->contour_2_value;
        $machineLog->contour_3_value       = $request->contour_3_value;
        $machineLog->contour_4_value       = $request->contour_4_value;
        $machineLog->contour_5_value       = $request->contour_5_value;
        $machineLog->shift_work_id         = $request->shift_work_id;

        event( new MachineLogEvent( $production_form->machine, $machineLog, "", true, $last_row_log ) );


        $production_form->carrier->SetStatus( 5320005 ); //  پر در انتظار درجه بندی


        // نگهداری وضعیت فرم تولید برای شرط پایین تر
        $old_production_form_status_id = $production_form->status_id;
        // تغییر وضعیت فرم به در انتظار درجه بندی
        $production_form->status_id             = DashboardController::$perfix_status_code . "003";
        $production_form->end_of_machine_log_id = $machineLog->id;
        $production_form->save();


        // بروز رسانی مقدار فرم ها
        $production_form->updateItemAmount( true, true );

        // برورز رسانی نوع برش پارچه و فاصله شانه تا چروک گیر برای استخراج
        $production_form->updateExtraAmountForExtraction( $request->fabric_raw_type_of_cut_id );

        event( new ProductionFormLogEvent(
            $production_form,
            700205 // استخراج فرم
        ) );

        if ( $old_production_form_status_id == 7002008 ) { // در حال تکمیل پارچه پایانی
//        // ایجاد فرم تولید برای کارت تولید جدید
            $allocation          = $production_form->machine->getCurrentAllocation();
            $production_form_new = ProductionForm::AddNewForm(
                $allocation->id,
                $production_form->machine->id,
                $new_carrier->id,
                $machineLog->id
            );


            foreach ( $allocation->items as $item ) {
                ProductionFormItem::AddNewItem(
                    $production_form_new->id,
                    $item->production_id,
                    $item->product_id,
                    $item->band_code,
                    7002001 // در حال تکمیل
                );
                $new_carrier->addProduct( $item->product_id );
            }
            // بروز رسانی مقدار فرم
            FabricRaw:: ChangeLot( $allocation );
        }


        return redirect()->route( $this->dashboard_route . "index" )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( ProductionForm $production_form ) {

        $result = DashboardController::checkPermissionConditions( $production_form, FinishingFabricExtractionController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
