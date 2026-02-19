<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Packing;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\Post\PostStatus;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use function back;
use function session;
use function view;

class DashboardController extends Controller
{
    public static $perfix_packing_status_code = "7007";

    var $view_path = "goods_kind_process.fabric_raw.packing.dashboard.";
    var $route_path = "fabric_raw.packing.dashboard.";

    public function __construct() {

        View::share( "perfix_status_code", DashboardController::$perfix_packing_status_code );
        View::share( "view_path", $this->view_path );
        View::share( "route_path", $this->route_path );
    }
    public function index( Request $request ) {

        $allowed_status_ids = PostStatus::getAllowedStatus();
        if ( $request->status_id != 0 && ! in_array( $request->status_id, $allowed_status_ids ) ) {
            return back()->withErrors( "شما اجازه دسترسی به مشاهده فرم با وضعیت انتخاب شده را ندارید" );
        }

        if ( $request->isMethod( 'post' ) ) {
            $search   = $request->search;
            $status_id = $request->status_id;
            $order_by = $request->order_by;
            $degree_id = $request->degree_id;
        } else {
            $search   = session( "search_packing2" );
            $status_id = session( "packing2_status_id" );
            $order_by = session( "order_by_packing2" );
            $degree_id = session( "degree_id_packing2" );
        }
        session( [
            "search_packing2"   => $search,
            "packing2_status_id" => $status_id,
            "order_by_packing2" => $order_by,
            "degree_id_packing2" => $degree_id,
        ] );

        // search
        if ( $status_id != 0 ) {
            $allowed_status_ids   = [];
            $allowed_status_ids[] = $status_id;
        }

        $list = PackingForm::
        when( $search != "", function ( $query ) use ( $search ) {
            return $query->where( function ( $query ) use ( $search ) {
                return $query->where( "code", "like", "%" . $search . "%" );
            } );

        } )->
        when( $degree_id != "", function ( $query ) use ( $degree_id ) {
            return $query->where( "degree_id", $degree_id );
        } )->
        whereIn( "status_id", $allowed_status_ids )->
        orderBy( "created_at", "desc" )-> /////////////////
        paginate( 50 );;
        $order_by_Option = Option::OrderBy( "public", $order_by );
        $status_option = Option::get( "status", $status_id, 7007 );
        $degree_Option=Option::get("degree",$degree_id, 4 ); // 3 => پارچه خام

        return view( $this->view_path . "index", compact( "list", "search","status_option","degree_Option", "order_by_Option" ) );
    }

    public function view( PackingForm $packing_form ) {

        $result=$this->checkPermission( $packing_form );
        if ( $result != "" ) {
            return $result;
        }
        $controller_info=DashboardController::get_controller_info();
        $cancel_item_route="fabric_raw.packing.cancel_packing_form_item.index";

        return view( $this->view_path . "view", compact( "packing_form","controller_info" ,"cancel_item_route") );
    }

    public static function checkPermissionConditions( PackingForm $packing_form, $info = false ) {

        $allowed_status_ids = PostStatus::getAllowedStatus();
        if ( !in_array($packing_form->status_id, $allowed_status_ids ) ) {
            return [
                "result"  => false,
                "message" => "شما اجازه مشاهده فرم را ندارید.",
            ];
        }

        if ( $info != false ) {
            foreach ( $info["enable_status"] as &$value ) {
                $value = DashboardController::$perfix_packing_status_code . $value;
            }
            unset( $value );
            if ( ! in_array( $packing_form->status_id,  $info["enable_status"] ) ) {
                return [
                    "result"  => false,
                    "message" => "وضعیت فرم بسته بندی جهت عملیات نامعتبر است",
                ];
            }
            $post_user = Auth::user()->posts->first();
            if(!$post_user->checkButtonPermission($info["route"]."index")){
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

    public function checkPermission( PackingForm $packing_form ) {
        $result = DashboardController::checkPermissionConditions( $packing_form );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

    }

    public static function get_controller_info() {
        return $controller_info = [
            "01"=>DeliveryToWarehouseController::$info,
            "02" => ChangeCarrierCodeController::$info,
            "03" => CancelPackingFormController::$info,
            "04" => CancelPackingFormItemController::$info
        ];
    }

}
