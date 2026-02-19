<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\ProductionWarehouse\DashboardController;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function back;
use function event;
use function redirect;
use function view;

class GeneralFinishAllocationController extends Controller
{
    var $view_path = "goods_kind_process.general.machine.finished_allocation.";
    var $route_path;
    var $dashboard_route;


    public function index(Machine $machine,$production_route)
    {

        $list = MachineAllocation::
        join("allocations", "allocation_id", "allocations.id")->
        where("allocations.machine_id", $machine->id)->
        whereIn("allocations.status_id", [5310020, 5310010,5310030])->
        groupBy("allocation_id")->
        select("machine_allocation.*")->
        orderByDesc("production_start_date")->
        paginate();

        $route_path = $this->route_path;
        $dashboard_route=$this->dashboard_route;
        return view($this->view_path . "index", compact("machine", "list", "route_path","dashboard_route","production_route"));

    }

    public function material_consumed_list(Machine $machine, Allocation $allocation)
    {

        $list = Allocation\MachineAllocationMaterialConsumed::where([
            "machine_id" => $machine->id,
            "allocation_id" => $allocation->id
        ])->paginate();

        $route_path = $this->route_path;
        $dashboard_route=$this->dashboard_route;
        return view($this->view_path . "material_consumed_list", compact("machine", "allocation", "list","route_path","dashboard_route"));

    }
}
