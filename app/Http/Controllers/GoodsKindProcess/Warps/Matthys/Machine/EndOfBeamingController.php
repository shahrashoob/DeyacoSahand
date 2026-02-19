<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\Matthys\Machine;

use App\Events\Form\PackingLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\Form\Packing\PackingFormLayer;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineTypeOutputBand;
use App\Models\LineProduct\Machine\MachineTypeOutputBandGoodsKind;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Option;
use App\Models\Utility\SmartObject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class EndOfBeamingController extends Controller {
    public static $info = [
        "route"         => "warps.matthys.machine.end_of_beaming.",
        "enable_status" => [ "002" ],
        "button"        => [ "caption" => "پایان برگردان", "class" => "btn-primary" ],
//        "message"       => [ "confirm" => "آیا پایان برگردان اطمینان دارید" ],
        "view_path"     => "goods_kind_process.warps.matthys.machine.end_of_beaming.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.matthys.machine.dashboard.";

    public function __construct() {
        $this->route_path = EndOfBeamingController::$info["route"];
        $this->view_path  = EndOfBeamingController::$info["view_path"];
    }

    public function index( Machine $machine ) {


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


        $machine_type_output_band_goods_kind = MachineTypeOutputBandGoodsKind::
        join( "machine_type_output_bands", "machine_type_output_band_id", "machine_type_output_bands.id" )->
        where( [
            "machine_type_id" => $machine->machine_type_id,
            "goods_kind_id"   => 3 // چله
        ] )->first();

        if ( ! $machine_type_output_band_goods_kind ) {
            return back()->withErrors( "خروجی های ماشین تعریف نشده است، لطفا با مسئول مربوطه تماس بگیرید." );
        }
        $packing_types = $machine_allocation->production->packing_types;
        if ( count( $packing_types ) == 0 ) {
            return back()->withErrors( "نوع بسته بندی مشخص نشده است." );
        }
        $production_form              = $machine->getCurrentProductionForm();
        $production_form_carrier_code = $production_form->carrier->code ?? "";
        if ( $production_form_carrier_code == "" ) {
            return back()->withErrors( "شماره غلطک چله برای فرم تولید ثبت نشده است." );
        }


        // محاسبه مقدار اصلی چله
        switch ( $machine_type_output_band_goods_kind->machine_type_calculation_method_for_unit_id ) {
            case 1:// ورود توسط اپراتور
                $amount = "";
                break;
            case 2: // خوانش از اشیاء
                return back()->withErrors( "امکان خوانش مقدار کنتور شیء هوشمند وجود ندارد" );
                break;
            case 3: // خوانش از مقدار سیستم
                $amount = $allocation->getAllocationAmount();
                break;
        }

        // محاسبه مقدار وزن چله
        switch ( $machine_type_output_band_goods_kind->machine_type_calculation_method_for_sub_unit_id ) {
            case 1:// ورود توسط اپراتور
                $sub_amount = "";
                break;
            case 2: // خوانش از اشیاء
                $result = SmartObject::getContour( $machine_type_output_band_goods_kind->smart_object_for_sub_unit );
                if ( $result["result"] ) {

                    $sub_amount = floatval( $result["data"]["weight"] );
                } else {
                    return back()->withErrors( $result["error"] );
                }
                break;
            case 3: // خوانش از مقدار سیستم
                $sub_amount = $machine_allocation->production->number * $machine_allocation->production->product->weight;
                break;
        }


        $product = $machine_allocation->production->product;

        // این دستور به صورت موفقت است.
        $bom_items = BOMItem::where( "product_id", $product->id )->groupBy( "material_id" )->get();

        // اگر درجه بندی نداریم، باید درجه اصلی در رسته کالایی را انتخاب کنیم
        $main_degree_result = Degree::getMainDegree( 3 ); // چله

        if ( ! $main_degree_result["result"] ) {
            return back()->withErrors( $main_degree_result["error"] );
        }
        $main_degree = $main_degree_result["degree"];

        // تولید لات برای فرم جاری
        Warps::ChangeLot( $allocation, false, $production_form->id );


        return view( $this->view_path . "index", compact( "machine", "product", "amount", "sub_amount",
            "machine_type_output_band_goods_kind", "packing_types",
            "production_form",
            "bom_items", "main_degree" ) );

    }

    public function submit( Request $request, Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $result = EndOfBeamingController::submitHasAnError( $request, $machine );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["error"] );
        }


        $result = EndOfBeamingController::submitConfirm( $request, $result );

        if ( ! $result["result"] ) {
            return back()->withErrors( $result["error"] );
        }


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "پایان برگردان باموفقیت انجام شود و بسته بندی " . $result["packing_form"]->code . " ایجاد گردید." ] );

    }

    public static function submitHasAnError( Request $request, Machine $machine ) {

        $allocation = $machine->getCurrentAllocation();

        $production_form = $machine->getCurrentProductionForm();


        if ( $allocation && ! $production_form ) {
            return [
                "result" => false,
                "error"  => "فرم تولید جاری یافت نشد، لطفا با مسئول مربوطه تماس بگیرید."."<br/>".$allocation->id."  --",
            ];
        }

        if ( $production_form && $production_form->packing_type ) {
            $production_form_item = $production_form->items()->first();

            $result_amount_form_weight = PackingType::getAmountFromWeight( $production_form_item->product, $production_form->packing_type, $request->gross_weight, 0, $production_form->carrier, $request->amount, null, true );
            if ( ! $result_amount_form_weight["result"] ) {
                return $result_amount_form_weight;
            }
            if ( $result_amount_form_weight["final_amount"] <= 0 ) {
                return [
                    "result" => false,
                    "error"  => "مقدار تولید به درستی وارد نشده است."
                ];
            }
            if ( $result_amount_form_weight["weight"] <= 0 ) {
                return [
                    "result" => false,
                    "error"  => "مقدار وزن خالص به درستی وارد نشده است."
                ];
            }
        }

        // اگر درجه بندی نداریم، باید درجه اصلی در رسته کالایی را انتخاب کنیم
        $main_degree_result = Degree::getMainDegree( 3 ); // چله

        if ( ! $main_degree_result["result"] ) {
            return [
                "result" => false,
                "error"  => $main_degree_result["error"]
            ];

        }
        $main_degree = $main_degree_result["degree"];

        return [
            "result"                    => true,
            "allocation"                => $allocation,
            "machine"                   => $machine,
            "production_form"           => $production_form,
            "main_degree"               => $main_degree,
            "result_amount_form_weight" => $result_amount_form_weight ?? null

        ];
    }

    public static function submitConfirm( Request $request, $resultHasAnError ) {

        $allocation                = $resultHasAnError["allocation"];
        $machine                   = $resultHasAnError["machine"];
        $production_form           = $resultHasAnError["production_form"];
        $main_degree               = $resultHasAnError["main_degree"];
        $result_amount_form_weight = $resultHasAnError["result_amount_form_weight"];

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 1000;
        $machineLog->save();


        if ( $allocation ) {

            // تغییر وضعیت فرم تولید و ایجاد یک فرم بسته بندی جدید

            //بسته بندی شده
            // 7202002 = پایان برگردان
            $production_form->ChangeStatus( 7202004, $text = "", $event_id = 7202002 );


            // ایجاد فرم بسته بندی
            $packing_form = PackingForm::create( [
                "packing_type_id" => $production_form->packing_type_id,
                "carrier_id"      => $production_form->carrier_id,
                "status_id"       => 7007005, // در انتظار تحویل به انبار,
                "degree_id"       => $main_degree->id,
                "weight"          => $result_amount_form_weight["weight"],
                "gross_weight"    => $result_amount_form_weight["gross_weight"],
            ] );

            event( new PackingLogEvent( $packing_form, 7007001 ) );

            // چون فقط یک آیتم و یک فرم تولید دارد.
            $production_form_item            = $production_form->items()->first();
            $production_form_item_lot_number = $production_form_item->lot_numbers()->first();

            $production_form_item_lot_number->amount = $result_amount_form_weight["final_amount"];
            $production_form_item_lot_number->save();

            $production_form_item->amount               = $result_amount_form_weight["final_amount"];
            $production_form_item->amount_after_control = $result_amount_form_weight["final_amount"];
            $production_form_item->final_amount         = $result_amount_form_weight["final_amount"];
            $production_form_item->sub_amount           = $result_amount_form_weight["sub_amount"];
            $production_form_item->save();
            // در اینجا اطلاعات فرم جاری کاملا محاسبه شده است و فقط باید بسته بندی ایجاد شود.

            // به ازای هر آیتم بسته بندی یک ردیف ایجاد می کنیم
            $packing_form_item = PackingFormItem::create( [
                "packing_form_id"                    => $packing_form->id,
                "production_form_item_id"            => $production_form_item->id,
                "production_form_item_lot_number_id" => $production_form_item_lot_number->id,
                "fabric_raw_grading_id"              => null,
                "product_id"                         => $production_form_item->product_id,
                "lot_number_id"                      => $production_form_item_lot_number->lot_number_id,
                "degree_id"                          => $main_degree->id,
                "amount"                             => $result_amount_form_weight["final_amount"],
                "amount_after_control"               => $result_amount_form_weight["final_amount"],
                "final_amount"                       => $result_amount_form_weight["final_amount"],
                "sub_amount"                         => $result_amount_form_weight["sub_amount"],
                "init_sub_amount"                         => $result_amount_form_weight["sub_amount"],
                "status_id"                          => 7006003, // بسته بندی شده
                "band_code"                          => $production_form_item->band_code
            ] );
            $packing_form_item->getCode( $production_form_item->band_code, 1 );


            // تغییر وضعیت تخصیص فعلی
            $allocation->status_id = 5310020; //  تخصیص های پایان یافته
            $allocation->save();
            foreach ( $allocation->items as $item ) {
                $item->status_id = 5310020; //  تخصیص های پایان یافته
                $item->save();
            }

            $carrier_status_id = 5320004  //  پر شده در انتظار تحویل به انبار
            ;
            $production_form->carrier->SetStatus(
                $carrier_status_id,
                null,
                5320105,
                null,
                $production_form->machine->id
            );


        }

        $reserve_allocation = $machine->getFirstReserveAllocation();
        if ( $reserve_allocation ) {

            foreach ( $reserve_allocation->items as $item ) {
                $item->status_id             = 5310010; //  تخصیص داده شده
                $item->production_start_date = Carbon::now();
                $item->save();
            }
            $reserve_allocation->status_id = 5310010; //  تخصیص جاری
            $reserve_allocation->save();

            $machine->setStatus(
                null,
                53002, // خاموش
                7203003, //در انتظار پایان قفسه گذاری
                2010 );

            // تغییر کانال جاری ماشین
            ProductionChannel::ChangeChannel( $reserve_allocation );


        } else {

            $machine->setStatus(
                null,
                53002, // خاموش
                7203001, //نداشتن سفارش
                4000 );

        }

        event( new MachineLogEvent( $machine, $machineLog ) );

        if ( $allocation ) {

            // ثبت مقدار مصرف
            MachineAllocationMaterialConsumed::registerNewConsumed( $allocation, $machine, null, $machineLog, $result_amount_form_weight["final_amount"] );

        }

        return [ "result" => true, "packing_form" => $packing_form ?? null ];
    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, EndOfBeamingController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
