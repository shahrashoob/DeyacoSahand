<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Form\PackingLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialReturnToWarehouseController;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeginWarpsExtractionAndPutController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.begin_warps_extraction_and_put.",
        "enable_status" => [ "044" ],
        "button"        => [ "caption" => "شروع استخراج چله و چله گذاری", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.jacquard.machine.begin_warps_extraction_and_put.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginWarpsExtractionAndPutController::$info["route"];
        $this->view_path  = BeginWarpsExtractionAndPutController::$info["view_path"];
    }

    public function index( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $result = self::GetRemainingOfWarps( $machine );

        $packing_form_ids = [ - 1 ];
        if ( $result["result"] ) {
            $packing_form_ids = $result["packing_form_ids"];
        }

        $packing_form_list = PackingForm::whereIn( "id", $packing_form_ids )->get();

        return view( $this->view_path . "index", compact( "machine", "packing_form_list" ) );

    }

    public static function GetRemainingOfWarps( $machine ) {
        $terminate_allocation = Allocation::where( [
            "machine_id" => $machine->id,
            "status_id"  => 5310020, // تخصیص خاتمه یافته قبلی
        ] )->
        orderByDesc( "id" )->
        first();

        if ( ! $terminate_allocation ) {
            return [
                "result"  => false,
                "warning" => "تخصیص خاتمه یافته ای وجود ندارد."
            ];
        }
        $packing_form_ids = CurrentMachineInput::where( "allocation_id", $terminate_allocation->id )->
        where( "goods_kind_id", 3 )->
        pluck( "packing_form_id" )->
        toArray();
        if ( count( $packing_form_ids ) == 0 ) {
            return [
                "result"  => false,
                "warning" => "ورودی چله خالی است."
            ]; // ورودی جاری چله ندارد،
        }


        return [
            "result"               => true,
            "packing_form_ids"     => $packing_form_ids,
            "terminate_allocation" => $terminate_allocation
        ];
    }

    public function submit( Request $request, Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        $message                 = "";
        $warps_remaining_checked = Setting::getIntegerValue( "warps_remaining_checked" );

        $result               = self::GetRemainingOfWarps( $machine );
        $packing_form_ids     = [ - 1 ];
        $data                 = [];
        $terminate_allocation = null;
        if ( $result["result"] ) {
            $packing_form_ids     = $result["packing_form_ids"];
            $terminate_allocation = $result["terminate_allocation"];
        }
        $packing_form_list = PackingForm::whereIn( "id", $packing_form_ids )->get();
        if ( count( $packing_form_list ) > 0 ) {


            foreach ( $packing_form_list as $item ) {
                if ( ! isset( $request["consumed"][ $item->id ] ) ) {
                    $message .= " لطفا وضعیت مصرف بسته بندی " . $item->code . "  را مشخص نمایید." . "<br/>";
                }
                if ( isset( $request["consumed"][ $item->id ] ) && $request["consumed"][ $item->id ] == 6021102 ) { //  مصرف شده
                    if ( ! isset( $request["amount"][ $item->id ] ) ) {
                        $message .= " لطفا مقدار نهایی باقی مانده بسته بندی " . $item->code . "  را مشخص نمایید." . "<br/>";
                    } elseif ( $request["amount"][ $item->id ] < 0 || $request["amount"][ $item->id ] > $item->getAmount() ) {
                        $message .= "  مقدار نهایی باقی مانده بسته بندی " . $item->code . "  معتبر نمی باشد. " . "<br/>";
                    }
                }

                if ( $message == "" ) {
                    $packing_form_item          = $item->items()->first();
                    $packing_type_weight_result = PackingType::getWeight( $item->packing_type, $item->carrier );

                    // به دست آوردن وزن ناخالص
                    if ( $packing_type_weight_result["result"] ) {
                        $data[ $item->id ]["gross_weight"] = $packing_type_weight_result["weight"] + $request["amount"][ $item->id ];
                    } else {
                        $message .= $packing_type_weight_result["error"] . "<br/>";
                    }
                    $data[ $item->id ]["consumed_status_id"]      = $request["consumed"][ $item->id ];
                    $data[ $item->id ]["sub_packing_form_number"] = 0;
                    $data[ $item->id ]["weight"]                  = $packing_form_item->product->weight * $request["amount"][ $item->id ];
                    $data[ $item->id ]["amount"]                  = $request["amount"][ $item->id ];
                    $data[ $item->id ]["carrier"]                 = $item->carrier;
                }
            }
        }
        if ( $message != "" ) {
            return back()->withErrors( $message );
        }


// دریافت قطب ها
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


        if($terminate_allocation) {
            $result_modification = GeneralMaterialReturnToWarehouseController::AddNewModificationForPacking($machine, $packing_form_ids, $data, $terminate_allocation);
            if (!$result_modification["result"]) {
                $message .= $result_modification["error"];
            }
        }


        if ( $message != "" ) {
            return back()->withErrors( $message );
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 530; // شروع استخراج چله و چله گذاری (جهت تغییر کالیته)
        $machineLog->contour_1_value       = $request->contour_1_value * $contour_result["ratio"];
        $machineLog->contour_2_value       = $request->contour_2_value * $contour_result["ratio"];
        $machineLog->contour_3_value       = $request->contour_3_value * $contour_result["ratio"];
        $machineLog->contour_4_value       = $request->contour_4_value * $contour_result["ratio"];
        $machineLog->contour_5_value       = $request->contour_5_value * $contour_result["ratio"];

//        // استخراج چله های موجود
//        $product_request_form = ProductRequestForm:: getLatestRequestForm( $machine->warehouse_id, 40 );
//        if ( $product_request_form ) {
        // بررسی درست بودن درجه چله ها
//            foreach ( $product_request_form->items as $item ) {
//                $id                       = "item_" . $item->id;
//                $degree_list[ $item->id ] = Degree::find( $request->$id );
//                if ( ! isset( $request->$id ) || ! $degree_list[ $item->id ] ) {
//                    $message .= "درجه چله ورودی" . $item->input_line_code . " نامعتبر است." . "<br/>";
//                }
//                if ( $degree_list[ $item->id ] && ! $degree_list[ $item->id ]->warehouse ) {
//                    $message .= "انبار چله ورودی" . $item->input_line_code . " نامعتبر است." . "<br/>";
//                }
//
//                $degree_list["input_line_code"][ $item->input_line_code ] = $degree_list[ $item->id ];
//
//            }
//
//            if ( $message != "" ) {
//                return back()->withErrors( $message );
//            }
//            // بررسی مقدار باقی مانده چله با توجه به قطب ها
//            if ( $warps_remaining_checked ) {
//                foreach ( $product_request_form->items as $item ) {
//
//                    $result = Warps::getRemainingAmountOfWarps( $product_request_form, $item );
//                    if ( ! $result["result"] ) {
//                        $message .= $result["message"] . "<br/>";
//                    }
//                    $amount_list[ $item->input_line_code ] = $result;
//                }
//            } else {
        // نیاز نیست متراژ باقیمانده چله بررسی شود.
//            foreach ( $product_request_form->forms as $prf_forms ) {
//                foreach ( $prf_forms->form->item as $form_item ) {
//                    if ( $form_item->carrier ) {
//                        $form_item->carrier->SetStatus( 5320001, null, 5320103, null, $machine->id );
//                    }
//                }
//            }
//            }
//            // نیاز نیست متراژ باقیمانده چله بررسی شود.
//            if ( ! $warps_remaining_checked ) {
//            event( new MachineLogEvent( $machine, $machineLog, "", false, $last_row_log ) );
//
//            $machine->setStatus(
//                null,
//                53002,
//                7003045, // در حال استخراج چله (جهت تغییر کالیته)
//                1760, // استخراج چله و چله گذاری (جهت تغییر کالیته)
//                "Fabric_Raw"
//            );
//
//            return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );
//            }
//            if ( $message != "" ) {
//                $machineLog->delete();
//
//                return back()->withErrors( $message );
//            }
//
//            if ( ! isset( $amount_list[1] ) && ! $amount_list[1]["result"] ) {
//                return back()->withErrors( "مقدار محاسبه شده برای باقیمانده چله معتبر نمی باشد. " );
//            }
//
//
//            // ایجاد فرم بسته بندی  با وضعیت در انتظار تحویل به انبار
//            foreach ( $product_request_form->forms as $prf_forms ) {
//                foreach ( $prf_forms->form->item as $form_item ) {
//
//                    if ( $amount_list[ $form_item->io_line_code ?? 1 ]["amount"] > 0 ) {
//                        // ایجاد فرم بسته بندی
//                        $packing_form = PackingForm::create( [
//                            "carrier_id"          => $form_item->carrier->id,
//                            "form_id"             => 0,
//                            "status_id"           => 7007005,// در انتظار تحویل به انبار
//                            "packing_type_id"     => $form_item->packing_type->id,
//                            "degree_id"           => $degree_list["input_line_code"][ $form_item->io_line_code ?? 1 ]->id,
//                            "warehouse_status_id" => 4202,// بسته خارج انبار است
//
//                        ] );
//                        $packing_cods .= $packing_form->getCode() . ", ";
//
//                        $packing_form_item = PackingFormItem::create( [
//                            "packing_form_id"      => $packing_form->id,
//                            "amount"               => $amount_list[ $form_item->io_line_code ?? 1 ]["amount"],
//                            "final_amount"         => $amount_list[ $form_item->io_line_code ?? 1 ]["amount"],
//                            "amount_after_control" => $amount_list[ $form_item->io_line_code ?? 1 ]["amount"],
//                            "status_id"            => 7006003, // بسته بندی شده
//                            "sub_amount"           => 0,
//                            "product_id"           => $form_item->product_id,
//                            "degree_id"            => $degree_list["input_line_code"][ $form_item->io_line_code ?? 1 ]->id,
//                            "lot_number_id"        => $form_item->lot_number_id,
//                            "band_code"            => 1,
//
//                        ] );
//
//                        event( new PackingLogEvent( $packing_form, "7007001" ) );
//                        if ( $form_item->carrier ) {
//                            $form_item->carrier->SetStatus( 5320004, null, 5320103, null, null ); // پر شده در انتظار تحویل به انبار
//                        }
//                    } else {
//                        $form_item->carrier->SetStatus( 5320001, null, 5320103, null, null );
//                    }
//                }
//            }
//        }


        event( new MachineLogEvent( $machine, $machineLog, $packing_cods = "", false, $last_row_log ) );

        $machine->setStatus(
            null,
            53002,
            7003045, // در حال استخراج چله (جهت تغییر کالیته)
            1760, // استخراج چله و چله گذاری (جهت تغییر کالیته)
            "Fabric_Raw"
        );

        $production_form = $machine->getCurrentProductionForm();
        if ( $production_form ) {
            // بروزرسانی مقادیر فرم تولید
            $last_machine_log = MachineLog::getLastLogWithContour( $machine );
            ProductionForm::UpdateAmountWithLastContour( $production_form, $last_machine_log );
        }

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, BeginWarpsExtractionAndPutController::$info, true );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

    // foreach ( $product_request_form->items as $item ) {
//                $form = Form::create(
//                    [
//                        "production_card_id" => $item->production_id,
//                        "user_id"            => Auth::user()->id,
//                        "status_id"          => 500000400, // در انتظار تحویل به انبار
//                        "form_type_id"       => 305,
//                        "warehouse_id"       => $degree_list[ $item->id ]->warehouse_id,
//                        "trans_kind"         => 2, // دریافت از تولید
//                        "ic"                 => 1, // بافندگی
//                        "applicant_type_id"  => 10, // ماشین
//                        "applicant_id"       => $machine->id
//                    ]
//                );
//                $form->getCode( "DCBWF" ); //Back Warp
//                FormItem::create( [
//                    "form_id"         => $form->id,
//                    "product_id"      => $item->product_id,
//                    "amount"          => $amount_list[ $item->id ]["amount"],
//                    "sub_amount"      => $amount_list[ $item->id ]["sub_amount"],
//                    "packing_type_id" => $item->warehouse_product->packing_type_id,
//                    "carrier_id"      => $item->warehouse_product->carrier_id,
//                    "degree_id"       => $degree_list[ $item->id ]->id,
//                    "lot_number_id"   => $item->warehouse_product->lot_number_id
//                ] );
//                event( new FormLogEvent( $form ) );
//
//                // تغییر وضعیت حامل، به در انتظار تحویل به انبار
//                $item->warehouse_product->carrier->SetStatus( 5320004, " استخراج چله -" . $machine->fullCaption() );
    //  }
}
