<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\ProductionCard;


use App\Events\Machine\MachineAllocationEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralMachineAllocationController;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralProductionChannelController;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBand;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\MaterialFlow;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\LineProduct\StationOperation;
use App\Models\Production\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

/**
 *  ماژول عمومی تکمیل Finishing Machine
 */
class MachineReallocationController_removed extends Controller
{
    //
    public static $info = [
        "route" => "fabric.finishing_machine.machine_reallocation.",
        "enable_status" => [],
        "next_status" => [],
        "button" => ["caption" => "تخصیص مجدد ماشین ", "class" => "btn-success"],
        "view_path" => "goods_kind_process.fabric.finishing_machine.production_card.machine_reallocation."
    ];

    public $dashboard_route = "fabric.production_card.view_card";
    public $controller_info;
    public $route_path;
    public $view_path;

    public function __construct()
    {
        $this->view_path = self::$info["view_path"];
        $this->route_path = self::$info["route"];
        $this->controller_info = MachineAllocationController::$info;
    }

    public function index(MachineAllocation $machine_allocation)
    {




    }

}
