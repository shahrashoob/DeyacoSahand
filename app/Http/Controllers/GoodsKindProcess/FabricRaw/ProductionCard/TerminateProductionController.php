<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCardController;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralTerminateProductionController;
use App\Models\Production\Production;
use Illuminate\Http\Request;

class TerminateProductionController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.production_card.terminate_production.",
        "enable_status" => [ "001", "002", "003" ],
        "next_status"        => [],
        "button"             => [ "caption" => "خاتمه یافته کردن کارت ", "class" => "btn-danger" ],
    ];

    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.production_card.";

    public function __construct() {
         $this->route_path =TerminateProductionController::$info["route"];

    }

    public function index( Production $production ) {

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->index(  $production );
    }
    public function submit(Request $request,Production $production ) {

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->submit( $request, $production );
    }

    public function getGeneralController() {
        $publicController                  = new GeneralTerminateProductionController();
        $publicController->route_path      = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;
        $publicController->waiting_status_id = 7001004; // خاتمه یافته

        return $publicController;
    }
    public function checkPermission( Production $production ) {

        $result = ProductionCardController::checkPermissionConditions( $production, self::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

}
