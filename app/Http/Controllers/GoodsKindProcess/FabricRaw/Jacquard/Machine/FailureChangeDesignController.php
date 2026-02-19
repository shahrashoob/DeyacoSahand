<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationProductionChannel;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypeProperty;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionFormLog;
use Illuminate\Http\Request;

class FailureChangeDesignController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.jacquard.machine.failure_change_design.",
        "enable_status" => ["047"],
        "button" => ["caption" => "عدم تغییر کالیته", "class" => "btn-danger"],
        "view_path" => "goods_kind_process.fabric_raw.jacquard.machine.failure_change_design.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

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

        return view($this->view_path . "index", compact("machine"));
    }

    public function submit(Request $request, Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 551; // عدم تغییر کالیته

        $reserve_allocation_list = $machine->ReserveAllocation()->get();

        // اولین تخصیص رزرو
        $first_reserve_allocation = isset($reserve_allocation_list[0]) ? $reserve_allocation_list[0] : null;
        if (!$first_reserve_allocation) {
            return back()->withErrors("هیچ کارت رزروی برای ماشین وجود ندارد، لطفا با پشتیبانی تماس بگیرید.");
        }

        // دومین تخصیص رزرو
        $second_reserve_allocation = isset($reserve_allocation_list[1]) ? $reserve_allocation_list[1] : null;;

        if ($second_reserve_allocation) {
            // چک کردن اینکه کانال تولید ماشین عوض می شود یا خیر
            // اگر کانال تولید عوض می شد، خطا بدهد.
            $second_reserve_allocation_production_channel =
                MachineAllocationProductionChannel::where("allocation_id", $second_reserve_allocation->id)->first();
            $current_production_channel = $machine->getCurrentProductionChannel();

            //  return  $second_reserve_allocation_production_channel->production_channel->production_channel_type_id ."!=". $current_production_channel->production_channel_type_id;
//            if (!$second_reserve_allocation_production_channel || $second_reserve_allocation_production_channel->production_channel->production_channel_type_id != $current_production_channel->production_channel_type_id) {
//                return back()->withErrors("با توجه به اینکه کانال تولید کارت رزرو اول و کارت  رزرو دوم با هم متفاوت هستند، امکان عدم تغییر کالیته وجود ندارد،
//               " . "<br/>");
//            }
        }

        // فرم تولید جاری ( درحال بافت، در انتظار پایان بافت بخش پایانی)
        $current_production_form = $machine->getCurrentProductionForm();


        // فرم در حال بارگذاری (رزرو)
        $reserve_production_form = $machine->getCurrentProductionForm("current_production_form_status_with_reserve");


        if (!$current_production_form && !$reserve_production_form) {
            return back()->withErrors("فرم تولید جاری یا رزرو برای ماشین وجود ندارد، لطفا با پشتیبانی تماس بگیرید.");
        }

        if ($current_production_form && $current_production_form->id == $reserve_production_form->id) {
            $reserve_production_form = null; // فرم در حال بارگذاری نداریم.
        }


        // کنسل کردن تخصیص
        ChangeAllocationAmountController::ChangeAllocationAmount($first_reserve_allocation, 0, $request->description);
        $first_reserve_allocation->status_id = 5310030; // تخصیص کنسل شده
        $first_reserve_allocation->save();

        $first_reserve_allocation->items()->update(["status_id" => 5310030]);


        if($current_production_form) {
            // مقدار فرم تولید را صفر می کنیم.
            ProductionFormItem::where("allocation_id", $first_reserve_allocation->id)->
            update([
                "start_machine_log_id" => $current_production_form->start_machine_log_id,
                "end_of_machine_log_id" => $current_production_form->start_machine_log_id
            ]);
        }
        // اگر رزرو دوم ندارد و فرم در انتظار بارگذاری دارد، فرم را خاتمه یافته می کنیم.
        if (!$second_reserve_allocation && $reserve_production_form) {
            $reserve_production_form->status_id = 7002005; // خاتمه یافته
            $reserve_production_form->save();

            // عدم تغییر کالیته
            event(new ProductionFormLogEvent($reserve_production_form, 7002019, null, $request->description));

            // خالی کردن حامل
            if ($reserve_production_form->carrier) {
                Carrier::StaticSetEmpty($reserve_production_form->carrier);
            }

        }

        //اگر کارت رزرو که کنسل شده تخصیص جاری دیگری ندارد، باید وضعیت آن به در انتظار

        if ($second_reserve_allocation) {
            // کارت رزرو بعدی اگر وجود داشت خود به خود جایگزین می شود.

            // اضافه کردن آیتم ها به فرم تولید
            foreach ($second_reserve_allocation->items as $machine_allocation) {
                ProductionFormItem::AddNewItem(
                    $second_reserve_allocation->id,
                    $reserve_production_form->id ?? $current_production_form->id,
                    $machine_allocation->production_id,
                    $machine_allocation->product_id,
                    $machine_allocation->band_code,
                    7002001,// در حال بافت
                    $machine_allocation->amount_of_each_doffs,
                    $machine_allocation->version_code??null
                );
            }
            // عدم تغییر کالیته
            event(new ProductionFormLogEvent($reserve_production_form ?? $current_production_form, 7002019));
        }

        $production_status_id = 7003017; // نداشتن سفارش
        if ($second_reserve_allocation) {
            // اگر چله تغییر می کند، در انتظار شروع تغییر چله، در غیر این صورت در انتظار شروع تغییر کالیته
            $production_status_id = $second_reserve_allocation->has_warps_change ? 7003044 : 7003043;
        }
        $machine->setStatus(
            null,
            null,
            $production_status_id,
            1780);


        event(new MachineLogEvent($machine, $machineLog, $request->description));

        Allocation::updatePriorityNumber($machine);

        $message = $request->description . " بر روی ماشین تغییر کالیته انجام نشد.";
        MachineModuleTypeProperty::SendSms($message, $first_reserve_allocation, 70030021403);

        // بروز رسانی وضعیت کارت تولیدی که تخصیص آن کنسل شده است.
        FabricRaw::UpdateProductionStatus($first_reserve_allocation);



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
