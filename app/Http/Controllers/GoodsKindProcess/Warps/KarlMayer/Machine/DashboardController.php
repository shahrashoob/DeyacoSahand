<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer\Machine;

use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Post\PostStatus;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller {
    public static $perfix_production_status_code = "7203";
    public static $info = [
        "route" => "warps.karl_mayer.machine.dashboard."
    ];
    var $view_path = "goods_kind_process.warps.karl_mayer.machine.dashboard.";
    var $route_path = "production.machine.";

    public function __construct() {

        View::share( "perfix_status_code",self::$perfix_production_status_code );
    }

    public function view( Machine $machine ) {


        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $controller_info   = self::get_controller_info( "view" );
        $special_condition = self::enable_special_condition( $machine );

        $allocation = $machine->getCurrentAllocation();


        $reserve_allocation      = $machine->ReserveAllocation()->get();
        $reserve_allocation_list = [];
        foreach ( $reserve_allocation as $r_allocation ) {
            foreach ( $r_allocation->items as $item ) {
                $reserve_allocation_list[ $item->production_id ] = $item;
            }

        }

        $productionFromItemLot = null;
        if ( $allocation ) {
            $productionFromItemLot = Warps::getCurrentLot( $allocation );

            // اگر به هر دلیل لات تولید نشده بود، دوباره لات را ایجاد می کند.
            if ( count( $productionFromItemLot ) == 0 ) {
                FabricRaw::ChangeLot( $allocation );
                $productionFromItemLot = Warps::getCurrentLot( $allocation );
            }


        }


        $production_form         = $machine->getCurrentProductionForm();


        $production_form_carrier_code         = ($production_form->carrier->code ?? "---")." (".($production_form->carrier->carrier_type->caption ?? "").")";
        $production_form_reserve_carrier_code = ($production_form_reserve->carrier->code ?? null)." ".($production_form_reserve->carrier->carrier_type->caption ?? "");

        $current_input_list = CurrentMachineInput::
        where( [ "machine_id" => $machine->id, "allocation_id" => ( $allocation->id ?? - 1 ) ] )->
        orderBy( "goods_kind_id" )->
        orderBy( "input_line_code" )->
        get();


        $form_list = Form::where( [
            "applicant_type_id" => 10, // ماشین
            "applicant_id"      => $machine->id
        ] )->paginate( 10 );


        return view( $this->view_path . "view", compact(
            "production_form_reserve_carrier_code",
            "current_input_list", "production_form_carrier_code",
            "productionFromItemLot",
            "allocation",
            "machine",
            "controller_info",
            "special_condition",
            "reserve_allocation_list",
            "form_list"
        ) );
    }

    public function short_link( Machine $machine ) {

        $controller_info   = self::get_controller_info( "view" );
        $special_condition = self::enable_special_condition( $machine );

        $current_allocation = $machine->getCurrentAllocation();

        $reserve_allocation_list    = [];
        $current_input_list         = [];
        $current_machine_allocation = null;
        if ( $current_allocation ) {
            $current_machine_allocation                                       = $current_allocation->items()->first();
            $current_input_list[ $current_machine_allocation->production_id ] = CurrentMachineInput::
            where( [
                "machine_id"    => $machine->id,
                "allocation_id" => ( $current_allocation->id ?? - 1 ),
                "goods_kind_id" => 2
            ] )->
            orderBy( "input_line_code" )->
            get();
        }

        $reserve_allocation = $machine->ReserveAllocation()->get();


        foreach ( $reserve_allocation as $r_allocation ) {
            foreach ( $r_allocation->items as $machine_allocation ) {
                $reserve_allocation_list[ $machine_allocation->production_id ] = $machine_allocation;


                $current_input_list[ $machine_allocation->production_id ] = CurrentMachineInput::
                where( [
                    "machine_id"    => $machine->id,
                    "allocation_id" => ( $r_allocation->id ?? - 1 ),
                    "goods_kind_id" => 2
                ] )->
                orderBy( "input_line_code" )->
                get();
            }

        }
        $software_name = Setting::getStringValue( "software_name" );

        return view( $this->view_path . "short_link.index", compact( "controller_info", "special_condition", "machine", "current_input_list", "reserve_allocation_list", "software_name", "current_machine_allocation", "current_allocation" ) );
    }

    public function checkPermission( Machine $machine ) {
        $result = DashboardController::checkPermissionConditions( $machine );
        if ( ! $result["result"] ) {
            $message = \Session::get( 'success' );
            if ( isset( $message ) ) {
                return redirect()->route( $this->route_path . "index" )->with( [ "success" => $message ] );
            }

            return redirect()->route( $this->route_path . "index" )->withErrors( $result["message"] );
        }

    }

    public static function checkPermissionConditions( Machine $machine, $info = false, $all_status = false ) {

        $allowed_status_ids = PostStatus::getAllowedStatus( 3 );
        if ( ! in_array( $machine->production_status_id, $allowed_status_ids ) ) {
            return [
                "result"  => false,
                "message" => "شما اجازه مشاهده ماشین را ندارید.",
            ];
        }

        $allowed_machine_ids = Line::getAllowedMachine();
        if ( ! in_array( $machine->id, $allowed_machine_ids ) ) {
            return [
                "result"  => false,
                "message" => "شما اجازه مشاهده ماشین را ندارید.",
            ];
        }
        if ( $info != false ) {
            foreach ( $info["enable_status"] as &$value ) {
                $value = DashboardController::$perfix_production_status_code . $value;
            }
            unset( $value );
            if ( ! $all_status && ! in_array( $machine->production_status_id, $info["enable_status"] ) ) {
                return [
                    "result"     => false,
                    "message"    => "وضعیت ماشین جهت عملیات نامعتبر است",
                    "error_type" => "for_machine_status"
                ];
            }

            $post_user = Auth::user()->posts->first();
            if ( ! $post_user->checkButtonPermission( $info["route"] . "index" ) ) {
                return [
                    "result"  => false,
                    "message" => "دسترسی  عملیات برای شما تعریف نشده است",
                ];
            }
        }

        return [
            "result" => true,
        ];

    }

    public static function enable_special_condition( Machine $machine ) {

        $result = [];
//        $result["05"] = isset( $warps_request_form );

        // فرم در انتظار تایید انبارک
        $product_request_form_count = ProductRequestForm::where( [
            "applicant_type_id" => 40,
            "applicant_id"      => $machine->warehouse_id,
            "status_id"         => 7005004,// در انتظار تایید برگ خروج
        ] )->count();
        $result["06"]               = $product_request_form_count > 0;
        $result["07"]               = $product_request_form_count > 0;
        return $result;


    }


    public static function get_controller_info( $type = "" ) {
        $controller_info = [
            "03" => EndOfShelvingController::$info,
            "04" => EndOfWarpingController::$info,
            "05" => StartWarpingController::$info,
            "06" => MaterialDeliveryConfirmationController::$info,
            "07" => MaterialDeliveryRejectController::$info,
            "08" => InjectionOfMaterialController::$info,
            "09" => MaterialReturnToWarehouseController::$info,
            "10" => WasteCollectionController::$info,
            "11" => RequestRawMaterialController::$info,
            "12" => FailureShelvingController::$info,
            "13" => MaterialReturnToWarehouseLogController::$info,
//
            "97" => AllocationCardController::$info,
            "98" => FinishedAllocationController::$info,
            "99" => LogController::$info,
        ];

        return $controller_info;
    }
}

