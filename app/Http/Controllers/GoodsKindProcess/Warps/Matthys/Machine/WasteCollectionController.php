<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\Matthys\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralWasteCollectionController;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use Illuminate\Http\Request;

class WasteCollectionController extends Controller
{
    public static $info = [
        "route"         => "warps.matthys.machine.waste_collection.",
        "enable_status" =>["001","002","003","004","005","006","007","008","009","010","011","012" ],
        "button"        => [ "caption" => "جمع آوری ضایعات", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.warps.matthys.machine.waste_collection.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.matthys.machine.dashboard.";

    public function __construct() {
        $this->route_path = WasteCollectionController::$info["route"];
        $this->view_path  = WasteCollectionController::$info["view_path"];
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

        $result = DashboardController::checkPermissionConditions( $machine, WasteCollectionController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
