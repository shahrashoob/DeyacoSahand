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
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\BOM\BOMLog;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionFormItemLotNumber;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * شروع تست کالا
 */
class ConfirmTestingController extends Controller
{
    // goods_kind_process/fabric/finishing_machine/machine/start_operation
    public static $info = [
        "route" => "fabric.finishing_machine.machine.confirm_testing.",
        "enable_status" => ["908"],
        "button" => ["caption" => "تایید و بررسی تست", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.fabric.finishing_machine.machine.confirm_testing.",
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
        return view($this->view_path . "index", compact(["machine", "allocation", "current_machine_inputs", "testing_amount", "machine_allocation"]));

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


        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 5310908; // تایید تست
        $machineLog->save();


        // ثبت وضعیت بعدی
        $next_status_result = DashboardController::GetNextStatus($machine, $allocation, $machine->production_status_id, 5310908, false);

        $machine->setStatus(
            null,
            $next_status_result["on_status_id"],
            $next_status_result["status_id"],
            $next_status_result["machine_off_reason_id"]);

        event(new MachineLogEvent($machine, $machineLog));
        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

    }


    public function change_bom(Machine $machine)
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
        if ($allocation->items()->count() != 1) {
            return back()->withErrors("با توجه به اینکه تعداد باندهای تخصیص برابر با یک نمی باشد، امکان تشخیص BOM کالا مقدور نمی باشد، لطفا با واحد پشتیبانی تماس بگیرید.");
        }
        $machine_allocation = $allocation->items()->first();
        if (!$machine_allocation) {
            return back()->withErrors("آیتم های تخصیص نامعتبر است، لطفا با پشتیبانی تماس بگیرد.");
        }
        return view($this->view_path . "change_bom", compact(["machine", "allocation", "current_machine_inputs", "machine_allocation"]));

    }

    public function submit_change_bom(Request $request, Machine $machine)
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
        if ($allocation->items()->count() != 1) {
            return back()->withErrors("با توجه به اینکه تعداد باندهای تخصیص برابر با یک نمی باشد، امکان تشخیص BOM کالا مقدور نمی باشد، لطفا با واحد پشتیبانی تماس بگیرید.");
        }
        $machine_allocation = $allocation->items()->first();
        if (!$machine_allocation) {
            return back()->withErrors("آیتم های تخصیص نامعتبر است، لطفا با پشتیبانی تماس بگیرد.");
        }

// انتخاب اولین BOM از مسیر برای تخصیص
        $bom = BOM::where([
            "product_id" => $machine_allocation->product_id,
            "product_route_id" => $machine_allocation->line_product_station->product_route_id
        ])->
        first();
        $bom_items=$bom->items()->where("station_id",$machine->station_id)->get();
        $bom_in_station_count=  $bom_items->count() ;
        if ($bom_in_station_count != count($current_machine_inputs)) {
            return back()->withErrors("با توجه به اینکه آیتم های BOM بعد از تخصیص کارت تولید تغییر پیدا کرده است، امکان ثبت ورژن جدید برای BOM وجود ندارد و </br> لطفا تخصیص را کنسل کرده و یکبار دیگر تخصیص را ثبت نمایید.");
        }

        foreach ($bom_items as $bom_item) {
            $key = $bom_item->input_line_code . "_" . $bom_item->material_id;

            if (!isset($request->item[$key])) {
                return back()->withErrors("ماده اولیه (" . $bom_item->material->caption . ") در BOM وجود دارد ولی در تخصیص وجود ندارد، لطفا تخصیص را کنسل کرده و یک بار تخصیص را ثبت نمایید.");
            }
            $new_value = $request->item[$key];
            if ($new_value < 0) {
                return back()->withErrors("ماده اولیه (" . $bom_item->material->caption . ") در BOM وجود دارد ولی در تخصیص وجود ندارد، لطفا تخصیص را کنسل کرده و یک بار تخصیص را ثبت نمایید.");
            }
        }

        // گرفتن نسخه ابتدایی BOM
        BOMLog::NewLog(BOM::find($bom->id), null, null, Auth::id(), true);

        foreach ($bom_items as $bom_item) {
            $key = $bom_item->input_line_code . "_" . $bom_item->material_id;
            $new_value = $request->item[$key];
            $bom_item->amount = $new_value;
            $bom_item->save();

        }
        $bom = BOM::find($bom->id);
        $result = BOMLog::ChangeBOM($bom);
        if (!$result["result"]) {
            return back()->withErrors("مقدار های ماده اولیه نسبت به مقدار های قبلی هیچ تغییری نکرده است، لطفا مقدار های جدید را وارد نمایید.");
        }

        BOMLog::NewLog($bom,$machine_allocation->production_id,$allocation->id,Auth::id());

        $allocation_amount=$allocation->getAllocationAmount();
        // ویرایش ووردی های ماشین
        foreach ($current_machine_inputs as $item) {
            $key = $item->input_line_code . "_" . $item->material_id;
            $new_value = $request->item[$key];
            $item->amount = $new_value;
            $item->amount_required=$item->amount_for_one_unit() * $allocation_amount;
            $item->save();
        }
        //

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 5310909; // عدم تایید تست
        $machineLog->save();


        // ثبت وضعیت بعدی
        $next_status_result = DashboardController::GetNextStatus($machine, $allocation, $machine->production_status_id, 5310909, false);

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
