<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialReturnToWarehouseLogController;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\Allocation\Modification;


class MaterialReturnToWarehouseLogController extends Controller
{
    public static $info = [
        "route" => "fabric.finishing_machine.machine.material_return_to_warehouse_log.",
        "enable_status" => ["001", "002", "003","901","902","903","904","905"],
        "button" => ["caption" => "مشاهده سابقه برگشت مواد اولیه", "class" => "btn-info"],
        "view_path" => "goods_kind_process.fabric.finishing_machine.machine.material_return_to_warehouse_log.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric.finishing_machine.machine.dashboard.";

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
