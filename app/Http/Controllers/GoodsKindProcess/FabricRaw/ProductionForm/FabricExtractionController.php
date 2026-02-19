<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionForm;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionFormController;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineStatus;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class FabricExtractionController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.production_form.fabric_extraction.",
        "enable_status" => [ "001","008" ],
        "button"        => [ "caption" => "استخراج پارچه", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.production_form.fabric_extraction.",

    ];

    var $dashboard_route = "fabric_raw.production_form.dashboard.";

    public function __construct() {

    }

    public function index( ProductionForm $production_form ) {

        $result = $this->checkPermission( $production_form );
        if ( $result != "" ) {
            return $result;
        }
        $machine_allocation_info = ( $production_form->machine->machine_type->machine_module_type->directory_namespace . "\ProductionForm\FabricExtractionController" )::$info;

        return redirect()->route(
            $machine_allocation_info["route"] . "index",$production_form
        );
    }

    public function checkPermission( ProductionForm $production_form ) {

        $result = ProductionFormController::checkPermissionConditions( $production_form, FabricExtractionController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
