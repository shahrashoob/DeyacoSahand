<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class StartSetupController extends Controller
{
    public static $info = [
        "route" => "fabric.finishing_machine.machine.start_setup.",
        "enable_status" => ["901"],
        "button" => ["caption" => "شروع ستاپ (Setup)", "class" => "btn-primary"],
        "message" => ["confirm" => "آیا از شروع ستاپ (Setup) اطمینان دارید؟"],

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

        $next_status_result = DashboardController::GetNextStatus($machine,$allocation, 7303901, -1, false);

        if (!$next_status_result["result"]) {
            return back()->withErrors($next_status_result["error"]);
        }

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 5310901; // شروع ستاب
        $machineLog->allocation_id=$allocation->id;

        $next_status_result = DashboardController::GetNextStatus($machine,$allocation, 7303901, 5310901, true);


        $machine->setStatus(
            null,
            $next_status_result["on_status_id"],
            $next_status_result["status_id"],
            $next_status_result["machine_off_reason_id"]);


        $machineLog->station_sub_operation_id=$next_status_result["current_station_sub_operation_id"];
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
