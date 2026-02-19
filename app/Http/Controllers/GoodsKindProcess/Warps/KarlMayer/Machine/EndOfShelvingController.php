<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\InjectionOfMaterialController;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\AllocationDoffs;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Option;
use App\Models\Warehouse\WarehouseProduct;
use Illuminate\Http\Request;


class EndOfShelvingController extends Controller {
    public static $info = [
        "route"         => "warps.karl_mayer.machine.end_of_shelving.",
        "enable_status" => [ "003" ],
        "button"        => [ "caption" => "پایان قفسه گذاری", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.warps.karl_mayer.machine.end_of_shelving.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.karl_mayer.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfShelvingController::$info["route"];
        $this->view_path  = EndOfShelvingController::$info["view_path"];
    }

    public function index( Machine $machine ) {

        $allocation = $machine->getCurrentAllocation();
        if ( ! $allocation ) {
            return back()->withErrors( "تخصیص جاری برای ماشین یافت نشد." );
        }

        $result=CurrentMachineInput::CheckInventoryAndPackingForms($allocation);
        if(!$result["result"]){
            return back()->withErrors($result["error"]);
        }



        $current_input_list = CurrentMachineInput::where( "allocation_id", $allocation->id )->get();

        $machine_allocation  = $allocation->items()->first();
        $production          = $machine_allocation->production;
        $packing_type_option = Option::get( "production_packing_type", 0, 0, $production->packing_types );


        return view( $this->view_path . "index", compact( "machine", "current_input_list", "production", "packing_type_option" ) );
    }

    public function submit( Request $request, Machine $machine ) {
        /***
         *  این تابع عین matthys است، اگر تغییر در آن انجام شده، باید در اینجا هم انجام شود.
         */
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();
        if ( ! $allocation ) {
            return back()->withErrors( "تخصیص جاری برای ماشین وجود ندارد، لطفا با مسئول مربوطه تماس بگیرید." );
        }
        $machine_allocation = $allocation->items()->first();
        if ( ! $machine_allocation ) {
            return back()->withErrors( "تخصیص جاری برای ماشین وجود ندارد، لطفا با مسئول مربوطه تماس بگیرید." );
        }
        if ( $machine_allocation->production->packing_types()->count() == 0 ) {
            return back()->withErrors( "نوع بسته بندی برای کارت تولید مشخص نشده است." );
        }

        $packing_type = PackingType::find( $request->packing_type_id );
        if ( ! $packing_type ) {
            return back()->withErrors( "لطفا نوع بسته بندی را انتخاب نمایید." );
        }
        $result_doff=  AllocationDoffs::HasAnyDoff($allocation->id,$packing_type->id);
        if(!$result_doff["result"]){
            return back()->withErrors($result_doff["error"]);
        }

        // ثبت بسته بندی یا لات مربوط به ورودی ها
        $message = "";
        if ( $machine->check_inventory_for_allocation ) {
            // ارسال درخواست برای کدام رسته های کالایی فعال است.
            $goods_kind_ids = MachineTypeInputBandGoodsKind:: getGoodsKindIdsWhereRequestFromRobot( $machine, $active = 1 );

            $reserve_input_list = CurrentMachineInput::
            where( [ "machine_id" => $machine->id, "allocation_id" => ( $allocation->id ?? - 1 ) ] )->
            whereIn( "goods_kind_id", $goods_kind_ids )->
            orderBy( "input_line_code" )->
            get();


            $input_number = count( $reserve_input_list );
            $result       = InjectionOfMaterialController::SetInjectionMaterial( $request, $machine, $allocation, $input_number );
            if ( ! $result["result"] ) {
                return back()->withErrors( $result["error"] );
            }
        } else {
            $data               = $request->data;
            $current_input_list = CurrentMachineInput::where( "allocation_id", $allocation->id )->get();
            foreach ( $current_input_list as $current_input ) {

                if ( isset( $data["input"][ $current_input->id ] ) ) {
                    $lot_number = LotNumber::where( [
                        "code"       => $data["input"][ $current_input->id ],
                        "product_id" => $current_input->material_id
                    ] )->first();

                    if ( $lot_number ) {
                        $current_input->lot_number_id = $lot_number->id;
                        $current_input->save();
                    } else {
                        $message .= "لات " . ( $data["input"][ $current_input->id ] ) . " برای " . $current_input->material->caption . " یافت نشد." . "<br/>";

                    }


                } else {
                    $message .= "لطفا لات برای ورودی " . $current_input->input_line_code_from . " را وارد نمایید." . "<br/>";
                }
            }

            if ( $message != "" ) {
                return back()->withErrors( $message );
            }

        }

        // بررسی اینکه می توان لات تولید کند یا خیر
        $product = $allocation->items()->first()->product;
        $result  = Warps::getLotNumber( $allocation, $machine, 1, $product );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }


        $last_row_log                      = MachineLog::getLastLogWithContour( $machine );
        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 1010;
        $machineLog->contour_1_value       = $last_row_log->contour_1_value ?? 0;
        $machineLog->save();

        // ایجاد یک فرم تولید در انتظار چله کشی برای تخصیص
       self::CreateProductionFromForWarps($machine,$allocation,$machineLog,$packing_type);


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public static function CreateProductionFromForWarps(Machine $machine,Allocation $allocation,MachineLog $machineLog,PackingType $packing_type){
        // ایجاد فرم تولید جدید با وضعیت در انتظار شروع چله کشی
        $production_form_status_id = 7202001;// در انتظار شروع چله کشی
        $production_form           = ProductionForm::AddNewForm( $machine->id, null, $machineLog->id, $packing_type->id, null, $production_form_status_id );
        $production_form->save();

        event( new ProductionFormLogEvent(
            $production_form,
            7202001 //ایجاد فرم تولید
        ) );


// اضافه کردن یک رکورد در فرم تولید
        foreach ( $allocation->items as $item ) {
            ProductionFormItem::AddNewItem(
                $allocation->id,
                $production_form->id,
                $item->production_id,
                $item->product_id,
                $item->band_code,
                $production_form_status_id,
                $item->amount_of_each_doffs,
                $item->version_code??null
            );
        }

        // تولید لات برای فرم جاری
        Warps::ChangeLot( $allocation, false, $production_form->id );

        $machine->setStatus(
            null,
            53002, // خاموش
            7203004, // در انتظار شروع چله کشی
            2020 );

        event( new MachineLogEvent( $machine, $machineLog ) );
    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfShelvingController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
