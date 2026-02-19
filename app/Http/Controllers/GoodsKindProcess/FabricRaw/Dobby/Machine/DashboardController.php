<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Post\PostStatus;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use function back;
use function redirect;
use function session;
use function view;

class DashboardController extends Controller {
    public static $perfix_production_status_code = "7003";
    var $view_path = "goods_kind_process.fabric_raw.machine.dashboard.";
    var $route_path = "fabric_raw.machine.dashboard.";

    public function __construct() {

        View::share( "perfix_status_code", DashboardController::$perfix_production_status_code );
    }

    public function index( Request $request ) {
        $allowed_status_ids = PostStatus::getAllowedStatus();
        if ( $request->status_id != 0 && ! in_array( $request->status_id, $allowed_status_ids ) ) {
            return back()->withErrors( "شما اجازه دسترسی به مشاهده ماشین با وضعیت انتخاب شده را ندارید" );
        }

        if ( $request->page ) {
            session( [ "page" => $request->page ] );
        }
        if ( session( "page" ) && ! $request->page && $request->isMethod( 'get' ) ) {
            $page = session( "page" );
            if ( $page != 1 ) {
                return redirect( URL::current() . "?page=" . $page );
            }
        }


        if ( $request->isMethod( 'post' ) ) {
            $search          = $request->search;
            $status_id       = $request->status_id;
            $machine_type_id = $request->machine_type_id;
        } else {
            $search          = session( "search_machine" );
            $status_id       = session( "machine_status_id" );
            $machine_type_id = session( "machine_type_id" );
        }
        session( [
            "search_machine"    => $search,
            "machine_status_id" => $status_id,
            "machine_type_id"   => $machine_type_id,
        ] );

        $allowed_machine_ids = Line::getAllowedMachine();
        $allowed_machine_ids[]=-1;
        // search
        if ( $status_id != 0 ) {
            $allowed_status_ids   = [];
            $allowed_status_ids[] = $status_id;
        }

        $list = Machine::join( "stations", "stations.id", "station_id" )->
        when( $search != "", function ( $query ) use ( $search ) {
            return $query->where( function ( $query ) use ( $search ) {
                return $query->where( "machines.code", "like", "%" . $search . "%" )->
                orWhere( "machines.caption", "like", "%" . $search . "%" );
            } );

        } )->
        when( $machine_type_id != 0, function ( $query ) use ( $machine_type_id ) {
            return $query->where( "machine_type_id", $machine_type_id );
        } )->
        whereIn( "production_status_id", $allowed_status_ids )->
        whereIn( "machines.id", $allowed_machine_ids )->
        where( "machines.active_status_id", 1200 )->
        select( "machines.id", "machines.code", "machines.caption", "machines.station_id", "machine_type_id", "production_status_id" )->
        paginate( 50 );;

        $status_option = Option::get( "status", $status_id, 7003 );

        return view( $this->view_path . "index", compact( "list", "search", "status_option" ) );
    }

    public function view( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $controller_info   = DashboardController::get_controller_info( "view" );
        $special_condition = DashboardController::enable_special_condition( $machine );

        $allocation = $machine->getCurrentAllocation();

        $reserve_allocation      = $machine->ReserveAllocation()->get();
        $reserve_allocation_list = [];
        foreach ( $reserve_allocation as $allocation ) {
            foreach ( $allocation->items as $item ) {
                $reserve_allocation_list[ $item->production_id ] = $item;
            }

        }

        $productionFromItemLot = FabricRaw::getCurrentLot( $allocation );


        $production_form = ProductionForm::
        whereIn( "status_id", [
            7002001, // در حال تکمیل
            7002007, // در انتظار استخراج پارچه پایانی
            7002008, // در حال تکمیل پارچه پایانی
            7002009  // در حال تغییر نخ پود (دستور توقف)
        ] )->
        where( "machine_id", $machine->id )->
        first();

        $production_form_carrier_code = $production_form->carrier->code ?? "---";

        $current_input_list = CurrentMachineInput::
        where( [ "machine_id" => $machine->id, "allocation_id" => ( $allocation->id ?? 0 ), "goods_kind_id" => 3 ] )->
        orderBy( "input_line_code" )->
        get();
        $carrier_codes      = "";

        if ( $current_input_list ) {
            foreach ( $current_input_list as $item ) {
                $carrier_codes .= " ورودی" . $item->input_line_code . ": " . ( $item->carrier->code ?? "---" ) . ", ";
            }
        }

        $form_list = Form::where( [
            "applicant_type_id" => 10, // ماشین
            "applicant_id"      => $machine->id
        ] )->paginate( 10 );


        return view( $this->view_path . "view_card", compact( "carrier_codes", "production_form_carrier_code", "productionFromItemLot", "allocation", "machine", "controller_info", "special_condition", "reserve_allocation_list", "form_list" ) );
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

        $allowed_status_ids = PostStatus::getAllowedStatus();
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

        // $design_form  = FabricRawDesignForm::getDesignFormFromMachine( $machine );
        $result = [];
        //$result["12"] = isset( $design_form->design_available ) ? ! $design_form->design_available : false;

        $allocation = $machine->getCurrentAllocation();

        if ( isset( $allocation ) ) {
            $warps_request_form = WarpsRequestForm::where( [
                "status_id"     => 7005004, // در انتظار تایید درخواست کننده
                "allocation_id" => $allocation->id
            ] )->first();
        }

        $result["20"] = isset( $warps_request_form );
        $result["21"] = isset( $warps_request_form );


        return $result;


    }

    public function change_lot_confirmation( Machine $machine ) {

        $allocation            = $machine->getCurrentAllocation();
        $productionFromItemLot = FabricRaw::getCurrentLot( $allocation, true );

        return view( $this->view_path . "change_lot_confirmation", compact( "machine", "productionFromItemLot" ) );

    }

    public static function get_controller_info( $type = "" ) {
        $controller_info = [
            "01" => BeginIntroChangeLotController::$info,
            "02" => EndOfIntroChangeLotController::$info,
            "03" => EndOfStep2ChangeLogController::$info,
            "04" => BeginWarpingController::$info,
            "05" => EndWarpingController::$info,
            "06" => BeginKnottingController::$info,
            "07" => EndOfKnottingController::$info,
            "08" => BeginPinningController::$info,
            "09" => EndOfPinningController::$info,
            "10" => LaunchChangeLotController::$info,
            "11" => ConfirmQualityControlController::$info,
            "12" => RejectQualityControlKnottingController::$info,
            "13" => LaunchShiftController::$info,
            "14" => ChangeYarnLotController::$info,
            "15" => RequestChangeWarpsController::$info,
            "16" => BeginChangeWarpsController::$info,
            "17" => EndOfChangeWarpsController::$info,
            "18" => BeginWarpingForChangeWarpsController::$info,
            "19" => EndOfWarpingForChangeWarpsController::$info,
            "20" => WarpsDeliveryConfirmationController::$info,
            "21" => WarpsDeliveryRejectController::$info,
            "22" => LogController::$info,
            "23" => DeclarationEndOfWarpsController::$info,
            "24" => ChangeYarnForChangeDesignController::$info,
            "25" => BeginChangeWarpsForChangeDesignController::$info,
            "26" => EndOfChangeWarpsForChangeDesignController::$info,
            "27" => BeginWarpingForChangeDesignController::$info,
            "28" => EndOfWarpingForChangeDesignController::$info,
            "29" => LaunchShiftForChangeDesignController::$info,
            "30" => ProductionCardStopOrderController::$info,
            "31" => BeginChangeYarnForStopOrderController::$info,
            "32" => EndOfChangeYarnForStopOrderController::$info,
            "33" => FabricProfileCardController::$info,
            "35" => PreparationForPiningController::$info,
            "36" => BeginWarpsExtractionController::$info,
            "37" => EndOfWarpsExtractionController::$info,
            "38" => WarpsDeliveryToWarehouseController::$info,
            "39" => RequestChangeWarpsCancelController::$info,
//            "40" => EditLogController::$info,
        ];
//         if($type=="view"){
//             unset($controller_info["40"]);
//         }
        return $controller_info;
    }
}
