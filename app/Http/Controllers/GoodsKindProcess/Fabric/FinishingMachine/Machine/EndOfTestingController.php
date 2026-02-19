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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * شروع تست کالا
 */
class EndOfTestingController extends Controller
{
    // goods_kind_process/fabric/finishing_machine/machine/start_operation
    public static $info = [
        "route" => "fabric.finishing_machine.machine.end_of_testing.",
        "enable_status" => ["907"],
        "button" => ["caption" => "پایان تست", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.fabric.finishing_machine.machine.end_of_testing.",
        "message" => ["confirm" => "آیا از پایان تست اطمینان دارید؟"],
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric.finishing_machine.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
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



        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 5310907; // پایان تست
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
