<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer\Machine;

use App\Events\Form\PackingLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Warps\Matthys\Machine\DashboardController;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Machine\Allocation\AllocationDoffs;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineTypeOutputBandGoodsKind;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\Utility\SmartObject;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EndOfWarpingController extends Controller
{
    public static $info = [
        "route" => "warps.karl_mayer.machine.end_of_warping.",
        "enable_status" => ["005"],
        "button" => ["caption" => "پایان چله کشی", "class" => "btn-primary"],
//        "message"       => [ "confirm" => "آیا پایان برگردان اطمینان دارید" ],
        "view_path" => "goods_kind_process.warps.karl_mayer.machine.end_of_warping.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.karl_mayer.machine.dashboard.";

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
            return back()->withErrors("تخصیص جاری برای ماشین وجود ندارد، لطفا با مسئول مربوطه تماس بگیرید.");
        }
        $machine_allocation = $allocation->items()->first();
        if (!$machine_allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین وجود ندارد، لطفا با مسئول مربوطه تماس بگیرید.");
        }


        $machine_type_output_band_goods_kind = MachineTypeOutputBandGoodsKind::
        join("machine_type_output_bands", "machine_type_output_band_id", "machine_type_output_bands.id")->
        where([
            "machine_type_id" => $machine->machine_type_id,
            "goods_kind_id" => 3 // چله
        ])->first();

        if (!$machine_type_output_band_goods_kind) {
            return back()->withErrors("خروجی های ماشین تعریف نشده است، لطفا با مسئول مربوطه تماس بگیرید.");
        }
        $packing_types = $machine_allocation->production->packing_types;
        if (count($packing_types) == 0) {
            return back()->withErrors("نوع بسته بندی مشخص نشده است.");
        }
        $production_form = $machine->getCurrentProductionForm();
//        $production_form_carrier_code = $production_form->carrier->code ?? "";
//        if ($production_form_carrier_code == "") {
//            return back()->withErrors("شماره غلطک چله برای فرم تولید ثبت نشده است.");
//        }

        $gross_weight=0;
        // محاسبه مقدار اصلی چله
        switch ($machine_type_output_band_goods_kind->machine_type_calculation_method_for_unit_id) {
            case 1:// ورود توسط اپراتور
                $amount = "";
                break;
            case 2: // خوانش از اشیاء
                if($machine_allocation->product->unit->weight_conversion_rate!=1){
                    return back()->withErrors("واحد اصلی کالا غیر وزنی می باشد و امکان محاسبه آن توسط اشیاء هوشمند وجود ندارد، لطفا با پشیتبانی تماس بگیرید.");
                }
                $result = SmartObject::getContour($machine_type_output_band_goods_kind->smart_object_for_unit);
                if ($result["result"]) {

                    $amount = floatval($result["data"]["weight"]);
                    $gross_weight=$amount;
                } else {
                    return back()->withErrors($result["error"]);
                }
                break;
            case 3: // خوانش از مقدار سیستم
                $amount = $allocation->getAllocationAmount();
                break;
        }

        // محاسبه مقدار وزن چله
        switch ($machine_type_output_band_goods_kind->machine_type_calculation_method_for_sub_unit_id) {
            case 1:// ورود توسط اپراتور
                $sub_amount = "";
                break;
            case 2: // خوانش از اشیاء
                if($machine_allocation->product->sub_unit->weight_conversion_rate!=1){
                    return back()->withErrors("واحد اصلی کالا غیر وزنی می باشد و امکان محاسبه آن توسط اشیاء هوشمند وجود ندارد، لطفا با پشیتبانی تماس بگیرید.");
                }
                $result = SmartObject::getContour($machine_type_output_band_goods_kind->smart_object_for_sub_unit);
                if ($result["result"]) {

                    $gross_weight = floatval($result["data"]["weight"]);
                    $sub_amount=$gross_weight;
                } else {
                    return back()->withErrors($result["error"]);
                }
                break;
            case 3: // خوانش از مقدار سیستم
                $sub_amount = $machine_allocation->production->number * $machine_allocation->production->product->weight;
                break;
        }


        $product = $machine_allocation->production->product;

        // این دستور به صورت موفقت است.
        $bom_items = BOMItem::where("product_id", $product->id)->groupBy("material_id")->get();

        // اگر درجه بندی نداریم، باید درجه اصلی در رسته کالایی را انتخاب کنیم
        $main_degree_result = Degree::getMainDegree(3); // چله

        if (!$main_degree_result["result"]) {
            return back()->withErrors($main_degree_result["error"]);
        }
        $main_degree = $main_degree_result["degree"];

        // تولید لات برای فرم جاری
        Warps::ChangeLot($allocation, false, $production_form->id);


        return view($this->view_path . "index", compact("machine", "product", "amount", "sub_amount",
            "machine_type_output_band_goods_kind", "packing_types",
            "production_form","gross_weight",
            "bom_items", "main_degree"));

    }

    public function submit(Request $request, Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $result = self::submitHasAnError($request, $machine);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }


        $result = self::submitConfirm($request, $result);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }


        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "پایان چله کشی با موفقیت ثبت شد و بسته بندی " . $result["packing_form"]->code . " ایجاد گردید."]);

    }

    public static function submitHasAnError(Request $request, Machine $machine)
    {

        $allocation = $machine->getCurrentAllocation();

        $production_form = $machine->getCurrentProductionForm();


        if ($allocation && !$production_form) {
            return [
                "result" => false,
                "error" => "فرم تولید جاری یافت نشد، لطفا با مسئول مربوطه تماس بگیرید."
            ];
        }


        if ($production_form && $production_form->packing_type) {
            $production_form_item = $production_form->items()->first();

            $result_amount_form_weight = PackingType::getAmountFromWeight($production_form_item->product, $production_form->packing_type, $request->gross_weight, 0, $production_form->carrier, $request->amount, null, true);
            if (!$result_amount_form_weight["result"]) {
                return $result_amount_form_weight;
            }
            if ($result_amount_form_weight["final_amount"] <= 0) {
                return [
                    "result" => false,
                    "error" => "مقدار تولید به درستی وارد نشده است."
                ];
            }
            if ($result_amount_form_weight["weight"] <= 0) {
                return [
                    "result" => false,
                    "error" => "مقدار وزن خالص به درستی وارد نشده است."
                ];
            }
            $result_doff = AllocationDoffs::HasAnyDoff($allocation->id, $production_form->packing_type->id);
            if (!$result_doff["result"]) {
                return [
                    "result" => false,
                    "error" => $result_doff["error"]
                ];

            }
            $allocation_doff = $result_doff["allocation_doff"];
        }

        // اگر درجه بندی نداریم، باید درجه اصلی در رسته کالایی را انتخاب کنیم
        $main_degree_result = Degree::getMainDegree(3); // چله

        if (!$main_degree_result["result"]) {
            return [
                "result" => false,
                "error" => $main_degree_result["error"]
            ];

        }
        $main_degree = $main_degree_result["degree"];

        return [
            "result" => true,
            "allocation" => $allocation,
            "machine" => $machine,
            "production_form" => $production_form,
            "main_degree" => $main_degree,
            "result_amount_form_weight" => $result_amount_form_weight ?? null,
            "allocation_doff" => $allocation_doff ?? null

        ];
    }

    public static function submitConfirm(Request $request, $resultHasAnError)
    {

        $allocation = $resultHasAnError["allocation"];
        $machine = $resultHasAnError["machine"];
        $production_form = $resultHasAnError["production_form"];
        $main_degree = $resultHasAnError["main_degree"];
        $result_amount_form_weight = $resultHasAnError["result_amount_form_weight"];
        $doff_complete = true;
        $allocation_doff = $resultHasAnError["allocation_doff"];

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 1000;
        $machineLog->save();


        if ($allocation) {

            // تغییر وضعیت فرم تولید و ایجاد یک فرم بسته بندی جدید

            //بسته بندی شده
            // 7202002 = پایان برگردان
            $production_form->ChangeStatus(7202004, $text = "", $event_id = 7202002);


            // ایجاد فرم بسته بندی
            $packing_form = PackingForm::create([
                "packing_type_id" => $production_form->packing_type_id,
                "carrier_id" => $production_form->carrier_id,
                "status_id" => 7007005, // در انتظار تحویل به انبار,
                "degree_id" => $main_degree->id,
                "weight" => $result_amount_form_weight["weight"],
                "gross_weight" => $result_amount_form_weight["gross_weight"],
            ]);

            event(new PackingLogEvent($packing_form, 7007001));

            // چون فقط یک آیتم و یک فرم تولید دارد.
            $production_form_item = $production_form->items()->first();
            $production_form_item_lot_number = $production_form_item->lot_numbers()->first();

            $production_form_item_lot_number->amount = $result_amount_form_weight["final_amount"];
            $production_form_item_lot_number->save();

            $production_form_item->amount = $result_amount_form_weight["final_amount"];
            $production_form_item->amount_after_control = $result_amount_form_weight["final_amount"];
            $production_form_item->final_amount = $result_amount_form_weight["final_amount"];
            $production_form_item->sub_amount = $result_amount_form_weight["sub_amount"];
            $production_form_item->save();
            // در اینجا اطلاعات فرم جاری کاملا محاسبه شده است و فقط باید بسته بندی ایجاد شود.

            // به ازای هر آیتم بسته بندی یک ردیف ایجاد می کنیم
            $packing_form_item = PackingFormItem::create([
                "packing_form_id" => $packing_form->id,
                "production_form_item_id" => $production_form_item->id,
                "production_form_item_lot_number_id" => $production_form_item_lot_number->id,
                "fabric_raw_grading_id" => null,
                "product_id" => $production_form_item->product_id,
                "lot_number_id" => $production_form_item_lot_number->lot_number_id,
                "degree_id" => $main_degree->id,
                "amount" => $result_amount_form_weight["final_amount"],
                "amount_after_control" => $result_amount_form_weight["final_amount"],
                "final_amount" => $result_amount_form_weight["final_amount"],
                "sub_amount" => $result_amount_form_weight["sub_amount"],
                "init_sub_amount" => $result_amount_form_weight["sub_amount"],
                "status_id" => 7006003, // بسته بندی شده
                "band_code" => $production_form_item->band_code
            ]);
            $packing_form_item->getCode($production_form_item->band_code, 1);

            // یکی به تعداد داف ها اضافه می کنیم، اگر تعداد داف ها کامل نشده بود،
            // وضعیت تولید ماشین: در انتظار شروع چله کشی و یک فرم تولید جدید ثبت می کنیم.
            foreach ($allocation->items as $item) {
                $item->number_of_doffs_done = $item->number_of_doffs_done + 1; // یکی به تعداد داف ها اضافه می کنیم.
                $item->save();
                // تعداد داف برای بسته بندی هم یکی اضافه می گردد.
                $allocation_doff->number_of_doffs_done=$allocation_doff->number_of_doffs_done + 1;
                $allocation_doff->save();
                if ($item->number_of_doffs_done < $item->max_number_of_doffs) {
                    $doff_complete = false;
                }
            }

            if ($doff_complete) {
                // تغییر وضعیت تخصیص فعلی
                $allocation->status_id = 5310020; //  تخصیص های پایان یافته
                $allocation->save();
                foreach ($allocation->items as $item) {
                    $item->status_id = 5310020; //  تخصیص های پایان یافته
                    $item->save();
                }
            } else {
                // تعداد داف ها کامل نشده است و باید یک فرم تولید جدید ایجاد کنیم.
                // ایجاد یک فرم تولید در انتظار چله کشی برای تخصیص
                EndOfShelvingController::CreateProductionFromForWarps($machine, $allocation, $machineLog, $production_form->packing_type);
            }

            if ($production_form->carrier) {
                $carrier_status_id = 5320004  //  پر شده در انتظار تحویل به انبار
                ;
                $production_form->carrier->SetStatus(
                    $carrier_status_id,
                    null,
                    5320105,
                    null,
                    $production_form->machine->id
                );
            }

        }

        // اگر تعداد داف ها به حد نصاب رسید بود، انگاه فرم های رزور را بررسی می کنیم.
        if ($doff_complete) {

            $reserve_allocation = $machine->getFirstReserveAllocation();
            if ($reserve_allocation) {

                foreach ($reserve_allocation->items as $item) {
                    $item->status_id = 5310010; //  تخصیص داده شده
                    $item->production_start_date = Carbon::now();
                    $item->save();
                }
                $reserve_allocation->status_id = 5310010; //  تخصیص جاری
                $reserve_allocation->save();

                $machine->setStatus(
                    null,
                    53002, // خاموش
                    7203003, //در انتظار پایان قفسه گذاری
                    2010);

                // تغییر کانال جاری ماشین
                ProductionChannel::ChangeChannel($reserve_allocation);


            } else {

                $machine->setStatus(
                    null,
                    53002, // خاموش
                    7203001, //نداشتن سفارش
                    4000);

            }
        }
        event(new MachineLogEvent($machine, $machineLog));

        if ($allocation) {

            // ثبت مقدار مصرف
            MachineAllocationMaterialConsumed::registerNewConsumed($allocation, $machine, null, $machineLog, $result_amount_form_weight["final_amount"]);

        }

        return ["result" => true, "packing_form" => $packing_form ?? null];
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
