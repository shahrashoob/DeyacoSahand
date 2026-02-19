<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialDeliveryRejectController;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use Illuminate\Http\Request;


class MaterialDeliveryRejectController extends Controller {
    public static $info = [
        "route"         => "warps.karl_mayer.machine.material_delivery_reject.",
        "enable_status" =>["001","002","003","004","005","006","007","008","009","010","011","012" ],
        "button"        => [ "caption" => "عدم تایید تحویل مواد اولیه از انبار", "class" => "btn-danger" ],
        "view_path"     => "goods_kind_process.warps.karl_mayer.machine.material_delivery_reject.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.karl_mayer.machine.dashboard.";

    public function __construct() {
        $this->route_path = MaterialDeliveryRejectController::$info["route"];
        $this->view_path  = MaterialDeliveryRejectController::$info["view_path"];

    }

    public function index( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }


        $publicController = $this->getGeneralController();

        return $publicController->index( $machine );


    }

    public function submit( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->submit( $request, $machine );
    }

    public function getGeneralController() {
        $publicController                  = new GeneralMaterialDeliveryRejectController();
        $publicController->route_path      = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, MaterialDeliveryRejectController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

}
