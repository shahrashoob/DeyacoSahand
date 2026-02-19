<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralInjectionOfMaterialController;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineInputLog;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class InjectionOfMaterialController extends Controller {
    public static $info = [
        "route"         => "warps.karl_mayer.machine.injection_of_material.",
        "enable_status" =>["-1" ], // "002","003","004","005","006","007","008","009","010","011","012"
        "button"        => [ "caption" => "ثبت تزریق مواد اولیه", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.warps.karl_mayer.machine.injection_of_material.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.karl_mayer.machine.dashboard.";

    public function __construct() {
        $this->route_path = InjectionOfMaterialController::$info["route"];
        $this->view_path  = InjectionOfMaterialController::$info["view_path"];
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

        return $publicController->submit( $request, $machine ,false);


    }

    public static function SetInjectionMaterial( Request $request, Machine $machine, Allocation $allocation, $input_number = 0, $new_machine_log = null, $current_production = null ) {

        return GeneralInjectionOfMaterialController::SetInjectionMaterial($request,$machine,$allocation,$input_number,$new_machine_log,$current_production);

    }


    public function getGeneralController() {
        $publicController                  = new GeneralInjectionOfMaterialController();
        $publicController->route_path      = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }
    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, InjectionOfMaterialController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
