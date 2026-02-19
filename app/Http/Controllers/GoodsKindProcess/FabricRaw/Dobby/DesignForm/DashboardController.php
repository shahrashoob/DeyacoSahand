<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\DesignForm;

use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\Post\PostStatus;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;


class DashboardController extends Controller {
    public static $perfix_design_form_status_code = "7004";
    var $view_path = "goods_kind_process.fabric_raw.design_form.dashboard.";
    var $route_path = "fabric_raw.design_form.dashboard.";

    public function __construct() {

        View::share( "perfix_status_code", DashboardController::$perfix_design_form_status_code );
        View::share( "view_path", $this->view_path );
        View::share( "route_path", $this->route_path );
    }

    public function index( Request $request ) {

        $allowed_status_ids = PostStatus::getAllowedStatus();

        if ( $request->status_id != 0 && ! in_array( $request->status_id, $allowed_status_ids ) ) {
            return back()->withErrors( "شما اجازه دسترسی به مشاهده فرم با وضعیت انتخاب شده را ندارید" );
        }

        if ( $request->isMethod( 'post' ) ) {
            $search    = $request->search;
            $status_id = $request->status_id;
            $order_by  = $request->order_by;
        } else {
            $search    = session( "search_design_form" );
            $status_id = session( "design_form_status_id" );
            $order_by  = session( "order_by_design_form" ) ?? "updated_at__desc";
        }
        session( [
            "search_design_form"    => $search,
            "design_form_status_id" => $status_id,
            "order_by_design_form"  => $order_by,
        ] );

        // search
        if ( $status_id != 0 ) {
            $allowed_status_ids   = [];
            $allowed_status_ids[] = $status_id;
        }

        $list = FabricRawDesignForm::when( $search != "", function ( $query ) use ( $search ) {
            return $query->where( function ( $query ) use ( $search ) {
                return $query->where( "code", "like", "%" . $search . "%" );
            } );

        } )->
        when( $order_by != "", function ( $query ) use ( $order_by ) {
            $order_by = Str::of( $order_by )->explode( "__" );

            return $query->orderBy( $order_by[0], $order_by[1] );
        } )->
        whereIn( "status_id", $allowed_status_ids )->
        paginate( 50 );

        $order_by_Option = Option::OrderBy( "fabric_raw_design_form", $order_by );
        $status_option = Option::get( "status", $status_id, 7004 );
        return \view( $this->view_path . "index", compact( "list", "order_by_Option", "search","status_option" ) );
    }

    public function view( FabricRawDesignForm $design_form ) {

        $result=$this->checkPermission( $design_form );
        if ( $result != "" ) {
            return $result;
        }
        $controller_info   = DashboardController::get_controller_info();
        $special_condition = $this->enable_special_condition($design_form);

        return \view( $this->view_path . "view", compact( "design_form", "controller_info", "special_condition" ) );

    }

    public static function checkPermissionConditions( FabricRawDesignForm $design_form, $info = false ) {

        $allowed_status_ids = PostStatus::getAllowedStatus();
        if ( !in_array($design_form->status_id, $allowed_status_ids ) ) {
            return [
                "result"  => false,
                "message" => "شما اجازه مشاهده فرم را ندارید.",
            ];
        }
        if ( $info != false ) {
            foreach ( $info["enable_status"] as &$value ) {
                $value = DashboardController::$perfix_design_form_status_code . $value;
            }
            unset( $value );
            if ( ! in_array( $design_form->status_id, $info["enable_status"] ) ) {
                return [
                    "result"  => false,
                    "message" => "وضعیت فرم طراحی جهت عملیات نامعتبر است",
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

    public function checkPermission( FabricRawDesignForm $design_form ) {
        $result = DashboardController::checkPermissionConditions( $design_form );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

    }

    public static function get_controller_info() {
        return $controller_info = [
            "01" => StartDesigning::$info,
            "02" => EndOfDesigning::$info,
            "03" => WarpsDeliveryConfirmationController::$info,
        ];
    }

    public static function enable_special_condition( FabricRawDesignForm $design_form ) {

        $warps_request_form=WarpsRequestForm::where([
            "status_id"=>7005004, // در انتظار تایید درخواست کننده
            "allocation_id"=>$design_form->allocation_id
        ])->first();

        $result["03"]=isset($warps_request_form) ;


        return $result;


    }

}
