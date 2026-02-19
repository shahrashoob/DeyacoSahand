<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer\Machine;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralWasteCollectionController;
use App\Models\LineProduct\Machine\Machine;
use Illuminate\Http\Request;

class WasteCollectionController extends Controller
{
    public static $info = [
        "route"         => "warps.karl_mayer.machine.waste_collection.",
        "enable_status" =>["001","002","003","004","005","006","007","008","009","010","011","012" ],
        "button"        => [ "caption" => "جمع آوری ضایعات", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.warps.karl_mayer.machine.waste_collection.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.karl_mayer.machine.dashboard.";

    public function __construct() {
        $this->route_path = self::$info["route"];
        $this->view_path  = self::$info["view_path"];
    }

    public function index( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return  $publicController->index( $machine );

    }

    public function submit( Request $request, Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $publicController = $this->getGeneralController();

        return $publicController->submit($request, $machine );

    }


    public function getGeneralController() {
        $publicController                  = new GeneralWasteCollectionController();
        $publicController->route_path      = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }
    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, self::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}

