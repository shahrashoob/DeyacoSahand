<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;

use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;

use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\CurrentMachineInput;
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

class LaunchChangeLotController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.machine.launch_change_lot.",
        "enable_status" => [ "012" ],
        "button"        => [ "caption" => "راه اندازی تغییر کالیته", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.launch_change_lot.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = LaunchChangeLotController::$info["route"];
        $this->view_path  = LaunchChangeLotController::$info["view_path"];
    }

    public function index( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        $allocation=$machine->getCurrentAllocation();

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



        $machine_input_share_band=CurrentMachineInput::
        where([
            "allocation_id"=>$allocation->id,
            "goods_kind_id"=>2 // نخ
            ])->get();

        $shift_work_option = Option::get( "shift_work" );

        return view( $this->view_path . "index", compact( "machine", "shift_work_option", "machine_input_share_band" ) );
    }

    public function submit( Request $request, Machine $machine ) {

        $allocation = $machine->getCurrentAllocation();

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

        // بررسی حامل
        $result = Carrier::firstOrCreate( $request->carrier_id, 4, 5320001, null );
        if(!$result["result"]){
            return redirect()->back()->withErrors($result["message"]);
        }
        $carrier=$result["carrier"];
        if ( $carrier->status_id != 5320001 ) {
            return redirect()->back()->withErrors( "شماره غلطک وارد شده خالی نیست، لطفا یک شماره غلطک خالی وارد کنید." );
        }

        //  (ورودی اشتراکی ) دریافت لات نخ
        $machine_input_share_band=CurrentMachineInput::
        where([
            "allocation_id"=>$allocation->id,
            "goods_kind_id"=>2 // نخ
        ])->get();

        foreach ( $machine_input_share_band as $item ) {

                $lot_code                     = "lot_" . $item->id . "_code";
                if(!isset($request->$lot_code)){
                    return back()->withErrors("همه همبافت های مورد نیاز وارد نشده است.");
                }
                $lot_number                   = LotNumber::firstOrCreate( [
                    "product_id" => $item->material_id,
                    "code"       => $request->$lot_code
                ] );
              $item->lot_number_id=$lot_number->id;
              $item->save();

        }


        // لاگ ماشین
        $machineLog                        = MachineLog::create();
        $machineLog->machine_event_type_id = 200;
        $machineLog->contour_1_value       = $request->contour_1_value;
        $machineLog->contour_2_value       = $request->contour_2_value;
        $machineLog->contour_3_value       = $request->contour_3_value;
        $machineLog->contour_4_value       = $request->contour_4_value;
        $machineLog->contour_5_value       = $request->contour_5_value;
        $machineLog->shift_work_id         = $request->shift_work_id;
        $machineLog->save();

        $machine->on_status_id          = 53002;
        $machine->machine_off_reason_id = 1613;
        $machine->production_status_id  = DashboardController::$perfix_production_status_code . "015";
        $machine->save();


        event( new MachineLogEvent( $machine, $machineLog,"",false,$last_row_log ) );

// حامل در حال تکمیل
        $carrier->setStatus( 5320006,$machine->fullCaption());


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
                7002001, // در حال تکمیلو
                0,
                $item->version_code??null

            );
            $carrier->addProduct( $item->product_id );
        }

        // تولید لات پارچه
       FabricRaw:: ChangeLot( $allocation );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, LaunchChangeLotController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
