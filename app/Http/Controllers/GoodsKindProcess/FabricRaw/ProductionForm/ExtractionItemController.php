<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionForm;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionForm\FabricExtractionController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionFormController;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineStatus;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;

class ExtractionItemController extends Controller
{

    public static $info = [
        "route"         => "fabric_raw.jacquard.production_form.extraction_item.",
        "enable_status" => [ "001","008" ],
        "button"        => [ "caption" => "استخراج آیتم های فرم تولید", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.jacquard.production_form.extraction_item.",

    ];
    var $view_path;
    var $route_path;

    var $dashboard_route = "fabric_raw.production_form.dashboard.";

    public function __construct() {

    }

    public function index( ProductionForm $production_form ) {

        $result = $this->checkPermission( $production_form );
        if ( $result != "" ) {
            return $result;
        }
        $machine_allocation_info = ( $production_form->machine->machine_type->machine_module_type->directory_namespace . "\ProductionForm\ExtractionItemController" )::$info;

        return redirect()->route(
            $machine_allocation_info["route"] . "index",$production_form
        );
    }

    public function checkPermission( ProductionForm $production_form ) {

        $result = ProductionFormController::checkPermissionConditions( $production_form, ExtractionItemController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
