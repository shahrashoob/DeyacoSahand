<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\Matthys\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialDeliveryConfirmationController;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialDeliveryRejectController;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class MaterialDeliveryConfirmationController extends Controller {
    public static $info = [
        "route"         => "warps.matthys.machine.material_delivery_confirmation.",
        "enable_status" =>["001","002","003","004","005","006","007","008","009","010","011","012" ],
        "button"        => [ "caption" => "تایید تحویل مواد اولیه از انبار", "class" => "btn-success" ],
        "view_path"     => "goods_kind_process.warps.matthys.machine.material_delivery_confirmation.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.matthys.machine.dashboard.";

    public function __construct() {
        $this->route_path = MaterialDeliveryConfirmationController::$info["route"];
        $this->view_path  = MaterialDeliveryConfirmationController::$info["view_path"];
    }

    public function index( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->index(  $machine );


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
        $publicController                  = new GeneralMaterialDeliveryConfirmationController();
        $publicController->route_path      = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, MaterialDeliveryConfirmationController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
