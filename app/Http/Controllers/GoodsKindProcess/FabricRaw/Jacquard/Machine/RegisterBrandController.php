<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Machine\Allocation\AllocationDoffs;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;

class RegisterBrandController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.jacquard.machine.register_brand.",
        "view" => "goods_kind_process.fabric_raw.jacquard.machine.register_brand.",
        "enable_status" => ["016", "042"],
        "button" => ["caption" => "ثبت لوگو", "class" => "btn-primary"],


    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view"];
    }

    public function index(Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        $shift_work_option = Option::get("shift_work");

        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری ماشین یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
        }

        $allocation_doff = null;
        if ($allocation) {
            $machine_allocation = $allocation->items->first();
            $allocation_doff = AllocationDoffs::where("allocation_id", $allocation->id)->orderBy("id")->skip($machine_allocation->number_of_doffs_done)->first();
        }

        if (!$allocation_doff) {
            return back()->withErrors("با توجه به اینکه تخصیص جاری دارای جدول داف نمی باشد، امکان ثبت لوگو وجود ندارد.");
        }

        if ($allocation_doff->number_of_brand_done + 1 >= $allocation_doff->number_of_brand) {
            return back()->withErrors("تعداد لوگو در داف جاری برابر با ".($allocation_doff->number_of_brand)." می باشد و شما تمامی لوگو های مورد نیاز را قبلا ثبت کرده اید.");
        }

        $route_path = $this->route_path;
        $dashboard_route = $this->dashboard_route;
        return view($this->view_path . "index", compact( "machine", "shift_work_option", "route_path", "dashboard_route"));

    }

    public function submit(Request $request, Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }


        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 641; // ثبت برند

        $message = "";
        $last_row_log = MachineLog::getLastLogWithContour($machine);


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
        }


        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری ماشین یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
        }

        $allocation_doff = null;
        if ($allocation) {
            $machine_allocation = $allocation->items->first();
            $allocation_doff = AllocationDoffs::where("allocation_id", $allocation->id)->orderBy("id")->skip($machine_allocation->number_of_doffs_done)->first();
        }

        if (!$allocation_doff) {
            return back()->withErrors("با توجه به اینکه تخصیص جاری دارای جدول داف نمی باشد، امکان ثبت لوگو وجود ندارد.");
        }

        if ($allocation_doff->number_of_brand_done + 1 >= $allocation_doff->number_of_brand) {
            return back()->withErrors("تعداد لوگو در داف جاری برابر با ".$allocation_doff->number_of_brand." می باشد و شما تمامی لوگو ها را قبلا ثبت کرده اید.");
        }

        $allocation_doff->number_of_brand_done += 1;
        $allocation_doff->save();


        event(new MachineLogEvent($machine, $machineLog));


        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "یک لوگو با موفقتی ثبت کردید."]);

    }


    public
    function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, self::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
