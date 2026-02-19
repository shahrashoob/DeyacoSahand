<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;


use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralInjectionOfMaterialController;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use Illuminate\Http\Request;


class InjectionOfMaterialController extends Controller
{
    public static $info = [
        "route" => "fabric.finishing_machine.machine.injection_of_material.",
        "enable_status" => [
            "903",
        ],
        "button" => ["caption" => "ثبت تزریق مواد اولیه", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.fabric.finishing_machine.machine.injection_of_material.",
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
        // آیا مقدار فرم تولید با تزریق مواد اولیه تکمیل می شود؟
        $value_201 = MachineModuleTypePropertyValue::getValue("73030011201", $machine->machine_type_id);
        if ($value_201 == 1) {

            $current_production_form = $machine->getCurrentProductionForm();
            if (!$current_production_form) {
                return back()->withErrors("فرم تولید جاری برای ماشین یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
            }
            // آیتمی که تخصیص جاری است.
            $machine_allocation = $allocation->items()->
            where("status_id", 5310010)-> // تخصیص جاری
            first();
            $current_input_list =
                CurrentMachineInput::
                where([
                    "machine_id" => $machine->id,
                    "allocation_id" => ($allocation->id ?? -1),
                    "production_id" => $machine_allocation->production_id ?? -1
                ])->
                get();


            return view($this->view_path . "index", compact(["machine", "allocation", "current_input_list",]));
        } else {
            return back()->withErrors("بخشی از ماژول برای حالتی که مشخصه 73030011201  برابر با 0 باشد پیاده سازی نشده است.");
        }

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
        foreach ($allocation->items as $machine_allocation) {
            $machine_allocation_count++;
            if ($machine_allocation->production->packing_types()->count() == 0) {
                return back()->withErrors("نوع بسته بندی برای کارت تولید " . $machine_allocation->production->serial . " مشخص نشده است.");
            }
        }
        if ($machine_allocation_count == 0) {
            return back()->withErrors("هیچ آیتم تخصیصی برای تخصیص جاری یافت نشد، لطفا با مسئول مربوطه تماس بگیرید.");
        }


        if ($machine->check_inventory_for_allocation) {
            // ارسال درخواست برای کدام رسته های کالایی فعال است.
//            $goods_kind_ids = MachineTypeInputBandGoodsKind:: getGoodsKindIdsWhereRequestFromRobot($machine, $active = 1);

            $result = \App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\InjectionOfMaterialController::SetInjectionMaterial($request, $machine, $allocation, 1);
            if (!$result["result"]) {
                return back()->withErrors($result["error"]);
            }
        } else {
            return back()->withErrors("لطفا تنظیمات 'موجودی مواد اولیه برای انبارک چک شود؟' را برای ماشین فعال کنید.");
        }

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 5310920; // تزریق مواد اولیه
        $machineLog->save();

        $value_201 = MachineModuleTypePropertyValue::getValue("73030011201", $machine->machine_type_id);
        if ($value_201 == 1) {
            /**
             * آیا مقدار فرم تولید با تزریق مواد اولیه تکمیل می شود؟ بله
             * در این حالت هر آیتمی بسته بندی برابر است با یک آیتم در فرم تولید
             */
            $result = StartOperationController::AddItemToCurrentProductionFrom($request, $machine, $allocation, $machineLog,1,2);
            if (!$result["result"]) {
                $machineLog->delete();
                return back()->withErrors($result["error"]);
            }

            event(new MachineLogEvent($machine, $machineLog));



        } else {
            return back()->withErrors("بخشی از ماژول برای حالتی که مشخصه 73030011201  برابر با 0 باشد پیاده سازی نشده است.");

        }


        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

    }

    public static function SetInjectionMaterial(Request $request, Machine $machine, Allocation $allocation, $input_number = 0, $new_machine_log = null, $current_production = null, $checkPackingIsOnlyEntrance = false, $replacement_status_id = 3359001)
    {

        return GeneralInjectionOfMaterialController::SetInjectionMaterial($request, $machine, $allocation, $input_number, $new_machine_log, $current_production, $checkPackingIsOnlyEntrance, $replacement_status_id);

    }

    public function getGeneralController()
    {
        $publicController = new GeneralInjectionOfMaterialController();
        $publicController->route_path = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }

    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, InjectionOfMaterialController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

}
