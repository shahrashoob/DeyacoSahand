<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\GoodsKindProcess\Fabric\Fabric;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\Production\ProductionFormItem;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;

class EndOfOperationController extends Controller
{
    public static $info = [
        "route" => "fabric.finishing_machine.machine.end_of_operation.",
        "view" => "goods_kind_process.fabric.finishing_machine.machine.end_of_operation.",
        "enable_status" => ["903"],
        "button" => ["caption" => "پایان عملیات", "class" => "btn-primary"],
        "message" => ["confirm" => "آیا از پایان عملیات اطمینان دارید؟"],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric.finishing_machine.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view"];
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
        // استارت T استارت برای ماشین ها
        $result = StartToStartMachineController::PostSubmit($machine);
        if (!$result["result"] && !isset($result["warning"])) {
            return redirect()->route($this->dashboard_route . "view", compact("machine"))->withErrors($result["error"]);

        }
        // آیا نیاز است تا مقدار نهایی فرم تولید در پایان عملیات بروز رسانی شود؟
        $value_208 = MachineModuleTypePropertyValue::getValue("73030011208", $machine->machine_type_id);
        if ($value_208) {
            return redirect()->route($this->route_path . "set_production_form_amount", $machine);
        }
         return self::PostSubmit($machine, $allocation);
    }

    public function PostSubmit(Machine $machine, Allocation $allocation)
    {

        // آیا مقدار فرم تولید با تزریق مواد اولیه تکمیل می شود؟
        $value_201 = MachineModuleTypePropertyValue::getValue("73030011201", $machine->machine_type_id);
// آیا ماژول ثبت تولید در ماشین فعال است.
        $value_202 = MachineModuleTypePropertyValue::getValue("73030011202", $machine->machine_type_id);

        $machine_allocation = $allocation->items()->where('status_id', 5310010)->first();
        // اگر تزریق داشته باشد و فرم تولید هم داشته باشد و اولین ردیف در تخصیص هم نداشته باشد ( پدر داشته باشد.)
        if ($value_202 && $value_201 == 1 && $machine_allocation->parent_allocation_id) {
            $source_production_form_item = ProductionFormItem::
            where("allocation_id", $machine_allocation->parent_allocation_id)->
            //  where("product_id",$machine_allocation->product_id)->
            where("status_id", 7302003)-> // در انتظار تزریق به ماشین
            first();
            if (!$source_production_form_item || $source_production_form_item->allocation_id != $machine_allocation->parent_allocation_id) {
                return back()->withErrors("اطلاعات فرم تولید در انتظار تزریق ماشین نامعتبر است، لطفا با پشتیبانی تماس بگیرید.");
            }

            $count_packing_form_temp = MachineAllocationPackingForm::where([
                "machine_allocation_id" => $machine_allocation->id,
                "status_id" => 7007005 // در انتظار تحویل به انبار
            ])->count();
            if ($count_packing_form_temp > 0) {
                return back()->withErrors("لطفا قبل از ثبت پایان عملیات، همه بسته بندی را به انبار تحویل دهید.");

            }
            // اگر خطایی نبود، در پایین مقدار آن را ویرایش می کنیم.
            $result_end_of_source = \App\Http\Controllers\Production\PublicModule\RegisterProductionController::
            EndOfSourceProductionFormItem($machine_allocation, $source_production_form_item->id ?? 0, 1);
            if (!$result_end_of_source["result"]) {
                return back()->withErrors($result_end_of_source["error"]);
            }
        }

        if ($value_202) {
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
            if ($next_machine_allocation) {
                return back()->withErrors("با توجه به اینکه همه کارت های موجود در تخصیص شماره " . $next_machine_allocation->allocation_id . " تولید نشده اند، امکان خاتمه یافته کردن کارت وجود ندارد."
                    . "<br/>" . "کارت تولید:" . $next_machine_allocation->production->serial
                );
            }

            $result_can_production_terminate = \App\Http\Controllers\Production\PublicModule\RegisterProductionController::CheckProductionTerminate($machine_allocation, "can_terminate", 0, false);
            if (!$result_can_production_terminate["result"]) {

                return back()->withErrors($result_can_production_terminate["error"]);
            }

            $result_terminate = \App\Http\Controllers\Production\PublicModule\RegisterProductionController::ProductionTerminate($machine_allocation);
            if (!$result_terminate["result"]) {
                return redirect()->route("production.public_module.register_production.index", $machine_allocation)->withErrors($result_terminate["error"]);
            }


        }

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 5310903; // پایان عملیات
        $machineLog->allocation_id = $allocation->id;

        $next_status_result = DashboardController::GetNextStatus($machine, $allocation, $machine->production_status_id, 5310903, true);


        $machine->setStatus(
            null,
            $next_status_result["on_status_id"],
            $next_status_result["status_id"],
            $next_status_result["machine_off_reason_id"]);


        $machineLog->station_sub_operation_id = $next_status_result["current_station_sub_operation_id"];
        event(new MachineLogEvent($machine, $machineLog));

        if ($value_202 == 1) { // اکر مازول ثبت تولید در ماشین داریم، به اندازه مقدار تولید ثبت شده باید.
            // ثبت مقدار مصرف
            MachineAllocationMaterialConsumed::registerNewConsumed($allocation, $machine, null, $machineLog, -1);
        }

        // همه کارت های تخصیص بشود در حال تولید
        foreach ($allocation->items as $item) {
            $new_status = Fabric::GetProductionStatus($item->production);
            $item->production->waiting_status_id = $new_status;
            if($new_status == 7301004){
                $item->production->status_id = 520;
            }
            $item->production->save();
            event(new ProductionCardLogEvent($item->production, "", null, 7301004));

        }

        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

    }

    public function set_production_form_amount(Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین یافت نشد");
        }


        $production_form = $machine->getCurrentProductionForm();
        if (!$production_form) {
            return back()->withErrors("فرم تولید جاری ماشین یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
        }
        ///return $production_form->items;
        return view($this->view_path . "set_production_form_amount", compact("machine", "allocation", "production_form"));
    }

    public function confirm_production_form_amount(Request $request, Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();
        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین یافت نشد");
        }

        $production_form = $machine->getCurrentProductionForm();
        if (!$production_form) {
            return back()->withErrors("فرم تولید جاری ماشین یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
        }
        foreach ($production_form->items as $item) {
            if (!isset($request["production_form_item"][$item->id])) {
                return back()->withErrors("لطفا مقدار نهایی همه آیتم های فرم ورود را ثبت نمایید.");
            }
        }

        foreach ($production_form->items as $item) {
            $allow_diff_percent = $item->product->goods_kind->min_diff_of_production_and_allocation_in_the_end_of_production;
            $max_allow_diff_percent = $item->product->goods_kind->max_diff_of_production_and_allocation_in_the_end_of_production;

            $max_production = $item->final_amount * (1 + $max_allow_diff_percent / 100);
            $min_production = $item->final_amount * (1 - $allow_diff_percent / 100);

            if (isset($request["production_form_item"][$item->id])) {
                $production_amount = $request["production_form_item"][$item->id];

                if ((
                        $production_amount > $max_production ||
                        $production_amount < $min_production
                    ) && $item->final_amount != 0 // چون وقتی که فرم را تزریق می کنیم مقدار آن صفر می شود و مجبور هستیم که در این حالت از چک کردن رد شویم.
                ) {
                    return back()->withErrors("مقدار $production_amount برای کارت تولید " . $item->production->serial() . " نامعتبر است، با توجه به تنظیمات رسته کالایی حداکثر و حداقل مقدار برای تخصیص رعایت نشده است.");
                }


                $machine_allocation = MachineAllocation::where([
                    "allocation_id" => $allocation->id,
                    "production_id" => $item->production_id,
                ])->first();

                if (!$machine_allocation) {
                    return back()->withErrors("ردیف تخصیص برای کارت تولید " . $item->production->serial . " یافت نشد، لطفا با واحد پشتیبانی تماس بگیرید.");
                }


            }
        }

        foreach ($production_form->items as $item) {
            if (isset($request["production_form_item"][$item->id])) {
                $new_amount = round($request["production_form_item"][$item->id], 6);
                $before_amount = $item->final_amount;
                $item->final_amount = $new_amount;
                $item->save();

                event(new ProductionFormLogEvent(
                    $production_form,
                    7002020, // بروز رسانی مقدار فرم تولید
                    $item,
                    $before_amount . "=>" . $item->final_amount
                ));

            }

        }


        return self::PostSubmit($machine, $allocation);
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
