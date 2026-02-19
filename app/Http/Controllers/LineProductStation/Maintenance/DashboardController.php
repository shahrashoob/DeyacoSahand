<?php

namespace App\Http\Controllers\LineProductStation\Maintenance;

use App\Events\Machine\MachineLogEvent;
use App\Events\Machine\MaintenanceLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\Maintenance\Maintenance;
use App\Models\Post\PostStatus;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class DashboardController extends Controller {
    public static $perfix_status_code = "6003";
    var $view_path = "line_product_station.maintenance.dashboard.";
    var $route_path = "line_product_station.maintenance.dashboard.";

    public function __construct() {

        View::share( "view_path", $this->view_path );
        View::share( "route_path", $this->route_path );
    }

    public function index( Request $request ) {

        if ( $request->isMethod( 'post' ) ) {
            $search    = $request->search;
            $order_by  = $request->order_by;
            $status_id = $request->status_id;
        } else {
            $search    = session( "search_maintenance" );
            $status_id = session( "maintenance_status_id" );
            $order_by  = session( "order_by_maintenance" ) ?? "created_at__desc";
        }
        session( [
            "search_maintenance"    => $search,
            "maintenance_status_id" => $status_id,
            "order_by_maintenance"  => $order_by,
        ] );

        $list = Maintenance::when( $search != "", function ( $query ) use ( $search ) {
            return $query->where( function ( $query ) use ( $search ) {
                return $query->where( "code", "like", "%" . $search . "%" );
            } );

        } )->
        when( $order_by != "", function ( $query ) use ( $order_by ) {
            $order_by = Str::of( $order_by )->explode( "__" );

            return $query->orderBy( $order_by[0], $order_by[1] );
        } )->
        paginate( 50 );

        $order_by_Option = Option::OrderBy( "maintenance", $order_by );

        $status_option = Option::get( "status", $status_id, 6003 );

        return \view( $this->view_path . "index", compact( "list", "order_by_Option", "search", "status_option" ) );
    }

    public function view( Maintenance $maintenance ) {
//        $result = $this->checkPermission( $maintenance );
//        if ( $result != "" ) {
//            return $result;
//        }
        return \view( $this->view_path . "view", compact( "maintenance" ) );
    }

    public function submit_start( Request $request, Maintenance $maintenance ) {

        if ( $maintenance->status_id != 6003001 ) { // در انتظار شروع
            return back()->withErrors( "درخواست نت در انتظار شروع نمی باشد." );
        }


        $result = Maintenance::UpdateMaintenance( $maintenance, "start" );
        if ( $result["result"] ) {

            return redirect()->route( $this->route_path . "index" )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );


        } else {
            return back()->withErrors( $result["error"] );
        } }

    public function submit_end( Request $request, Maintenance $maintenance ) {


        if ( $maintenance->status_id != 6003002 ) { // در حال انجام
            return back()->withErrors( "درخواست نت در حال انجام نمی باشد." );
        }

        $result = Maintenance::UpdateMaintenance( $maintenance, "end" );
        if ( $result["result"] ) {

            return redirect()->route( $this->route_path . "index" )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );


        } else {
            return back()->withErrors( $result["error"] );
        }



    }

    public function confirm_maintenance( Request $request, Maintenance $maintenance ) {

        if ( $maintenance->status_id != 6003004 ) { // در انتظار تایید
            return back()->withErrors( "درخواست نت در انتظار تایید نمی باشد." );
        }

        $result = Maintenance::UpdateMaintenance( $maintenance, "confirm" );
        if ( $result["result"] ) {

            return redirect()->route( $this->route_path . "index" )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );


        } else {
            return back()->withErrors( $result["error"] );
        }
    }

    public function reject_maintenance( Request $request, Maintenance $maintenance ) {

        if ( $maintenance->status_id != 6003004 ) { // در انتظار تایید
            return back()->withErrors( "درخواست نت در انتظار تایید نمی باشد." );
        }

        $result = Maintenance::UpdateMaintenance( $maintenance, "reject" );
        if ( $result["result"] ) {

            return redirect()->route( $this->route_path . "index" )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );


        } else {
            return back()->withErrors( $result["error"] );
        }
    }

    public static function checkPermissionConditions( Maintenance $maintenance, $info = false ) {

        $allowed_status_ids = PostStatus::getAllowedStatus();
        if ( ! in_array( $maintenance->status_id, $allowed_status_ids ) ) {
            return [
                "result"  => false,
                "message" => "شما اجازه مشاهده فرم نت را ندارید.",
            ];
        }

        if ( $info != false ) {
            foreach ( $info["enable_status"] as &$value ) {
                $value = DashboardController::$perfix_status_code . $value;
            }
            unset( $value );
            if ( ! in_array( $maintenance->status_id, $info["enable_status"] ) ) {
                return [
                    "result"  => false,
                    "message" => "وضعیت فرم نت جهت عملیات نامعتبر است",
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

    public function checkPermission( Maintenance $maintenance ) {
        $result = DashboardController::checkPermissionConditions( $maintenance );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

    }

}
