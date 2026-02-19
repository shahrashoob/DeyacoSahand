<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Machine\Allocation\AllocationDoffs;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;

class StartWarpingController extends Controller
{
    public static $info = [
        "route" => "warps.karl_mayer.machine.start_warping.",
        "enable_status" => ["004"],
        "button" => ["caption" => "شروع چله کشی", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.warps.karl_mayer.machine.start_warping.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.karl_mayer.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = StartWarpingController::$info["route"];
        $this->view_path = StartWarpingController::$info["view_path"];
    }

    public function index(Request $request, Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین وجود ندارد، لطفا با مسئول مربوطه تماس بگیرید.");
        }

        $machine_allocation = $allocation->items()->first();
        if (!$machine_allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین وجود ندارد، لطفا با مسئول مربوطه تماس بگیرید.");
        }

        // پیدا کردن نوع حامل از روی نوع بسته بندی
        $list = $machine_allocation->production->packing_types;

        if (count($list) == 0) {
            return back()->withErrors("برای کارت تولید هیچ نوع بسته بندی تعریف نشده است، لطفا با پشتیبانی تماس بگیرید.");
        }
        $packing_type_option = Option::get("production_packing_type", 0, 0, $list);

        if (count($list) == 1) {
            $packing_type = $list[0]->packing_type;
            // پیدا کردن نوع حامل از روی نوع بسته بندی
            $first_layer = $packing_type->layers->where("layer_code", 1)->first();
            if (!$first_layer) {
                return redirect()->route($this->route_path . "index")->withErrors("تعریف نوع حامل در  بسته بندی معتبر نمی باشد، لطفا با پشتیبانی تماس بگیرید.");

            }

            // اگر فقط یک بسته بندی است و نیاز به شماره حامل ندارد، بنابراین مستقیم به مرحله بعدی می رویم.
            if ($first_layer->carrier_type && !$first_layer->carrier_type->has_number_ability) {
                $request["packing_type_id"] = $packing_type->id;
                return $this->submit($request, $machine);
            }
        }

        return \view($this->view_path . "index", compact("machine", "packing_type_option"));

    }

    public function submit(Request $request, Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین وجود ندارد، لطفا با مسئول مربوطه تماس بگیرید.");
        }

        $machine_allocation = $allocation->items()->first();
        if (!$machine_allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین وجود ندارد، لطفا با مسئول مربوطه تماس بگیرید.");
        }

        $packing_type = PackingType::where("id", $request->packing_type_id)->first();
        if (!$packing_type) {
            return back()->withErrors("لطفا یک بسته بندی انتخاب نمایید.");
        }

      $result_doff=  AllocationDoffs::HasAnyDoff($allocation->id,$packing_type->id);
        if(!$result_doff["result"]){
            return back()->withErrors($result_doff["error"]);
        }
        // پیدا کردن نوع حامل از روی نوع بسته بندی
        $first_layer = $packing_type->layers->where("layer_code", 1)->first();
        if (!$first_layer) {
            return redirect()->route($this->route_path . "index")->withErrors("تعریف نوع حامل در  بسته بندی معتبر نمی باشد، لطفا با پشتیبانی تماس بگیرید.");

        }
        $carrier = null;
        // اگر حامل قابلیت شماره گذاری دارد، اطلاعات حامل را بررسی می کنیم.
        if ($first_layer->carrier_type->has_number_ability) {
            $result = Carrier::firstOrCreate($request->carrier_code, $first_layer->carrier_type_id, 5320001, null);
            if (!$result["result"]) {
                return redirect()->back()->withErrors($result["message"]);
            }

            $carrier = $result["carrier"];
        }
        $result = PackingType::getWeight($packing_type, $carrier);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        if ($carrier && !$carrier->weight) {
            return redirect()->back()->withErrors("وزن شماره حامل " . $carrier->code . " در سامانه ثبت نگردیده است، لطفا با مسئول اطلاعات پایه تماس بگیرید.");

        }


        $production_form = $machine->getCurrentProductionForm();
        if (!$production_form) {
            return back()->withErrors("فرم تولید جاری برای ماشین یافت نشد، لطفا با پشتیبانی تماس  بگیرید.");
        }

        $production_form->carrier_id = $carrier->id??null;
        $production_form->packing_type_id = $packing_type->id;
        $production_form->save();

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 1040;
        $machineLog->save();

        if($carrier) {
            $carrier_status_id = 5320006  //  پر شده در حال تکمیل
            ;
            $production_form->carrier->SetStatus(
                $carrier_status_id,
                $machine->fullCaption(),
                5320105,
                $machine_allocation->product_id,
                $production_form->machine->id
            );
        }

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 1020;
        $machineLog->save();


        $machine->setStatus(
            null,
            53001, // روشتن
            7203005, // در حال چله کشی
            null);

        event(new MachineLogEvent($machine, $machineLog));


        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

    }


    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, StartWarpingController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
