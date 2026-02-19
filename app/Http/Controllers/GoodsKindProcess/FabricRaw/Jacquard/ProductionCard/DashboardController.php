<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\ProductionCard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCardController;
use App\Models\Post\PostStatus;
use App\Models\Production\Production;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller {
    //
    public static $perfix_status_code = "7001";

    public function __construct() {
        $perfix_status_code = DashboardController::$perfix_status_code;
        View::share( "perfix_status_code", $perfix_status_code );
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
       return ProductionCardController::checkPermissionConditions($production,$info);
    }

    public function checkPermission( Production $production ) {
        $result = DashboardController::checkPermissionConditions( $production );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

    }
}
