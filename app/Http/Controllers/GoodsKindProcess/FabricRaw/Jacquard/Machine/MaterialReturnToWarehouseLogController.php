<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialReturnToWarehouseController;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialReturnToWarehouseLogController;
use App\Models\Form\Packing\PackingForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Utility\Option;
use App\Models\Utility\Unit;
use App\Models\Warehouse\Warehouse;
use Illuminate\Http\Request;
use App\Models\LineProduct\Machine\Allocation\Modification;


class MaterialReturnToWarehouseLogController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.jacquard.machine.material_return_to_warehouse_log.",
        "enable_status" => [
            "001",
            "002",
            "003",
            "004",
            "005",
            "006",
            "007",
            "008",
            "009",
            "010",
            "011",
            "012",
            "013",
            "014",
            "015",
            "016",
            "017",
            "018",
            "019",
            "020",
            "021",
            "022",
            "023",
            "024",
            "025",
            "026",
            "027",
            "028",
            "029",
            "030",
            "031",
            "032",
            "033",
            "034",
            "035",
            "036",
            "037",
            "038",
            "039",
            "040",
            "041",
            "042",
            "043",
            "044",
            "045",
            "046",
            "047",
            "048",
            "049",
            "050",
            "051",
            "052",
            "053",
            "054"
        ],
        "button" => ["caption" => "مشاهده سابقه برگشت مواد اولیه ", "class" => "btn-info"],
        "view_path" => "goods_kind_process.fabric_raw.jacquard.machine.material_return_to_warehouse_log.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];

    }

    public function index(Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->index($machine);
    }

    public function details(Machine $machine, Modification\MachineAllocationModification $machine_allocation_modification)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();
        return $publicController->details($machine, $machine_allocation_modification);
    }

    public function print_one_of_packing_form(Machine $machine,PackingForm $packing_form,  $modification_packing_form_id)
    {


        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->print_one_of_packing_form($packing_form,$modification_packing_form_id);
    }

    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, MaterialReturnToWarehouseLogController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

    public function getGeneralController()
    {
        $publicController = new GeneralMaterialReturnToWarehouseLogController();
        $publicController->route_path = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }


}
