<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use App\Models\Worker;
use Illuminate\Http\Request;

class GeneralOperatorController extends Controller
{
    // ثبت اپراتور مسئول
    var $view_path = "goods_kind_process.general.machine.operator.";
    var $route_path;
    var $dashboard_route;

    public function __construct()
    {

    }

    public function index(Machine $machine)
    {
        $shift_work_option = Option::get("shift_work");
        $worker_option = Option::get("worker", 0, [1, 11]);
        $route_path = $this->route_path;
        $dashboard_route = $this->dashboard_route;
        return view($this->view_path . "index", compact("worker_option", "machine", "shift_work_option", "route_path", "dashboard_route"));

    }

    public function submit(Request $request, Machine $machine)
    {

        $message = "";
        $last_row_log = MachineLog::getLastLogWithContour($machine);

        $machineLog = new MachineLog();
        $machineLog->operator_id = $request->operator_id;
        $machineLog->machine_event_type_id = 650; // ثبت تحویل شیفت

        if ($machine->machine_type->machine_type_consumption_type_id == 1) {
            if ($last_row_log) {
                $contour_result = $last_row_log->checkMinContour(
                    $request->contour_1_value,
                    $request->contour_2_value,
                    $request->contour_3_value,
                    $request->contour_4_value,
                    $request->contour_5_value);
                if (
                    isset($last_row_log) && !$contour_result["result"]
                ) {
                    $message .= $contour_result["error"];
                }
            } else {
                $contour_result["ratio"] = $machine->machine_type->getContourRatio();
            }


            if ($message != "") {
                return back()->withErrors($message);
            }


            $machineLog->contour_1_value = $request->contour_1_value * $contour_result["ratio"];
            $machineLog->contour_2_value = $request->contour_2_value * $contour_result["ratio"];
            $machineLog->contour_3_value = $request->contour_3_value * $contour_result["ratio"];
            $machineLog->contour_4_value = $request->contour_4_value * $contour_result["ratio"];
            $machineLog->contour_5_value = $request->contour_5_value * $contour_result["ratio"];
            $machineLog->shift_work_id = $request->shift_work_id;

            $production_form = $machine->getCurrentProductionForm();
            if ($production_form) {
                // بروزرسانی مقادیر فرم تولید
                $last_machine_log = MachineLog::getLastLogWithContour($machine);
                if($last_machine_log) {
                    ProductionForm::UpdateAmountWithLastContour($production_form, $last_machine_log);
                }
            }

            $allocation = $machine->getCurrentAllocation();
            // ثبت مقدار مصرف
            MachineAllocationMaterialConsumed::registerNewConsumed($allocation, $machine, $last_row_log, $machineLog);

        }

        event(new MachineLogEvent($machine, $machineLog));


        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

    }


    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, OperatorController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
