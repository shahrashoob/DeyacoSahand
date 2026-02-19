<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\ProductionCard;

use App\Events\Machine\MachineAllocationEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralMachineAllocationController;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralProductionChannelController;
use App\Http\Controllers\GoodsKindProcess\Warps\Matthys\Machine\EndOfBeamingController;
use App\Http\Controllers\GoodsKindProcess\Warps\ProductionCardController;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachinePropertyValue;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBand;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\MaterialFlow;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\Production\Production;
use Illuminate\Http\Request;

class MachineAllocationController extends Controller
{
    public static $perfix_production_status_code = "7201";
    public static $info = [
        "route" => "warps.machine_allocation.",
        "enable_status" => ["001", "002", "003"],
        "enable_route" => [
            "fabric_raw.jacquard.machine_allocation.index",
            "fabric_raw.dobby.machine_allocation.index"
        ],
        "next_status" => [],
        "button" => ["caption" => "تخصیص ماشین", "class" => "btn-success"],
        "view_path" => "goods_kind_process.warps.production_card.machine_allocation."
    ];


    public $dashboard_route = "warps.machine_allocation.";
    public $controller_info;

    public function __construct()
    {
        $this->controller_info = MachineAllocationController::$info;
    }

    public function index(Production $production)
    {

        $result = $this->checkPermission($production);
        if ($result != "") {
            return $result;
        }

        if ($production->get_allocation_amount() >= $production->number) {
            return back()->withErrors("با توجه به اینکه مقدار تخصیص داده شده به اندازه مقدار کارت تولید می باشد، امکان تخصیص جدید وجود ندارد.");
        }
        if ($production->product->line_product_station()->where("status_id", 1200)->count() == 0) {
            return back()->withErrors("هیچ مسیر محصول فعالی برای کالا یافت نشد.");
        }

        return view($this->controller_info["view_path"] . "index", compact("production"));
    }

    public function select_machine_type(Request $request, Production $production, MachineType $machine_type)
    {


        $machine_id_name = "machine_type_" . $machine_type->id;

        if (!isset($request->$machine_id_name)) {
            return redirect()->
            route($this->dashboard_route . "index", $production)->withErrors("لطفا یک ماشین جهت تخصیص انتخاب نمایید.");
        }
        $machine_id = $request->$machine_id_name;

        // $dashboard_info=  ($machine_type->machine_module_type->directory_namespace."\ProductionCard\DashboardController")::$info;
        $machine_allocation_info = ($machine_type->machine_module_type->directory_namespace . "\ProductionCard\MachineAllocationController")::$info;

        $machine = Machine::find($machine_id);

        return redirect()->route(
            $machine_allocation_info["route"] . "select_band",
            [$machine_id, $machine_type, $production, true]
        );
    }

    public function checkPermission(Production $production)
    {

        $result = ProductionCardController::checkPermissionConditions($production, MachineAllocationController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

    /********* General Function ***********/


    public static function MaterialFlow($allocation, $machine)
    {

        foreach ($allocation->items as $allocation_item) {

            $production = $allocation_item->production;
            // Check BOM Exists
            if ($production->product->bill_of_material()->count() == 0) {
                return [
                    "result" => false,
                    "error" => "BOM کالای" . $production->product->fullCaption() . " تعریف نشده است."
                ];
            }

            /* به دست آورد مسیر تولید */
            $line_product_station = LineProductStation::where([
                "product_id" => $production->product_id,
                "machine_type_id" => $machine->machine_type_id
            ])->
            first();

            if (!$line_product_station) {
                return [
                    "result" => false,
                    "error" => "  برای  تولید کالای " . $production->product->fullCaption() . " توسط ماشین های " . $machine->machine_type->caption . " مسیری یافت نشد."
                ];

            }


            // انتخاب اولین BOM از مسیر برای تخصیص
            $bom = $production->product->get_first_bom_from_route($machine);
            if (!$bom) {
                $product_route = ProductRoute::find($line_product_station->product_route_id);

                return [
                    "result" => false,
                    "error" => "  برای  تولید کالای " . $production->product->fullCaption() . " توسط ماشین های " . $machine->machine_type->caption . " (" . $product_route->fullCaption() . ") " . " هیچ BOMی تعریف نشده است."
                ];

            }

            if ($bom->items->count() == 0) {
                return [
                    "result" => false,
                    "error" => $bom->caption . "  برای کالای " . $production->product->fullCaption() . " ;به صورت کامل تعریف نشده است."
                ];
            }

            //گرفتن گراف جریان مواد
            $material_flow = MaterialFlow::
            where("bill_of_material_id", $bom->id)->
            where("machine_type_id", $machine->machine_type_id)->
            where("band_code", $allocation_item->band_code)->
            get();
            if (count($material_flow) == 0) {
                return [
                    "result" => false,
                    "error" => "گراف جریان مواد برای " . $production->product->fullCaption() . " تعریف نشده است،"
                ];
            }
            // چک کردن اینکه گراف جریان مواد به درستی تعریف شده است یا خیر
            $material_flow_graph_link_count = MaterialFlow::
            where("bill_of_material_id", $bom->id)->
            where("machine_type_id", $machine->machine_type_id)->
            count();
            if ($material_flow_graph_link_count < $bom->items->count() * $allocation->items()->count()) {
                return [
                    "result" => false,
                    "error" => "گراف جریان مواد برای " . $production->product->fullCaption() . " به صورت کامل تعریف نشده است،"
                ];
            }

            // مشخصات اطلاعات کالای کارت تولید قبلی را حذف می کنیم.
            CurrentMachineInput::
            where("allocation_id", $allocation->id)->
            where("machine_id", $machine->id)->
            delete();

            // به دست آوردن شناسه باند ورودی
            $machine_type_input_band = MachineTypeInputBand::
            join("machine_type_input_band_goods_kind", "machine_type_input_band_id", "machine_type_input_bands.id")->
            where([
                "active_status_id" => 1200, // فعال
                "machine_type_id" => $machine->machine_type_id,
                "goods_kind_id" => 2 // نخ
            ])->
            select("machine_type_input_bands.id", "input_line_number")->
            first();

            $input_band_count = $machine_type_input_band->input_line_number ?? 0;

            if ($input_band_count < 1) {
                return [
                    "result" => false,
                    "error" => "مشخصات باند ورودی برای " . " رسته کالایی نخ " . " یافت نشد، لطفا با پشتیبانی سیستم تماس بگیرید."
                ];
            }


            $input_line_code_count = BOMItem::where(["bill_of_material_id" => $bom->id])->count();
            // بررسی محدودیت تعداد ورودی های ماشین
            if ($input_line_code_count > $input_band_count) {
                return [
                    "result" => false,
                    "error" => "با توجه به محدودیت تعداد خط های ورودی در " . "ووردی نخ " . "  امکان تخصیص وجود ندارد."
                ];

            }


            $bom_material_items = BOMItem::where(["bill_of_material_id" => $bom->id])->
            groupBy("material_id")->
            get();

            foreach ($bom_material_items as $bom_material_item) {

                $material_info = BOMItem::where(["bill_of_material_id" => $bom->id])->
                where("material_id", $bom_material_item->material_id)->
                selectRaw("
                       amount,
                       percent_of_use,
                       count(id) as number,
                      sum(amount * percent_of_use * number /100) as amount_required,
                      min(input_line_code) as input_line_code_from,
                      max(input_line_code) as input_line_code_to,
                      productive_consume_warehouse_type_id,
                      productive_consume_warehouse_id,
                      sampling_consume_warehouse_type_id,
                      sampling_consume_warehouse_id"
                )->
                first();

                $amount_required = $material_info->amount_required * $allocation->getAllocationAmount();
                $consume_warehouse_id = BOMItem::getConsumeWarehouseId($material_info, $production, $machine);

                $graph_link = MaterialFlow::where(["bill_of_material_item_id" => $bom_material_item->id])->first();
                // به ازای لینکی که در گراف وجود دارد یک درخواست یک ردیف ورودی ثبت می کند.
                CurrentMachineInput::CreateOrUpdate(
                    $machine_type_input_band->id,
                    0,   //  کد خط ورودی
                    $bom_material_item->material_id,
                    $material_info->amount,
                    $amount_required,
                    $material_info->percent_of_use,
                    2,
                    $allocation->id,
                    $machine->id,
                    $allocation_item->product_id,
                    $allocation_item->production_id,
                    $graph_link->band_code,
                    $material_info->number,
                    $material_info->input_line_code_from,
                    $material_info->input_line_code_to,
                    $consume_warehouse_id,
                    $bom_material_item->warehouse_id,
                    $bom_material_item,
                    1

                );

            }
        }


        return ["result" => true, "bom" => $bom];
    }

    public static function ConfirmSubmit(Request $request, Machine $machine, Production $production)
    {


        $band_id = "band_1"; // چون فقط یک باند دارد
        $band_name = "band_name_1";// مقدار تخصیص به باند 1
        $band_amount = "band_amount_1";
        $allocation_amount = $request->$band_amount;
        $number_of_doffs_done = 0;

        // گرفتن تعداد داف
        $result_number_of_doff = self::getNumberOfDoff($production, $allocation_amount, $machine, $request);
        if (!$result_number_of_doff["result"]) {
            return $result_number_of_doff;
        }
        $max_number_of_doffs = $result_number_of_doff["number_of_doff"];
        $amount_of_each_doffs = $result_number_of_doff["amount_of_each_doffs"];
        $packing_type_doffs = $result_number_of_doff["packing_type_doffs"];

        // انتخاب اولین مسیر محصول، حتما هم وجود دارد
        $line_product_station = LineProductStation::where([
            "product_id" => $production->product_id,
            "machine_type_id" => $machine->machine_type_id
        ])->first();

        //چک کردن کانال تولید جاری ماشین و کانال تولید کارت تولید
        $result = ProductionChannel::CheckProductionChannelForAllocation(
            $machine, $production, $allocation_amount,
            $line_product_station->station_operation_id,
            $line_product_station->station_operation->station_operation_category_id
        );
        if (!$result["result"]) {
            if (isset($result["error"])) {
                return $result;
            } else {
                $production_channel_type = $result["production_channel_type"];
                $station_operation_category_id = $result["station_operation_category_id"];
                // به صورت اتوماتیک یک کانال ایجاد می کنیم.
                $result_production_channel = GeneralProductionChannelController:: create_production_channel($machine, $production_channel_type, $production_channel_type->min_capacity, $production_channel_type->max_capacity, true, $station_operation_category_id);
                if (!$result_production_channel["result"]) {
                    return $result_production_channel;
                }
            }
        }

        // ثبت یک تخصیص با وضعیت معلق
        event(new MachineAllocationEvent(
            $production,
            $machine,
            "Warps",
            $request->$band_id,
            $allocation_amount,
            $number_of_doffs_done,
            $max_number_of_doffs,
            $amount_of_each_doffs,
            null,
            null,
            $packing_type_doffs
        ));

        // گرفتن تخصیص معلق
        $allocation = Allocation::where([
            "machine_id" => $machine->id,
            "status_id" => 5310005
        ])->first();

        //به ازای هر ورودی ماشین یک درخواست ثبت شود.
        $result = self::MaterialFlow($allocation, $machine);
        if (!$result["result"]) {
            return $result;
        }

        $bom = $result["bom"];
        // بررسی امکان تخصیص با توجه به موجودی انبار
        $permutation_list = Allocation::getMaxAllocationAmountAccordingToWarehouse($allocation, $allocation_amount, $bom);

        if (count($permutation_list["permutation_list"]) == 0
            || $permutation_list["end_result"][0]["amount_can_be_produced"] < $allocation_amount
        ) {

            $message = GeneralMachineAllocationController::GetMessagePermutation($permutation_list, $production, $machine);

            return [
                "result" => false,
                "error" => $message
            ];

        }

        // محاسبه ضریفت مصرف کانال تولید برای تخصیص
       $result_consumption_percent  = BOM::GetConsumptionOfProductionChannel($bom);

        if (!$result_consumption_percent["result"]) {
            return $result_consumption_percent;
        }
        $allocation->consumption_percent_of_production_channel=$result_consumption_percent["value"];
        // اضافه کردن مقدار تخصیص به کانال تولید
        $production_channel_type = $production->getProductionChannelType();
        if (!$production_channel_type) {
            return [
                "result" => false,
                "error" => "نوع کانال تولید برای کارت تولید مشخص نشده است."
            ];
        }

        // انتخاب اولین مسیر محصول، حتما هم وجود دارد
        $line_product_station = LineProductStation::where([
            "product_id" => $production->product_id,
            "machine_type_id" => $machine->machine_type_id
        ])->first();
        $result_add_allocation = Allocation\MachineAllocationProductionChannel::AddAllocation($machine, $allocation, $production_channel_type->id, $line_product_station->station_operation->station_operation_category_id, $allocation_amount);
        if (!$result_add_allocation["result"]) {
            return $result_add_allocation;
        }

        $allocation_status = 5310040; // تخصیص رزرو شده

        // تغییر وضعیت تخصیص فعلی
        $allocation->status_id = $allocation_status;

        // به دست آوردن اولویت کارت
        switch ($production->production_type_id) {
            case 1:
                $priority_number = $machine->ReserveAllocation()->count() + 1;
                break;
            case 2:
                return [
                    "result" => false,
                    "error" => "تخصیصی که باید بعد از آن کارت نمونه گیری تخصیصی داده شود، یافت نشد."
                ];
//                $after_allocation = Allocation::find($reserve_after_allocation_id);
//                if (!$after_allocation) {
//                    return [
//                        "result" => false,
//                        "error" => "تخصیصی که باید بعد از آن کارت نمونه گیری تخصیصی داده شود، یافت نشد."
//                    ];
//                }
//                $priority_number = $after_allocation->priority_number + .5;
                break;
            default:
                return [
                    "result" => false,
                    "error" => "نوع کارت تولید مشخص نشده است"
                ];
        }

        $allocation->priority_number = $priority_number;
        $allocation->save();

        // تغیر وضعیت همه آیتم های تخصیص
        foreach ($allocation->items as $item) {

            $item->status_id = $allocation_status;
            $item->save();

// بروز رسانی وضعیت کارت تولید
            if ($item->production->waiting_status_id == "7201" . "001") { // در انتظار تخصیص
                $item->production->waiting_status_id = "7201" . "002"; // در انتظار نصب و راه اندازی
                $item->production->save();
                event(new ProductionCardLogEvent($item->production));
            }
        }

        // آخرین وضعیت  قبل از تخصیص ماشین
        $machineLog = MachineLog::create();

        $machineLog->machine_event_type_id = 90; // وضعیت قبل از تخصیص ماشین
        event(new MachineLogEvent($machine, $machineLog));


        // لاگ تخصیص جدید ماشین
        $machineLog = MachineLog::create();
        $machineLog->machine_event_type_id = 92;
        $machineLog->allocation_id = $allocation->id;
        event(new MachineLogEvent($machine, $machineLog));

        Production::sendSmsAfterAllocation($production, $machine);


        Allocation::updatePriorityNumber($machine);


        return [
            "result" => true,
            "message" => "عملیات تخصیص با موفقیت انجام شد."
        ];

    }

    public static function getNumberOfDoff(Production $production, $all_allocation_amount, $machine, Request $request)
    {


        //         حداکثر مقدار جهت داف (متر)
        $packing_type_max_amount = [];
        foreach ($production->packing_types as $production_packing_type) {
            $first_layer = $production_packing_type->packing_type->layers->first();
            if (!$first_layer) {
                return [
                    "result" => false,
                    "error" => "لایه های بندی " . $production_packing_type->packing_type->caption . " به درستی تعریف نشده است، لطفا با پشتیبانی تماس بگیرید."
                ];
            }
            $carrier_type = $first_layer->carrier_type;
            if (!$carrier_type) {
                return [
                    "result" => false,
                    "error" => "اطلاعات  " . $carrier_type->caption . " به صورت کامل ثبت نشده است، لطفا با پشتیبانی تماس بگیرید."
                ];
            }

            $packing_type_max_amount[$production_packing_type->packing_type_id] = $carrier_type->max_band_capacity * $carrier_type->max_band_number;
        }


        if ($production->packing_types->count() == 1) {
            // یک نوع بسته بندی را خودمان محاسبه می کنم.
            $n = 1;
            $packing_type_id = $production->packing_types()->first()->packing_type_id;
            while ($all_allocation_amount / $n > $packing_type_max_amount[$packing_type_id]) {
                $n++;
            }
            $amount_of_each_doffs = round($all_allocation_amount / $n);
            $packing_type_doffs[$packing_type_id]["max_number_of_doffs"] = $n;
            $packing_type_doffs[$packing_type_id]["amount_of_each_doffs"] = $amount_of_each_doffs;
            $packing_type_doffs[$packing_type_id]["packing_type_id"] = $packing_type_id;
            return [
                "result" => true,
                "number_of_doff" => $n,
                "amount_of_each_doffs" => $amount_of_each_doffs,
                "packing_type_doffs" => $packing_type_doffs
            ];
        } elseif ($production->packing_types->count() > 1) {
            $min_packing_type_amount = min($packing_type_max_amount);
            $packing_type_doff_number = $request->packing_type_doff_number;
            $doff_amount = 0;
            $n = 0;
            // بیش از یک نوع بسته بندی خود کاربر وارد می کند و ما فقط چک می کنیم که درست باشد.
            foreach ($production->packing_types as $production_packing_type) {
                if (!isset($packing_type_doff_number[$production_packing_type->packing_type_id]) ||
                    $packing_type_doff_number[$production_packing_type->packing_type_id] < 0
                ) {
                    return [
                        "result" => false,
                        "error" => "تعداد داف  " . $production_packing_type->packing_type->caption . " به درستی ثبت نشده است."
                    ];
                }
                $amount_of_each_doffs = $packing_type_max_amount[$production_packing_type->packing_type_id] * $packing_type_doff_number[$production_packing_type->packing_type_id];
                $doff_amount += $amount_of_each_doffs;
                $max_number_of_doff = $packing_type_doff_number[$production_packing_type->packing_type_id] + 0;
                $packing_type_doffs[$production_packing_type->packing_type_id]["max_number_of_doffs"] = $max_number_of_doff;
                $packing_type_doffs[$production_packing_type->packing_type_id]["amount_of_each_doffs"] = $max_number_of_doff == 0 ? 0 : $amount_of_each_doffs / $max_number_of_doff;
                $n += $packing_type_doffs[$production_packing_type->packing_type_id]["max_number_of_doffs"];

                $packing_type_doffs[$production_packing_type->packing_type_id]["packing_type_id"] = $production_packing_type->packing_type_id;
            }

            if ($doff_amount >= $all_allocation_amount && $doff_amount - $min_packing_type_amount <= $all_allocation_amount) {

                return [
                    "result" => true,
                    "number_of_doff" => $n,
                    "amount_of_each_doffs" => $doff_amount / $n,
                    "packing_type_doffs" => $packing_type_doffs,
                    "doff_amount" => $doff_amount,
                    "all_allocation_amount" => $all_allocation_amount
                ];
            } else {
                return [
                    "result" => false,
                    "error" => "تعداد داف برای برای هر نوع بسته بندی به درستی انتخاب نشده است." . "<br/>" .
                        "مقدار تخصیص: " . $all_allocation_amount . " " . $production->product->unit->caption . "<br/>." .
                        "جمع کل مقدار داف ها: " . $doff_amount . " " . $production->product->unit->caption . "<br/>"
                ];
            }

        } else {
            return [
                "result" => false,
                "error" => "نوع بسته بندی های مجاز برای کارت تولید مشخص نشده است."
            ];
        }
    }


}
