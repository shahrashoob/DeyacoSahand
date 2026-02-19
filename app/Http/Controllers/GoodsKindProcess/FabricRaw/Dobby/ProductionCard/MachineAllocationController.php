<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\ProductionCard;

use App\Events\Fabric_Raw\FabricRawDesignFormLogEvent;
use App\Events\Machine\MachineAllocationEvent;
use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCardController;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignForm;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignFormProduction;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachinePropertyValue;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBand;
use App\Models\LineProduct\Product;
use App\Models\Production\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;


class MachineAllocationController extends Controller {
    //
    public static $info = [
        "route"         => "fabric_raw.dobby.machine_allocation.",
        "enable_status" => [ "001", "002", "003" ],
        "next_status"   => [],
        "button"        => [ "caption" => "تخصیص ماشین (دابی/بادامکی)", "class" => "btn-success" ],
        "view_path"     => "goods_kind_process.fabric_raw.dobby.production_card.machine_allocation."
    ];
    public $shoulder_width_id = 220228;
    public $dashboard_route = "fabric_raw.dobby.machine_allocation.";
    public $controller_info;

    public function __construct() {
        $perfix_status_code    = DashboardController::$perfix_status_code;
        $this->view_path       =
            View::share( "perfix_status_code", $perfix_status_code );
        $this->controller_info = MachineAllocationController::$info;
    }

    public function index( Production $production, MachineType $machine_type ) {

        // return $machine_type;
        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        if($machine_type->machine->count() ==0){
            return  back()->withErrors("برای این گروه ماشینی تعریف نشده است.");
        }

        return \view( $this->controller_info["view_path"] . "index", compact( "production", "machine_type" ) );
    }

    public function select_band( Request $request, $machine_id, MachineType $machine_type, Production $production, $is_first_production = false ) {
        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $key     = "machine_type_" . $machine_type->id;
        $machine = Machine::find( $request->$key ?? $machine_id );

        if ( ! isset( $machine ) ) {
            return redirect()->
            route( $this->dashboard_route . "index", $production )->withErrors( "لطفا یک ماشین جهت تخصیص انتخاب نمایید." );
        }
        // حذف کارت تولید های در انتظار تایید
        if ( $is_first_production ) {
            MachineAllocation::where( [ "machine_id" => $machine->id, "status_id" => 5310005 ] )->delete();
        }

        // چک کردن اینکه گروه ماشین در خط محصول وجود داشته باشد.
        if ( ! $production->product->line_product_station()->where( "machine_type_id", $machine_type->id )->exists() ) {
            return redirect()->
            route( $this->dashboard_route . "index", $production )->
            withErrors( "ماشین در خط - محصول های تعریف شده برای محصول وجود ندارد." );
        }
        $machine_check = Machine::
        join( "machine_status", "machines.production_status_id", "machine_status.production_status_id" )->
        where( [
            "machine_type_id"                   => $machine_type->id,
            "possibility_of_allocation_machine" => 1,
            "machines.id"                       => $machine->id,
            "machines.active_status_id"         => 1200
        ] )->first();

        if ( ! isset( $machine_check ) ) {
            return redirect()->
            route( $this->dashboard_route . "index", $production )->
            withErrors( "وضعیت ماشین برای تخصیص نامعتبر است." );
        }


        // عرض پارچه
        $product_shoulder_width = $production->product->getPropertyValue( $this->shoulder_width_id );
        if ( ! isset( $product_shoulder_width ) ) {
            return redirect()->
            route( $this->dashboard_route . "index", $production )->
            withErrors( "عرض پارچه برای محصول ثبت نشده است" );
        }
        $product_shoulder_width = $product_shoulder_width->value;

        //         عرض شانه ماشین
        $machine_shoulder_width = MachinePropertyValue::
        where( [ "machine_type_id" => $machine->machine_type->id, "machine_property_id" => 1 ] )->
        first();
        if ( ! isset( $machine_shoulder_width ) ) {
            return redirect()->
            route( $this->dashboard_route . "index", $production )->
            withErrors( "عرض ماشین برای نوع ماشین ثبت نشده است" );
        }
        $machine_shoulder_width = $machine_shoulder_width->value;

// عرض  باقی مانده ماشین
        $machine_shoulder_width_allocation = 0;
        $product_allocation_list           = MachineAllocation::where( [
            "machine_id" => $machine->id,
            "status_id"  => 5310005
        ] )->
        get();

        foreach ( $product_allocation_list as $item ) {
            $machine_shoulder_width_allocation += $item->product->getPropertyValue( $this->shoulder_width_id )->value;
        }
        $machine_shoulder_width_allocation = $machine_shoulder_width - $machine_shoulder_width_allocation;


        // حداکثر چند باند می تواند تخصیص دهد.
        $band_count_allocation = floor( $machine_shoulder_width_allocation / $product_shoulder_width );
        $band_count_allocation = min( $band_count_allocation, $machine->machine_type->band_number );

        if ( $band_count_allocation < 1 ) {

            // هیچ باندی نمی تواند تخصیص دهد، تایید نهایی تخصیص
            return redirect()->route( "fabric_raw.machine_allocation.select_input_line", [ $machine, 0 ] );


        } else {
            // حداقل می تواند یک باند به کارت تخصیص دهد.

            // شماره باند تخصیص آزاد
            $reserve_band_list = $product_allocation_list->pluck( "band_code" )->toArray();
            $open_band_list    = [];
            for ( $i = 1; $i <= $machine_type->band_number; $i ++ ) {
                if ( ! in_array( $i, $reserve_band_list ) && count( $open_band_list ) < $band_count_allocation ) {
                    $open_band_list[] = $i;
                }
            }

            return \view( $this->controller_info["view_path"] . "select_band", compact( "machine", "production", "open_band_list", "band_count_allocation" ) );

        }
    }

    public function select_other_production( Machine $machine, Production $production ) {
        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $production_allow_list = $this->get_other_production_for_allocation( $machine, $production );
        // هیچ کارت تولیدی برای بافت در باندهای دیگر یافت نشد، تایید تخصیص و خاتمه
        if ( count( $production_allow_list ) == 0 ) {

            return redirect()->route( "fabric_raw.machine_allocation.select_input_line", [ $machine, 0 ] );
        }

        return \view( $this->controller_info["view_path"] . "select_other_production", compact( "machine", "production", "production_allow_list" ) );
    }

    public function select_band_submit( Request $request, Machine $machine, Production $production ) {

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        // تخصیص باندهای انتخاب شده به کارت تولید
        for ( $i = 1; $i <= $machine->machine_type->band_number; $i ++ ) {
            $band_id   = "band_" . $i;
            $band_name = "band_name_" . $i;
            if ( isset( $request->$band_id ) && $request->$band_id > 0 ) {
                event( new MachineAllocationEvent( $production, $machine, "Fabric_Raw", $request->$band_name ) );
            }

        }

        $production_allow_list = $this->get_other_production_for_allocation( $machine, $production );
        // هیچ کارت تولیدی برای بافت در باندهای دیگر یافت نشد، تایید تخصیص و خاتمه
        if ( count( $production_allow_list ) == 0 ) {

            return redirect()->route( "fabric_raw.machine_allocation.select_input_line", [ $machine, 0 ] );
        }

        // انتخاب یک کارت تولید دیگر برای بافت در باند های دیگر
        return redirect()->route( "fabric_raw.machine_allocation.select_other_production", [ $machine, $production ] );

    }

    private function get_other_production_for_allocation( $machine, $production ) {
        //         عرض شانه ماشین
        $machine_shoulder_width = MachinePropertyValue::
        where( [ "machine_type_id" => $machine->machine_type->id, "machine_property_id" => 1 ] )->
        first()->value;

        // عرض  باقی مانده ماشین
        $machine_shoulder_width_allocation = 0;
        $product_allocation_list           = MachineAllocation::where( [
            "machine_id" => $machine->id,
            "status_id"  => 5310005
        ] )->
        get();

        foreach ( $product_allocation_list as $item ) {
            $item_value                        = $item->production->product->getPropertyValue( $this->shoulder_width_id );
            $machine_shoulder_width_allocation += isset( $item_value ) ? $item_value->value : 0;

        }
        $machine_shoulder_width_allocation = $machine_shoulder_width - $machine_shoulder_width_allocation;

        /**
         * پیدا کردن کارت تولید هایی که می تواند در باندهای دیگر ماشین بافته شوند
         */

        // به دست آورن مقدار مشخصه ها
        $production_list = Production::
        whereIn( "waiting_status_id",
            \App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCard\MachineAllocationController::$info["enable_status_full"]
        )->
        where( "id", "!=", $production->id )->
        get();

        $checklist              = [ 220224, 220225, 220237, 220238, 220241, 220242, 220279, 220280 ];
        $property_value_product = GoodsKindPropertyValue::
        where( "product_id", $production->product->id )->
        whereIn( "goods_kind_property_id", $checklist )->orderBy( "goods_kind_property_id" )->
        pluck( "value" )->toArray();


        $production_allow_list = [];
        foreach ( $production_list as $item ) {

            $property_value_item = GoodsKindPropertyValue::
            where( "product_id", $item->product->id )->
            whereIn( "goods_kind_property_id", $checklist )->
            pluck( "value", "goods_kind_property_id" )->toArray();

            // عرض پارچه
            $product_shoulder_width = $item->product->getPropertyValue( $this->shoulder_width_id );
            if ( isset( $product_shoulder_width ) ) {
                $product_shoulder_width = $product_shoulder_width->value;
            } else {
                $product_shoulder_width = - 1;
            }

// همه مشخصه های پارچه یکی باشد و عرض شانه باقی مانده ماشین از عرض شانه کالا کمتر مساوی باشد.
            if ( count( array_diff( $property_value_product, $property_value_item ) ) == 0 && $machine_shoulder_width_allocation >= $product_shoulder_width ) {
                $production_allow_list [] = $item;
            }

        }

        return $production_allow_list;
    }


    public function confirm_submit( Machine $machine ) {

        $machine_allocation = MachineAllocation::where( [
            "machine_id" => $machine->id,
            "status_id"  => 5310005
        ] )->get();

        // گرفتن تخصیص معلق
        $allocation = Allocation::where( [
            "machine_id" => $machine->id,
            "status_id"  => 5310005
        ] )->first();

        if ( count( $machine_allocation ) == 0 || ! $allocation ) {
            return back()->withErrors( "هیچ ماشینی تخصیص داده نشده است." );
        }
        $production = $machine_allocation[0]->production;
        if ( ! isset( $production ) ) {
            $production = $machine_allocation[0]->production;
        }
        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }


        $allocation_status = 5310040; // تخصیص رزرو شده

        // تغییر وضعیت تخصیص فعلی
        $allocation->status_id = $allocation_status;
        $allocation->save();

        // بررسی تفاوت تخصیص جدید با تخصیص قبلی
        $allocation->setChangesFromBeforeAllocation( "Fabric_Raw" );

        if ( $allocation->has_design_change ) {

            // ایجاد فرم طراحی
            $design_form = FabricRawDesignForm::create( [
                "machine_id"    => $machine->id,
                "status_id"     => FabricRawDesignForm::$perfix_status_code . "001",
                "allocation_id" => $allocation->id
            ] );

            $design_form->getCode();
            event( new FabricRawDesignFormLogEvent( $design_form ) );
        }


        foreach ( $allocation->items as $item ) {

            $item->status_id = $allocation_status;
            $item->save();

            if ( $allocation->has_design_change ) {
                FabricRawDesignFormProduction::create( [
                    "production_id"             => $item->production_id,
                    "fabric_raw_design_form_id" => $design_form->id,
                    "band_code"                 => $item->band_code
                ] );
            }
// بروز رسانی وضعیت کارت تولید
            if ( $item->production->waiting_status_id == "7001" . "001" ) {
                $item->production->waiting_status_id = "7001" . "002";
                $item->production->save();

            }


        }

        // آخرین وضعیت  قبل از تخصیص ماشین
        $machineLog                        = MachineLog::create();
        $machineLog->machine_event_type_id = 90; // وضعیت قبل از تخصیص ماشین
        event( new MachineLogEvent( $machine, $machineLog ) );

        // بروزرسانی وضعیت تولید ماشین
        $machine->setStatus(
            null,
            null,
            7003026,
            null,
            "Fabric_Raw"
        );

        // لاگ تخصیص جدید ماشین
        $machineLog                        = MachineLog::create();
        $machineLog->machine_event_type_id = 92;
        event( new MachineLogEvent( $machine, $machineLog ) );

        return redirect()->route( "production.dashboard.list" )->with( [ "success" => "عملیات تخصیص با موفقیت انجام شد." ] );

    }

    public function checkPermission( Production $production ) {

        // بررسی دسترسی خاص ماژول تخصیص
        $result = ProductionCardController::checkPermissionConditionsFull(
            $production,
            \App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCard\MachineAllocationController::$info
        );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

//         بررسی دسترسی در ماژول
        $result = DashboardController::checkPermissionConditions( $production, null, );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

    // مقدار دهی باند ورودی و خط ورودی

    public function select_input_line( Machine $machine, $is_edit = false ) {

        $machine_allocation = MachineAllocation::where( [
            "machine_id" => $machine->id,
            "status_id"  => 5310005
        ] )->get();

        if ( count( $machine_allocation ) == 0 ) {
            return redirect()->
            route( "dashboard" )->
            withErrors( "هیچ ماشینی تخصیص داده نشده است و یا با توجه به مشخصات کالا امکان تخصیص کارت به ماشین وجود ندارد.." );
        }

        // قبلا ماشین را به کارت تولید اختصاص داده است؟
        foreach ( $machine_allocation as $item ) {

            $before_allocation = MachineAllocation::
            where( "machine_id", $machine->id )->
            where( "production_id", $item->production_id )->
            whereIn( "status_id", [ 5310010, 5310040 ] )->count();
            if ( $before_allocation > 0 ) {
                return redirect()->
                route( $this->dashboard_route . "index", $item->production_id )->
                withErrors( "ماشین " . $machine->fullCaption() . " قبلا به کارت تولید " . $item->production->serial() . " تخصیص داده شده است." );
            }
        }

        if ( ! $is_edit ) {
            CurrentMachineInput::where( "allocation_id", $machine_allocation[0]->allocation_id )->delete();
        }


        foreach ( $machine_allocation as $allocation_item ) {

            $production = $allocation_item->production;
            // Check BOM Exists
            if ( $production->product->bill_of_material()->count() == 0 ) {
                return redirect()->
                route( $this->dashboard_route . "index", $production )->
                withErrors( "BOM کالای" . $production->product->fullCaption() . " تعریف نشده است." );
            }
            // انتخاب اولین BOM برای تخصیص
            $bom = $production->product->bill_of_material()->first();
            if ( $bom->items->count() == 0 ) {
                return redirect()->
                route( $this->dashboard_route . "index", $production )->
                withErrors( $bom->caption . "  برای کالای " . $production->product->fullCaption() . " ;به صورت کامل تعریف نشده است." );
            }

//echo "<br/> allocation:".$allocation_item->id;
            foreach ( $bom->items as $bom_item ) {
//echo "<br/>".$bom_item->id;
                // به دست آوردن شناسه باند ورودی
                $input_band = MachineTypeInputBand::
                join( "machine_type_input_band_goods_kind", "machine_type_input_band_id", "machine_type_input_bands.id" )->
                where( [
                    "active_status_id" => 1200, // فعال
                    "machine_type_id"  => $machine->machine_type_id,
                    "goods_kind_id"    => $bom_item->material->goods_kind_id
                ] )->
                select( "machine_type_input_bands.id", "effect_is_shared", "input_line_number" )->
                get();
                if ( count( $input_band ) != 1 ) {
                    return redirect()->route( $this->dashboard_route . "index", $production )->
                    withErrors( "مشخصات باند ورودی برای " . $bom_item->material->fullCaption() . " یافت نشد، لطفا با پشتیبانی سیستم تماس بگیرید." );
                }
                $input_band = $input_band[0];

                // به تعدادی که در BOM ذکر شده درخواست می دهد.

                for ( $k = 1; $k <= $bom_item->number; $k ++ ) {

                    $current_input_output_band = CurrentMachineInput::createNew(
                        $production->id,
                        $allocation_item->product_id,
                        null,
                        $machine->machine_type_id,
                        $machine->id,
                        $input_band->id,
                        $bom_item->input_line_code,   //  کد خط ورودی
                        $allocation_item->band_code,
                        $bom_item->material->id,
                        $bom_item->number,
                        $bom_item->amount,
                        $bom_item->percent_of_use,
                        $input_band->effect_is_shared,
                        $bom_item->material->goods_kind_id,
                        $allocation_item->allocation_id
                    );
                }

                // رسته های کالای اشتراکی باید درصد استفاده آنها 100 باشد
                if ( $input_band->effect_is_shared && $bom_item->percent_of_use != 100 ) {
                    return redirect()->
                    route( $this->dashboard_route . "index", $production )->
                    withErrors( "در " . $production->product->fullCaption() . ":  رسته های کالای اشتراکی باید درصد استفاده آنها 100 باشد" );
                }

                // اگر رسته کالایی تاثیر مشترک دارد، فقط یک ردیف باید ایجاد گردد و ردیف های دیگر حذف می شوند
                if ( $input_band->effect_is_shared ) {
                    $repeat_row = CurrentMachineInput::where( [
                        "allocation_id" => $allocation_item->allocation_id,
                        "material_id"   => $bom_item->material->id,
                    ] )->skip( 1 )->first();
                    if ( $repeat_row ) {
                        $repeat_row->delete();
                    }
                }

                // بررسی حذف رکورد با توجه به درصد استفاده
                if ( $bom_item->percent_of_use != 100 ) {
                    $calculate_row = CurrentMachineInput::where( [
                        "allocation_id" => $allocation_item->allocation_id,
                        "material_id"   => $bom_item->material->id,
                    ] )->
                    addSelect( DB::raw( "count(id) * 100 - sum(percent_of_use)  as remaining_amount" ) )->
                    first();

                    if ( $calculate_row->remaining_amount > 100 ) {
                        $current_input_output_band->input_line_code = 0;
                        $current_input_output_band->save();
                    }

                    // در صورتی که مقدار باقی مانده منفی باشد، یعنی درصد مصرف در BOM بیش از 100 بوده
                    if ( $calculate_row->remaining_amount < 0 ) {
                        // به دست آوردن شناسه باند ورودی
                        $input_band = MachineTypeInputBand::
                        join( "machine_type_input_band_goods_kind", "machine_type_input_band_id", "machine_type_input_bands.id" )->
                        where( [
                            "active_status_id" => 1200, // فعال
                            "machine_type_id"  => $machine->machine_type_id,
                            "goods_kind_id"    => $bom_item->material->goods_kind_id
                        ] )->
                        select( "machine_type_input_bands.id", "effect_is_shared", "input_line_number" )->
                        get();
                        if ( count( $input_band ) != 1 ) {
                            return redirect()->route( $this->dashboard_route . "index", $production )->
                            withErrors( "مشخصات باند ورودی برای " . $bom_item->material->fullCaption() . " یافت نشد، لطفا با پشتیبانی سیستم تماس بگیرید." );
                        }
                        $input_band = $input_band[0];

                        $current_input_output_band = CurrentMachineInput::createNew(
                            $production->id,
                            $allocation_item->product_id,
                            null,
                            $machine->machine_type_id,
                            $machine->id,
                            $input_band->id,
                            $allocation_item->band_code,
                            $allocation_item->band_code,
                            $bom_item->material->id,
                            $bom_item->number,
                            $bom_item->amount,
                            0,
                            $input_band->effect_is_shared,
                            $bom_item->material->goods_kind_id,
                            $allocation_item->allocation_id
                        );

                    }
                }

                // بررسی تعداد ورودی های ماشین
                $input_line_row = CurrentMachineInput::where( [
                    "allocation_id" => $allocation_item->allocation_id,
                    "material_id"   => $bom_item->material->id,
                ] )->whereNull( "input_line_code" )-> // حذف باندهای اشتراکی
                count();

                if ( $input_line_row > $input_band->input_line_number ) {
                    return redirect()->
                    route( $this->dashboard_route . "index", $production )->
                    withErrors( "با توجه به محدودیت تعداد خط های ورودی  امکان تخصیص وجود ندارد." );

                }

            }
        }

        $machine_input_output_band_list = CurrentMachineInput::where( [
            "allocation_id" => $machine_allocation[0]->allocation_id
        ] )->
        orderBy( "input_band_id" )->
        get();

        return view( $this->controller_info["view_path"] . "select_input_line", compact( "machine", "production", "machine_input_output_band_list", "machine_allocation" ) );


    }

    public function get_allocation_different( Allocation $allocation ) {


// گرفتن تخصیص قبلی
        $before_allocation = $allocation->before_allocation(1);

        $value = Allocation::getDifferentTowAllocation( $before_allocation, $allocation, "Fabric_Raw" );


        $before_warps_bom = Product\BOM\BOMItem::join( "products", "material_id", "products.id" )->
        where( "bill_of_material_id", isset( $value["before_bom"] ) ? $value["before_bom"]->id : 0 )->
        where( "product_id", isset( $value["before_product"] ) ? $value["before_product"]->id : 0 )->
        where( "goods_kind_id", 3 )->
        orderBy( "material_id" )->
        get();


        $current_warps_bom = Product\BOM\BOMItem::join( "products", "material_id", "products.id" )->
        where( "bill_of_material_id", isset( $value["current_bom"] ) ? $value["current_bom"]->id : 0 )->
        where( "product_id", isset( $value["current_product"] ) ? $value["current_product"]->id : 0 )->
        where( "goods_kind_id", 3 )->
        orderBy( "material_id" )->
        get();


        $before_yarn_weft_bom  = Product\BOM\BOMItem::join( "products", "material_id", "products.id" )->
        where( "bill_of_material_id", isset( $value["before_bom"] ) ? $value["before_bom"]->id : 0 )->
        where( "product_id", isset( $value["before_product"] ) ? $value["before_product"]->id : 0 )->
        where( "goods_kind_id", 2 )->
        get();
        $current_yarn_weft_bom = Product\BOM\BOMItem::join( "products", "material_id", "products.id" )->
        where( "bill_of_material_id", isset( $value["current_bom"] ) ? $value["current_bom"]->id : 0 )->
        where( "product_id", isset( $value["current_product"] ) ? $value["current_product"]->id : 0 )->
        where( "goods_kind_id", 2 )->
        get();

        return view( $this->controller_info["view_path"] . "get_allocation_different", compact( "allocation", "value", "before_warps_bom", "current_warps_bom", "before_yarn_weft_bom", "current_yarn_weft_bom" ) );

    }
}


