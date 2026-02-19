<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\Matthys\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialReturnToWarehouseController;
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


class MaterialReturnToWarehouseController extends Controller
{
    public static $info = [
        "route" => "warps.matthys.machine.material_return_to_warehouse.",
        "enable_status" => ["001", "002", "003", "004", "005", "006", "007", "008", "009", "010", "011", "012"],
        "button" => ["caption" => "برگشت مواد اولیه به انبار", "class" => "btn-info"],
        "view_path" => "goods_kind_process.warps.matthys.machine.material_return_to_warehouse.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.matthys.machine.dashboard.";
    var $route_log_path = "warps.karl_mayer.machine.material_return_to_warehouse_log.";

    public function __construct()
    {
        $this->route_path = MaterialReturnToWarehouseController::$info["route"];
        $this->view_path = MaterialReturnToWarehouseController::$info["view_path"];

    }


    public function index(Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

//
        return $publicController->index($machine);

    }
    public function remove_product_from_list(Machine $machine,Product $product)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

//
        return $publicController->remove_product_from_list($machine,$product);

    }

    public function reset_removed_product_from_list(Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->reset_removed_product_from_list($machine);

    }


    public function submit_change_range(Request $request, Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

//
        return $publicController->submit_change_range($request, $machine);

    }

    public function remaining_packing_form(Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

//
        return $publicController->remaining_packing_form($machine);

    }
    public function add_packing_form(Modification\MachineAllocationModification $machineAllocationModification,Machine $machine,Product $product,$type)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

//
        return $publicController->add_packing_form($machineAllocationModification,$machine,$product,$type);

    }
    public function delete_one_of_packing_form(Machine $machine,PackingForm $packing_form,  $modification_packing_form_id)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

//
        return $publicController->delete_one_of_packing_form($machine,$packing_form,$modification_packing_form_id);

    }

    public function set_remainder_consumed(Modification\MachineAllocationModification $machineAllocationModification,Machine $machine,Product $product)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

//
        return $publicController->set_remainder_consumed($machineAllocationModification,$machine,$product);

    }

    public function show_packing_form_by_product(Modification\MachineAllocationModification $machineAllocationModification,Machine $machine,Product $product,$consumed_status_id)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

//
        return $publicController->show_packing_form_by_product($machineAllocationModification,$machine,$product,$consumed_status_id);

    }

    public function submit_remaining_packing_form(Request $request, Machine $machine, Warehouse $warehouse)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->submit_remaining_packing_form($request, $machine, $warehouse);


    }

    public function confirm(Machine $machine, Warehouse $warehouse)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->confirm($machine, $warehouse);
    }

    public function submit_confirm(Request $request, Machine $machine, Warehouse $warehouse)
    {


        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->submit_confirm($request, $machine, $warehouse);
    }

    public function print_new_packing(Machine $machine)
    {


        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->print_new_packing($machine);
    }

    public function submit_print_new_packing(Request $request, Machine $machine, Modification\MachineAllocationModification $machine_allocation_modification)
    {


        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->submit_print_new_packing($request, $machine, $machine_allocation_modification);
    }
    public function show_log(Machine $machine)
    {


        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->show_log($machine);
    }

    public function show_log_details(Machine $machine, Modification\MachineAllocationModification $machine_allocation_modification)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->show_log_details($machine, $machine_allocation_modification);
    }

    public function warehouse_handling(Machine $machine, Warehouse $warehouse)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->warehouse_handling($machine, $warehouse);
    }

    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, MaterialReturnToWarehouseController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

    public function getGeneralController()
    {
        $publicController = new GeneralMaterialReturnToWarehouseController();
        $publicController->route_path = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;
        $publicController->route_log_path = $this->route_log_path;

        return $publicController;
    }


}
