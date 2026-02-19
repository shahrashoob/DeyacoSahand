<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\ProductionCard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Fabric\ProductionCardController;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\Production\Production;
use Illuminate\Http\Request;

class AllocationCancelController extends Controller {
    public static $info = [
        "route"         => "fabric.allocation_cancel.",
        "enable_status" => [ "001","002" ],
        "next_status"   => [],
        "button"        => [ "caption" => "کنسل کردن تخصیص ", "class" => "btn-danger" ],
        "view_path"     => ""
    ];

    public function index(Allocation $allocation, Production $production){

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $machine_allocation_info = ( $allocation->machine->machine_type->machine_module_type->directory_namespace . "\ProductionCard\AllocationCancelController" )::$info;

        return redirect()->route(
            $machine_allocation_info["route"] . "index",
            [ $allocation, $production ]
        );
    }

    public function checkPermission( Production $production ) {

        $result = ProductionCardController::checkPermissionConditions( $production, self::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

}
