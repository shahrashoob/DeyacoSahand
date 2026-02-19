<?php

namespace App\Http\Controllers\Contractor\Panel;

use App\Events\Form\PackingLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKind\GoodsKindLotNumberPropertyValue;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionFormItemLotNumber;
use App\Models\Utility\Option;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterProductionController extends Controller {
    public static $info = [
        "route"         => "contractor.panel.register_production.",
        "view"          => "contractor.panel.register_production.",
        "enable_status" => [ "104","106","109" ],
        "button"        => [ "caption" => "ثبت تولید", "class" => "btn-primary" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "contractor.panel.dashboard.";

    //
    public function __construct() {
        $this->route_path = RegisterProductionController::$info["route"];
        $this->view_path  = RegisterProductionController::$info["view"];
    }

    public function index( MachineAllocation $machine_allocation ) {

        $result = $this->checkPermission( $machine_allocation );
        if ( $result != "" ) {
            return $result;
        }

        return redirect()->route( "production.public_module.register_production.index", $machine_allocation );

    }

    public
    function checkPermission(
        MachineAllocation $contractor_allocation
    ) {

        $result = DashboardController::checkPermissionConditions( $contractor_allocation, RegisterProductionController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

}
