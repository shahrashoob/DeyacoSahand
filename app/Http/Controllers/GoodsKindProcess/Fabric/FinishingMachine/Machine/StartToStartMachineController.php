<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;

use App\Events\Contractor\ContractorLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\ProductionCard\MachineAllocationController;
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
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionFormItemLotNumber;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\ExecutionStatus;


class StartToStartMachineController extends Controller
{
    // goods_kind_process/fabric/finishing_machine/machine/start_operation
    // در عملیات های STS وقتی ماشین در حال عملیات است باید ماشین بعدی را هم استارت کنند.
    public static $info = [
        "route" => "fabric.finishing_machine.machine.start_to_start.",
        "enable_status" => ["902"],
        "button" => ["caption" => "شروع عملیات ماشین بعدی", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.fabric.finishing_machine.machine.start_to_start.",

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
            return back()->withErrors("تخصیص جاری ماشین یافت نشد.");
        }


        // آیتمی که تخصیص جاری است.
        $machine_allocation = $allocation->items()->
        where("status_id", 5310010)-> // تخصیص جاری
        first();

        //چک کردن اینکه نوع پیش نیازی شروع عملیات ماشین در مسیر محصول بعدی STS است یا خیر
        // اگر STS است باید بعد از مدت مشخصی عملیات شروع ماشین بعدی استارت شود.
        // مثلا بعد از سفت پیچی باید میز بسته بندی استارت شود.
        $start_to_start_line_product_station = DashboardController::GetStartToStartLineProductStation($machine_allocation);

        if (!$start_to_start_line_product_station) {
            return back()->withErrors("برای مسیر محصول بعدی نوع پیش نیازی شروع عملیات ماشین به صورت StartToStart نمی باشد.");
        }

        return view($this->view_path . "index", compact("machine", "machine_allocation", "start_to_start_line_product_station"));
    }

    public function submit(Request $request, Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        $result = self::PostSubmit($machine);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $allocation = $result["allocation"];
        $next_machine = $result["machine"];

        return redirect()->route($this->dashboard_route . "view", [$machine])->with(["success" => "تخصیص شماره " . $allocation->id . " برای ماشین " . $next_machine->caption . " فعال گردید."]);
    }

    public static function PostSubmit(Machine $machine)
    {


        $allocation = $machine->getCurrentAllocation();

        if (!$allocation) {
            return [
                "result" => false,
                "error" => "تخصیص جاری ماشین یافت نشد."
            ];

        }

        $result_current_allocation=self::StartToStartForAllocation($machine,$allocation);

        $reserve_allocation=$machine->ReserveAllocation()->get();
        foreach ($reserve_allocation as $reserve_allocation_item) {
            self::StartToStartForAllocation($machine,$reserve_allocation_item);
        }
        if (!$result_current_allocation["result"]) {
            return $result_current_allocation;
        }


    }
    public static function StartToStartForAllocation(Machine $machine, Allocation $allocation)
    {

        // آیتمی که تخصیص جاری است.
        $machine_allocation_list = $allocation->items()->
        where("status_id", $allocation->status_id)-> // تخصیص جاری
        get();

        $first_machine_allocation = $machine_allocation_list->first();
        //چک کردن اینکه نوع پیش نیازی شروع عملیات ماشین در مسیر محصول بعدی STS است یا خیر
        // اگر STS است باید بعد از مدت مشخصی عملیات شروع ماشین بعدی استارت شود.
        // مثلا بعد از سفت پیچی باید میز بسته بندی استارت شود.
        $start_to_start_line_product_station  = DashboardController::GetStartToStartLineProductStation($first_machine_allocation);

        if (!$start_to_start_line_product_station) {
            return [
                "result" => false,
                "warning" => true,
                "error" => "برای مسیر محصول بعدی نوع پیش نیازی شروع عملیات ماشین به صورت StartToStart نمی باشد."
            ];

        }

        if ($machine->next_relation_machine_code == "") {
            return [
                "result" => false,
                "error" => "در بخش تعریف ماشین آلات، ماشین متناظر بعدی مشخص نشده است، لطفا با واحد استقرار تماس بگیرید."
            ];
        }
        $next_machine = Machine::where("machine_type_id", $start_to_start_line_product_station->machine_type_id)->
        where("number_code", $machine->next_relation_machine_code)->first();

        if (!$next_machine) {
            return [
                "result" => false,
                "error" => "در بخش تعریف ماشین آلات، کد ماشین متناظر بعدی به درستی مشخص نشده است، کد " . $machine->next_relation_machine_code . " وجود ندارد. لطفا با واحد استقرار تماس بگیرید."
            ];
        }


        // بررسی اینکه قبلا تخصیص داده نشده باشد؟
        $before_allocation = MachineAllocation::where([
            "parent_allocation_id" => $first_machine_allocation->allocation_id,
            "production_id" => $first_machine_allocation->production_id,
        ])->
        whereNotIn("status_id", [5310030, 5310050])->
        first();
        if ($before_allocation) {
            return [
                "result" => false,
                "warning" => true,
                "error" => "قبلا برای ماشین تخصیص شماره " . $before_allocation->allocation_id . " بر روی ماشین " . $before_allocation->machine->caption . " انجام شده است."
            ];

        }

        $allocation_status_id = 5310010;
        // بررسی اینکه کارت روی ماشین رزور نباشد و یا جاری نداشته باشد.
        $reserve_allocation = $next_machine->ReserveAllocation()->count();
        if ($reserve_allocation > 0) {
            $allocation_status_id = 5310040;
//            return [
//                "result" => false,
//                "error" => "با توجه به اینکه ماشین دارای کارت رزرو می باشد، امکان شروع عملیات در " . $next_machine->caption . " وجود ندارد، لطفا پس از تکمیل سفارش ها اقدام نمایید."
//            ];
        }

        $current_allocation = $next_machine->getCurrentAllocation();
        if ($current_allocation) {
            $allocation_status_id = 5310040;
//            return [
//                "result" => false,
//                "error" => "با توجه به اینکه ماشین دارای کارت جاری می باشد، امکان شروع عملیات در " . $next_machine->caption . " وجود ندارد، لطفا پس از تکمیل سفارش ها اقدام نمایید."
//            ];
        }


        $allocation = Allocation::create($first_machine_allocation->allocation->toArray());
        $allocation->machine_id = $next_machine->id;
        $allocation->status_id = $allocation_status_id; // تخصیص جاری
        $allocation->created_at = now();
        $allocation->updated_at = now();
        $allocation->save();

        foreach ($machine_allocation_list as $allocation_item) {

            $new_machine_allocation = MachineAllocation::create($allocation_item->toArray());
            $new_machine_allocation->allocation_id = $allocation->id;
            $new_machine_allocation->status_id = $allocation_status_id; // جاری
            $new_machine_allocation->parent_allocation_id = $allocation_item->allocation_id;
            $new_machine_allocation->machine_id = $next_machine->id;
            $new_machine_allocation->created_at = now();
            $new_machine_allocation->updated_at = now();

            $new_machine_allocation->max_number_of_doffs = 1;
            $new_machine_allocation->amount_of_each_doffs = $allocation_item->allocation_amount;
            $new_machine_allocation->number_of_doffs_done = 0;

            $new_machine_allocation->line_product_station_id = $start_to_start_line_product_station->id;
            $new_machine_allocation->save();


            event(new ContractorLogEvent(
                null,
                5310501 //ایجاد تخصیص مجدد
                ,
                $new_machine_allocation->production,
                $new_machine_allocation
            ));
        }
        $machine_allocation_data = ["is_force_batch" => 1];
        // ذخیره اطلاعات تنظیمات ستاب و عملیات کارت ها
        Allocation\AllocationData::SetData(400, $machine_allocation_data, $allocation->id);

        /*****************************************/

        /***************************************************/



        if ($allocation_status_id == 5310010) {
            $next_status_result = [
                "result" => true,
                "status_id" => 7303902, // در انتظار شروع عملیات
                "on_status_id" => 53001, // روشن
                "machine_off_reason_id" => null,
            ];
            $next_machine->setStatus(
                null,
                $next_status_result["on_status_id"],
                $next_status_result["status_id"],
                $next_status_result["machine_off_reason_id"]);
        }


        return [
            "result" => true,
            "allocation" => $allocation,
            "machine" => $next_machine,
        ];
    }

    public static function CheckAllocation(Machine $machine, MachineAllocation $machine_allocation)
    {
        $start_to_start_line_product_station = DashboardController::GetStartToStartLineProductStation($machine_allocation);
        if ($start_to_start_line_product_station) {
            $next_machine = Machine::where("machine_type_id", $start_to_start_line_product_station->machine_type_id)->
            where("number_code", $machine->next_relation_machine_code)->first();

            if (!$next_machine) {
                return [
                    "result" => false,
                    "error" => "در بخش تعریف ماشین آلات، کد ماشین متناظر بعدی به درستی مشخص نشده است، کد " . $machine->next_relation_machine_code . " وجود ندارد. لطفا با واحد استقرار تماس بگیرید."
                ];
            }


            // بررسی اینکه قبلا تخصیص داده نشده باشد؟
            $before_allocation = MachineAllocation::where([
                "parent_allocation_id" => $machine_allocation->allocation_id,
                "production_id" => $machine_allocation->production_id,
            ])->first();

            if (!$before_allocation || ($before_allocation && !in_array($before_allocation->status_id, [5310010, 5310020]))) {
                return [
                    "result" => false,
                    "error" => "نوع پیش نیازی شروع عملیات ماشین در مسیر محصول بعدی STS است، لازم است تا قبل از پایان عملیات در این ماشین، تخصیص به ماشین " . $next_machine->caption . " انجام شده باشد و تخصیص در وضیعت جاری یا خاتمه یافته باشد. " .
                        (!$before_allocation ? "لطفا تخصیص در ماشین بعد را انجام دهید." : "تخصیص در ماشین " . $next_machine->caption . " انجام شده است ولی در وضعیت " . $before_allocation->status->caption . " قرار دارد.")
                ];
            }
        }
        return [
            "result" => true,
        ];
    }

    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, EndOfOperationController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

}