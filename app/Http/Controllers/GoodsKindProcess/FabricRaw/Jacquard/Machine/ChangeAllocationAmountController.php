<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationProductionChannel;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\Production\ProductionLog;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ChangeAllocationAmountController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.jacquard.machine.change_allocation_amount.",
        "enable_status" => ["016", "042"],
        "button" => ["caption" => "تغییر مقدار تخصیص (ویژه دوره پیاده سازی)", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.fabric_raw.jacquard.machine.change_allocation_amount.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = ChangeAllocationAmountController::$info["route"];
        $this->view_path = ChangeAllocationAmountController::$info["view_path"];
    }

    public function index(Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        $allocation = $machine->getCurrentAllocation();

        if (!$allocation || count($allocation->items) == 0) {
            return back()->withErrors("تخصیص نامعتبر است، لطفا با پشتیبانی تماس بگیرید.");
        }
        $machine_allocation = $allocation->items->first();
        $min = $machine_allocation->number_of_doffs_done * $machine_allocation->amount_of_each_doffs;
        $max = ($machine_allocation->number_of_doffs_done + 1) * $machine_allocation->amount_of_each_doffs;

        return view($this->view_path . "index", compact("machine", "min", "max"));

    }

    public function submit(Request $request, Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();

        if (!$allocation || count($allocation->items) == 0) {
            return back()->withErrors("تخصیص نامعتبر است، لطفا با پشتیبانی تماس بگیرید.");
        }
        $machine_allocation = $allocation->items->first();
        $min = $machine_allocation->number_of_doffs_done * $machine_allocation->amount_of_each_doffs;
        $max = ($machine_allocation->number_of_doffs_done + 1) * $machine_allocation->amount_of_each_doffs;

        $new_amount = $request->new_amount;
        if ($new_amount > $max || $new_amount < $min) {
            return back()->withErrors("مقدار وار دشده معتبر نمی باشد.");
        }

        $allocation_amount = $allocation->getAllocationAmount();
        $new_allocation_amount = $new_amount * $allocation->items()->count();

        if ($allocation_amount < $new_allocation_amount) {
            return back()->withErrors("با توجه به اینکه مقدار تخصیص جدید از مقدار تخصیص قبلی بیشتر است، امکان تغییر مقدار تخصیص وجود ندارد.");
        }


        self::ChangeAllocationAmount($allocation, $new_amount);


        // لاگ ماشین
        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 590;

        $machineLog->save();

        event(new MachineLogEvent($machine, $machineLog, $allocation_amount . "=>" . $new_allocation_amount, false));

        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

    }

    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, ChangeAllocationAmountController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

    public static function ChangeAllocationAmount(Allocation $allocation, $new_amount_for_one_band, $allocation_is_canceled = false, $special_message = "")
    {

        $production_list = [];
        $max_number_of_doffs = 0;
        foreach ($allocation->items as $item) {
            $old_amount = $item->allocation_amount;
            $item->max_number_of_doffs = $item->number_of_doffs_done + 1;
            $max_number_of_doffs = max($max_number_of_doffs, $item->max_number_of_doffs);
            $item->allocation_amount = $new_amount_for_one_band;
            $item->save();

            if (!isset($production_list[$item->production_id])) {
                $production_list[$item->production_id] = 1;
                $message = "تغییر مقدار تخصیص از " . $old_amount . " به" . $new_amount_for_one_band . "(به ازای هر باند)";
                if ($allocation_is_canceled) {
                    $message = "کنسل کردن تخصیص";
                }
                event(new ProductionCardLogEvent($item->production, $message . $special_message, null, 7008005));

            }

        }

        $doff_list = Allocation\AllocationDoffs::where("allocation_id", $allocation->id)->
        orderBy("id")->get();
        $k = 0;
        foreach ($doff_list as $doff) {
            $k++;
            if ($max_number_of_doffs < $k) {
                $doff->allocation_brands()->delete();
                $doff->delete();

            }

        }


        $last_doff = Allocation\AllocationDoffs::where("allocation_id", $allocation->id)->
        orderByDesc("id")->
        first();
        if($last_doff) {
            $last_doff->amount_of_each_doffs = $new_amount_for_one_band;
        }

        // مقدار تخصیص جدید به ازای یک باند است، که خود تابع آن را در تعداد باند ضرب می کند.
        $new_allocation_amount = $new_amount_for_one_band * $allocation->items()->count();

        MachineAllocationProductionChannel:: ReduceAllocationAmount($allocation, $new_allocation_amount);

    }
}
