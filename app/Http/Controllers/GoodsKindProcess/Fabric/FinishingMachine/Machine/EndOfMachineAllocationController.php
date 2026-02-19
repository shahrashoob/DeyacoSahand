<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\Production\ProductionFormItem;
use function back;
use function event;
use function redirect;

class EndOfMachineAllocationController extends Controller
{
    public static $info = [
        "route" => "fabric.finishing_machine.machine.end_of_machine_allocation.",
        "enable_status" => ["903"],
        "button" => ["caption" => "پایان و شروع کارت بعدی", "class" => "btn-primary"],
        "message" => ["confirm" => "آیا از پایان بسته بندی و شروع کارت بعدی اطمینان دارید؟"],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric.finishing_machine.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
    }

    public function submit(Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین یافت نشد");
        }

        $next_status_result = DashboardController::GetNextStatus($machine, $allocation, $machine->production_status_id, -1, false);

        if (!$next_status_result["result"]) {
            return back()->withErrors($next_status_result["error"]);
        }

// آیا ماژول ثبت تولید در ماشین فعال است.
        $value_202 = MachineModuleTypePropertyValue::getValue("73030011202", $machine->machine_type_id);

        $machine_allocation = $allocation->items()->where('status_id', 5310010)->first();

        if (!$value_202) {
            return back()->withErrors("با توجه به تنظیمات ماشین، امکان فراخوانی مازول وجود ندارد، لطفا با واحد پشتیبانی تماس بگیرید.");
        }
        // چک کردن اینکه تمامی فرم های بسته بندی تحویل شده به انبار باشند.
        $list_count = MachineAllocationPackingForm::whereIn(
            "status_id", [
                7007005,
                7007006,//معلق
                7007011,//معلق api
            ]
        // بسته های در انتظار تحویل به انبار/پیمانکار
        )->
        when($machine_allocation->machine, function ($query) use ($machine_allocation) {
            return $query->where([
                "machine_id" => $machine_allocation->machine_id,
                "machine_allocation_id" => $machine_allocation->id,
            ]);
        })->
        when($machine_allocation->contractor, function ($query) use ($machine_allocation) {
            return $query->where([
                "contractor_id" => $machine_allocation->contractor_id,
                "machine_allocation_id" => $machine_allocation->id,
            ]);
        })->
        when($machine_allocation->order, function ($query) use ($machine_allocation) {
            return $query->where([
                "order_id" => $machine_allocation->order_id,
                // "machine_allocation_id" => $machine_allocation->id,
            ]);
        })->
        count();

        if ($list_count > 0) {
            return back()->withErrors("لطفا قبل ثبت پایان عملیات، اقدامات مربوط به ثبت نهایی بسته بندی ها و تحویل به انبار (تحویل به کنترل کیفیت) را انجام دهید. "
                . "<br/>" . " وضعیت $list_count بسته بندی نامعتبر است."
            );
        }

        //چک کردن اینکه کارت رزرو بعدی در تخصیص وجود نداشته باشد،
        $next_machine_allocation = $allocation->items()->where('status_id', 5310040)->first();
        if (!$next_machine_allocation) {
            return back()->withErrors(" با توجه به انیکه آخرین کارت تولید در تخصیص در حال بسته بندی است، لازم است تا مازول پایان عملیات را ثبت نمایید.");
        }

        $result_can_production_terminate = \App\Http\Controllers\Production\PublicModule\RegisterProductionController::CheckProductionTerminate($machine_allocation, "can_terminate", 0, false);
        if (!$result_can_production_terminate["result"]) {

            return back()->withErrors($result_can_production_terminate["error"]);
        }

        $result_terminate = \App\Http\Controllers\Production\PublicModule\RegisterProductionController::ProductionTerminate($machine_allocation);
        if (!$result_terminate["result"]) {
            return redirect()->route("production.public_module.register_production.index", $machine_allocation)->withErrors($result_terminate["error"]);
        }

        $machine_allocation->status_id = 5310020;
        $machine_allocation->save();

        $next_machine_allocation->status_id = 5310010;
        $next_machine_allocation->save();

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 5310910; // پایان بسته بندی کارت جاری


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
