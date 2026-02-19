<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Fabric\SpecialProduction\ProductionCard\DashboardController;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\Post\PostStatus;
use App\Models\Production\Production;
use Illuminate\Support\Facades\Auth;

/**
 * پارچه تکمیل
 */
class ProductionCardController extends Controller {
    public static $perfix_status_code = "7301";
    public $view_path = "goods_kind_process.fabric.production_card.";
    public $route_path = "goods_kind_process.fabric.production_card.";

    public function __construct() {

    }

    public function view_card( Production $production,$back_url_type="" ) {

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        };

        $controller_info = ProductionCardController::get_controller_info();
        MachineAllocation::where( [ "production_id" => $production->id, "status_id" => 5310005 ] )->delete();


        return view( $this->view_path . "view_card",
            compact( "production", "controller_info", "back_url_type" ) );
    }

    public static function get_controller_info() {
        return $controller_info = [
            "01" => ProductionCard\MachineAllocationController::$info,
            "03" => ProductionCard\FinishedAllocationController::$info,
            "04" => ProductionCard\TerminateProductionController::$info,
        ];
    }

    public static function get_controller_info_for_permission() {
        return $controller_info = [
            "01" => ProductionCard\MachineAllocationController::$info,
            "02" => ProductionCard\AllocationCancelController::$info,
            "03" => ProductionCard\FinishedAllocationController::$info,
            "04" => ProductionCard\TerminateProductionController::$info,
        ];
    }

    public static function checkPermissionConditions( Production $production, $info = false ) {


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
                    "message" => "دسترسی  عملیات برای شما تعریف نشده است".$info["route"] . "index",
                ];
            }
        }


        return [
            "result" => true,
        ];

    }

    public function checkPermission( Production $production ) {
        $result = ProductionCardController::checkPermissionConditions( $production );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }
    }
}
