<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\ProductionCard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\ProductionCard\Allocation;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\Post\PostStatus;
use App\Models\Production\Production;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use function back;
use function view;

class DashboardController extends Controller {
    //
    public static $perfix_status_code = "7001";

    public function __construct() {
        $perfix_status_code = DashboardController::$perfix_status_code;
        View::share( "perfix_status_code", $perfix_status_code );
    }

    public function view_card( Production $production ) {
        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        };

        $controller_info = DashboardController::get_controller_info();
        MachineAllocation::where( [ "production_id" => $production->id, "status_id" => 5310005 ] )->delete();


        return view( "goods_kind_process.fabric_raw.production_card.dashboard.view_card",
            compact( "production", "controller_info" ) );
    }

    public static function get_controller_info() {
        return $controller_info = [
            "01" => MachineAllocationController::$info,
        ];
    }

    public static function get_controller_info_for_permission() {
        return $controller_info = [
            "01" => MachineAllocationController::$info,
            "02" => AllocationCancelController::$info
        ];
    }

    public static function checkPermissionConditions( Production $production, $info = false ) {

1/0;
        $allowed_status_ids = PostStatus::getAllowedStatus();

        if ( ! in_array( $production->waiting_status_id, $allowed_status_ids ) ) {
            return [
                "result"  => false,
                "message" => "شما اجازه مشاهده کارت تولید را ندارید.",
            ];
        }


        if ( $info != false ) {
            foreach ( $info["enable_status"] as &$value ) {
                $value = DashboardController::$perfix_status_code . $value;
            }
            unset( $value );
            if ( ! in_array( $production->waiting_status_id, $info["enable_status"] ) ) {
                return [
                    "result"  => false,
                    "message" => "وضعیت کارت تولید جهت عملیات نامعتبر است",
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

    public function checkPermission( Production $production ) {
        $result = DashboardController::checkPermissionConditions( $production );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

    }
}
