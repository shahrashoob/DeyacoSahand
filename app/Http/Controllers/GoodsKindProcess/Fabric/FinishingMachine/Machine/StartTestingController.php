<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\InjectionOfMaterialController;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric\Fabric;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Machine\MachineTypeOutputBandPackingType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLayer;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionFormItemLotNumber;
use App\Models\Utility\Option;
use App\Models\Warehouse\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * شروع تست کالا
 */
class StartTestingController extends Controller
{
    // goods_kind_process/fabric/finishing_machine/machine/start_operation
    public static $info = [
        "route" => "fabric.finishing_machine.machine.start_testing.",
        "enable_status" => ["906"],
        "button" => ["caption" => "شروع تست", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.fabric.finishing_machine.machine.start_testing.",
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

        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین یافت نشد");
        }

        $current_machine_inputs = CurrentMachineInput::where("allocation_id", $allocation->id)->get();
        $machine_allocation = $allocation->items()->first();
        if (!$machine_allocation) {
            return back()->withErrors("آیتم های تخصیص نامعتبر است، لطفا با پشتیبانی تماس بگیرد.");
        }
        $testing_amount = $machine_allocation->product->testing_amount;
        if (!$testing_amount || $testing_amount <= 0) {
            return back()->withErrors("مقدار تست کالای " . $machine_allocation->product->caption . " نامعتبر است، لطفا با واحد اطلاعات پایه تماس بگیرید.");
        }


       $warehouse_ids= Warehouse::
        where("id", $machine->warehouse_id)->
        orWhere(function ($query) use ($machine) {
            return $query->where("warehouse_type_id", 3)->
            where("belonging_to_id", $machine->machine_type_id);
        })->orWhere(function ($query) use ($machine) {
            return $query->where("warehouse_type_id", 4)->
            where("belonging_to_id", $machine->station_id);
        })->orWhere(function ($query) use ($machine) {
            return $query->where("warehouse_type_id", 5)->
            where("belonging_to_id", $machine->line_id);
        })->pluck("id");

        $packing_form_list = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
        whereIn("warehouse_id", $warehouse_ids)->
        where("packing_forms.status_id", 7007003)->select("packing_forms.code", "packing_forms.id", "product_id")->
        get();


        return view($this->view_path . "index", compact(["machine", "allocation", "packing_form_list", "current_machine_inputs", "testing_amount", "machine_allocation"]));

    }

    public function submit(Request $request, Machine $machine)
    {


        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین یافت نشد");
        }
        $machine_allocation_count = 0;
        foreach ($allocation->items as $machine_allocation_item) {
            $machine_allocation_count++;
        }


        if ($machine_allocation_count == 0) {
            return back()->withErrors("هیچ آیتم تخصیصی برای تخصیص جاری یافت نشد، لطفا با مسئول مربوطه تماس بگیرید.");
        }

        $current_machine_inputs = CurrentMachineInput::where("allocation_id", $allocation->id)->get();
        foreach ($current_machine_inputs as $current_machine_input) {
            if (!$request->current_machine_input_select[$current_machine_input->id]) {
                return back()->withErrors("لطفا کد بسته بندی مربوط به " . $current_machine_input->materail->caption . " را وارد نمایید.");
            }
            $current_machine_input->packing_form_id = $request->current_machine_input_select[$current_machine_input->id];
            $current_machine_input->save();
        }

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 5310906; // شروع تست
        $machineLog->save();


        // ثبت وضعیت بعدی
        $next_status_result = DashboardController::GetNextStatus($machine, $allocation, $machine->production_status_id, 5310906, false);

        $machine->setStatus(
            null,
            $next_status_result["on_status_id"],
            $next_status_result["status_id"],
            $next_status_result["machine_off_reason_id"]);

        event(new MachineLogEvent($machine, $machineLog));
        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

    }

    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, self::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

}
