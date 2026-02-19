<?php

namespace App\Models\GoodsKindProcess\Warps;

use App\Events\Machine\MachineLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestFormItem;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestFormPackingType;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\CurrentMachineMaterialFlow;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Product\BOM;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineTypeInputBandPackingType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionFormItemLotNumber;
use App\Models\Utility\Option;
use App\Models\Warehouse\WarehouseProduct;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Warps extends Model
{
    use HasFactory;
    use Loggable;

    /***
     * @param $product_id
     * پیدا کردن کد کالای چله ها از BOM
     *
     * @return mixe
     */

    public static function getLotNumber($allocation, $machine, $band_code, $product)
    {

        $machine_effective_lot_code = $machine->machine_type->lot_effective_code;

// رسته نخ
        $current_input_yarns = CurrentMachineInput::where([
            "allocation_id" => $allocation->id,
            "goods_kind_id" => 2
        ])->
        orderBy("material_id")->
        orderBy("lot_number_id")->
        get();


        if (count($current_input_yarns) == 0) {
            return [
                "result" => false,
                "message" => "لات جاری نخ ماشین ثبت نشده است."
            ];
        }


        // بررسی اینکه لات چله از قبل وجود داشته یا خیر

        $product_raw_lot = LotNumber::where([
            "product_id" => $product->id,
            "machine_lot_effective_code" => $machine_effective_lot_code,
        ]);

        // لات ها را به ترتیب رسته کالایی ( نخ) و سپس شناسه مواد اولیه در ستون های lot_number_1 تا lot_number_10 قرار می دهیم اگر مورد مشابه وجود داشت همان را برمی گردانیم در غیر این صورت یک لات جدید ایجاد می کنیم.
        $k = 0;
        $lot_number = 1;
        //بررسی نخ
        $k = 0;
        foreach ($current_input_yarns as $yarn_item) {
            $product_raw_lot = $product_raw_lot->where("lot_number_" . ($lot_number) . "_id", $current_input_yarns[$k]->lot_number_id);
            $k++;
            $lot_number++;
        }

        $product_raw_lot = $product_raw_lot->first();

        // بازگرداندن لات قبلی و پایان
        if (isset($product_raw_lot)) {
            return [
                "result" => true,
                "lot_number" => $product_raw_lot
            ];
        }

        // ایجاد لات جدید
        $before_lot = LotNumber::where([
            "product_id" => $product->id
        ])->orderByDesc("id")->first();

        // چک کردن لات موثر نخ تغییر کرده است یا خیر
        $yarn_lot_is_equal = true;
        $k = 0;
        $lot_number = 1;

        foreach ($current_input_yarns as $yarn_item) {
            $str = "lot_number_" . ($lot_number) . "_id";
            if (isset($before_lot->$str) && $before_lot->$str !=
                $current_input_yarns[$k]->lot_number_id) {
                $yarn_lot_is_equal = false;
            }
            $k++;
            $lot_number++;
        }
        $yarn_lot_code = $before_lot ?
            ($before_lot->lot_effective_code2 ?? 1) +
            (
            $yarn_lot_is_equal
                ?
                0 : 1 // یک شماره اضافه شود در صورت تفاوت حداقل یکی از لات های نخ با قبلی
            )
            : 1;


        $new_lot_code = Option::getFormatCode($machine_effective_lot_code, 2) .
            Option::getFormatCode($yarn_lot_code, 3);

        $new_lot_number = new LotNumber();
        $new_lot_number->product_id = $product->id;
        $new_lot_number->machine_lot_effective_code = $machine_effective_lot_code;

// شناسه کالای چله
        $lot_number = 1;

        // شناسه کالای نخ
        $k = 0;
        foreach ($current_input_yarns as $yarn_item) {
            $str = "lot_number_" . ($lot_number) . "_id";
            $new_lot_number->$str = $current_input_yarns[$k]->lot_number_id;
            $k++;
            $lot_number++;

        }

        $new_lot_number->lot_effective_code1 = $yarn_lot_code;
        $new_lot_number->code = $new_lot_code;
        $new_lot_number->user_id = Auth::user()->id;
        $new_lot_number->machine_id = $machine->id;

        $new_lot_number->save();

        return [
            "result" => true,
            "lot_number" => $new_lot_number,
            "is_new_lot" => true
        ];

    }

    public static function ChangeLot($allocation, $production_status_id = false, $production_form_id = false)
    {

        if (!$allocation) {
            return ["result" => false, "error" => "تخصیص یافت نشد."];
        }
        $added_lot = false;
        if (!$production_status_id) {

            $production_form = ProductionForm::where([
                "machine_id" => $allocation->machine_id
            ])->whereIn(
            // وضعیت های جاری فرم تولید چله کشی
                "status_id", MachineModuleType::getChecklist(
                $allocation->machine->machine_type->machine_module_type_id,
                "current_production_form_status")
            )->
            orderBy("id")->
            first();

        } else {
            $production_form = ProductionForm::where([
                "machine_id" => $allocation->machine_id
            ])->where("status_id", $production_status_id)->
            first();
        }

        if ($production_form_id) {
            $production_form = ProductionForm::find($production_form_id);
        }


        if (!$production_form) {
            return ["result" => false, "error" => "فرم تولید یافت نشد."];
        }
        $message = "";

        $machine_log = MachineLog::getLastLog($allocation->machine);


        foreach ($production_form->items as $item) {

            if ($item->allocation_id == $allocation->id) {
                // ایجاد لات
                $result = Warps::getLotNumber(
                    $allocation,
                    $allocation->machine,
                    $item->band_code,
                    $item->product
                );
                if (!$result["result"]) {
                    $message .= " " . $result["message"] . ": production_form_item=" . $item->id . "\n";
                    continue;
                }

                $result_add_lot = $item->AddLotItem($result["lot_number"]->id, $machine_log);
                $added_lot = $added_lot || $result_add_lot;

                if (!$item->start_machine_log) {
                    $item->setStartMachineLog($machine_log);
                }

                $item->setEndMachineLog($machine_log);
                $item->updateItemAmount();
            }
        }


        if ($added_lot || $message != "") {

            // لاگ ماشین
            $machineLog = new MachineLog();
            $machineLog->machine_event_type_id = 320;
            $message .= " - " . (isset($result["lot_number"]->code) ? $result["lot_number"]->code : "***") . " - " . (isset($result["is_new_lot"]) ? "ایجاد همبافت جدید برای اولین بار" : "همبافت های از قبل تولید شده");
            event(new MachineLogEvent($allocation->machine, $machineLog, $message));

        }

        return ["result" => true];

    }

    public static function getCurrentLot($allocation, $order_desc = false)
    {

        $production_form = $allocation->machine->getCurrentProductionForm();

        // ارسال آخرین لات های هر باند
        if ($order_desc) {
            return ProductionFormItemLotNumber::
            join("production_form_item", "production_form_item.id", "production_form_item_id")->
            where([
                "production_form_item.production_form_id" => $production_form->id ?? -1
            ])->
            orderBy("band_code")->
            orderByDesc("production_form_item_lot_number.id")->
            get();
        }

        return ProductionFormItemLotNumber::where([
            "production_form_id" => $production_form->id ?? -1
        ])->get();
    }

    public static function findWarpsIdFromMachine($product_id, Allocation $allocation)
    {
        $goods_kind_id = GoodsKind::where("caption_en", "Warps")->first()->id;

        $route_id = LineProductStation::
        where([
                "product_id" => $product_id,
                "machine_type_id" => $allocation->machine->machine_type_id
            ]
        )->
        pluck("product_route_id")->toArray();
        $route_id[] = 0;

        $bom = BOM\BOM::where("product_route_id", $route_id)->first();
        // BOM کالا تعریف نشده است.
        if (!$bom) {
            return [0];
            1 / 0;
        }

        $material_list = BOM\BOMItem::join("products", "material_id", "products.id")->
        where([
            "bill_of_material_id" => $bom->id,
            "product_id" => $product_id,
            "goods_kind_id" => $goods_kind_id
        ])->
        select("material_id")->
        pluck("material_id");

        return $material_list;
    }

    public static function warpsCountInWarehouse($product_id, $applicant_id, $product_request_form_id = null)
    {

        // product_id => کد چله
        return ProductRequestForm::productCountInWarehouse($product_id, 40, $applicant_id, $product_request_form_id);
    }


    public static function warpsExistInMachineWarehouse(Allocation $allocation)
    {
        $warps_machine_input = CurrentMachineInput::where(
            [
                "allocation_id" => $allocation->id,
                "goods_kind_id" => 3
            ])->
        groupBy("material_id","input_line_code")->
        get();

        $warps_is_in_warehouse = true;

        foreach ($warps_machine_input as $input) {
            // لیست بسته بندی های چله که در انبارک موجود است
            $packing_form_ids = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
            where("packing_forms.warehouse_id", $allocation->machine->warehouse_id)->
            where("packing_form_item.product_id", $input->material_id)->
            where("packing_forms.status_id", 7007003)->//تحویل شده به انبار
            where("warehouse_status_id", 4201)->
            pluck("packing_forms.id")->
            toArray("packing_forms.id");

            // لیست بسته بندی های بالا که در ورودی های ماشین ست شده اند.
            $packing_form_consumption_ids = CurrentMachineInput::where([
                "machine_id" => $allocation->machine_id,
                "material_id" => $input->material_id
            ])->
            whereIn("packing_form_id", $packing_form_ids)->
            groupBy("packing_form_id")->
            pluck("packing_form_id")->
            toArray("packing_form_id");

            $warps_count_in_warehouse = count($packing_form_ids) - count($packing_form_consumption_ids);
            $warps_is_in_warehouse = $warps_is_in_warehouse && $warps_count_in_warehouse >= $input->number;
        }

        return $warps_is_in_warehouse;
    }

    public static function warpsExistInWarehouse($allocation, $product_request_form = null)
    {

        if ($allocation) {
            $warps_machine_input = CurrentMachineInput::where(
                [
                    "allocation_id" => $allocation->id,
                    "goods_kind_id" => 3
                ])->
            get();

            $warps_is_in_warehouse = true;

            foreach ($warps_machine_input as $input) {
                $warps_count_in_warehouse = Warps::warpsCountInWarehouse($input->material_id, $input->consume_warehouse_id, null);
                $warps_is_in_warehouse = $warps_is_in_warehouse && $warps_count_in_warehouse >= $input->number;
            }
        } else {
            $warps_is_in_warehouse = true;

            foreach ($product_request_form->items as $item) {
                $warps_count_in_warehouse = Warps::warpsCountInWarehouse($item->product_id, $product_request_form->applicant_id, $product_request_form->id);
                $warps_is_in_warehouse = $warps_is_in_warehouse && $warps_count_in_warehouse >= $item->min_number_of_packing_forms;
            }
        }

        return $warps_is_in_warehouse;
    }

//    public static function warpsDeliveryConfirmation( Allocation $allocation ) {
//
//        $product_request_form = ProductRequestForm::where( [
//            "status_id"     => 7005004, // در انتظار تایید درخواست کننده
//            "allocation_id" => $allocation->id
//        ] )->first();
//
//        if ( ! isset( $product_request_form ) ) {
//            return back()->withErrors( "وضعیت فرم تحویل چله از انبار 'در انتظار تایید درخواست کننده' نمی باشد." );
//        }
//
//        $product_request_form->confirmRequest();
//
//    }

    public static function getRemainingAmountOfWarps(ProductRequestForm $warps_request_form, ProductRequestFormItem $warps_request_form_item, $end_machine_log = false, $warps_product_id = null)
    {
        $start_machine_log = null;

        $checklist = [
            130, //شروع چله گذاری
            140, // پایان چله گذاری
            290, //پایان تعویض چله
            530, // شروع استخراج چله و چله گذاری (جهت تغییر کالیته)
        ];
        $machine = Machine::where("warehouse_id", $warps_request_form->applicant_id)->first();
        if (!$machine) {
            return [
                "result" => false,
                "amount" => "0",
                "sub_amount" => "0",
                "message" => "برای انبارک  هیچ ماشین یافت نشد.",
                "sum_amount_reserve" => 0,
                "amount_begin_of_weaving" => "0",
            ];
        }
        $machine_log_warping = MachineLog::
        where("machine_id", $machine->id)->
        whereIn("machine_event_type_id", $checklist)->
        orderByDesc("id")->
        first();


        if ($machine_log_warping) {
            // لاگ ماشین در زمان شروع کارت تولید جاری
//            $start_current_end_of_production_machine_log = MachineLog::
//            where( "machine_id", $warps_request_form->applicant_id )->
//            where( "id", ">=", $machine_log_warping->id )->
//            where( "machine_event_type_id", 520 )-> // پایان بافت (کارت تولید جاری)
//            whereNotNull( "shift_work_id" )->
//            orderByDesc( "id" )->
//            first();
            $start_machine_log = MachineLog::
            where("machine_id", $machine->id)->
            where("id", ">=", $machine_log_warping->id)->
            whereNotNull("shift_work_id")->
            orderBy("id")->
            first();

        } else {
            // رکورد چله گذاری یافت نشد.
            return [
                "result" => false,
                "amount" => "0",
                "sub_amount" => "0",
                "message" => "رکورد چله گذاری یافت نشد",
                "sum_amount_reserve" => 0,
                "amount_begin_of_weaving" => "0",
            ];
        }

//        if ( ! $start_current_end_of_production_machine_log ) {
//            return [
//                "result"                  => false,
//                "message"                 => "رکورد قطب های شروع چله گذاری یافت نشد",
//                "amount"                  => "0",
//                "sub_amount"              => "0",
//                "sum_amount_reserve"      => 0,
//                "amount_begin_of_weaving" => "0",
//            ];
//        }

        if (!$end_machine_log) {

            $end_machine_log = MachineLog::
            where("machine_id", $machine->id)->
            whereNotNull("shift_work_id")->
            orderByDesc("id")->
            first();

            if (!$end_machine_log) {
                return [
                    "result" => false,
                    "message" => "رکورد قطب های پایان چله گذاری یافت نشد",
                    "amount" => "0",
                    "sub_amount" => "0",
                    "sum_amount_reserve" => 0,
                    "amount_begin_of_weaving" => "0",
                ];
            }
        }

// گرفتن کد کالای پارچه
//        $current_machine_material_flow = CurrentMachineMaterialFlow::where( [
//            "allocation_id"   => $warps_request_form->allocation_id,
//            "input_line_code" => $warps_request_form_item->input_line_code,
//            "material_id"     => $warps_request_form_item->product_id
//        ] )->first();
//
//        if ( ! $current_machine_material_flow ) {
//            return [
//                "result"                  => false,
//                "message"                 => "رکورد تخصیص در زمان محاسبه متراژ چله یافت نشد",
//                "amount"                  => "0",
//                "sub_amount"              => "0",
//                "sum_amount_reserve"      => 0,
//                "amount_begin_of_weaving" => "0",
//            ];
//        }

        if (count($warps_request_form->forms) == 0) {
            return [
                "result" => false,
                "message" => "رکورد فرم انبار یافت نشد." . " شماره فرم درخواست: " . $warps_request_form->code,
                "amount" => "0",
                "sub_amount" => "0",
                "sum_amount_reserve" => 0,
                "amount_begin_of_weaving" => "0",
            ];
        }

        foreach ($warps_request_form->forms as $forms_item) {

            if(!isset($forms_item->form->item[0]->amount)){
                continue;
            }
           
            $input_amount = $forms_item->form->item[0]->amount;
            $sub_input_amount = $forms_item->form->item[0]->sub_amount;
            $packing_form_item_id = $forms_item->form->item[0]->packing_form_item_id;
            break;
        }
        if (!isset($input_amount)) {
            return [
                "result" => false,
                "message" => "رکورد فرم انبار یافت نشد (کد 2)." . " شماره فرم درخواست: " . $warps_request_form->code,
                "amount" => "0",
                "sub_amount" => "0",
                "sum_amount_reserve" => 0,
                "amount_begin_of_weaving" => "0",
            ];
        }
        $gram = $input_amount / ($sub_input_amount == 0 ? 1 : $sub_input_amount);

//        if ( $input_amount < $amount ) {
//            return [
//                "result"                  => false,
//                "message"                 => "متراژ مصرف محاسبه شده توسط سیستم از متراژ اولیه " . $warps_request_form_item->product->fullCaption() . " بیشتر است.",
//                "amount"                  => round( $input_amount - $amount, 2 ),
//                "sub_amount"              => "0",
//                "sum_amount_reserve"      => $sum_reserve,
//                "amount_begin_of_weaving" => "0",
//            ];
//        }

        $amount_status = Warps::getAmountBeginOfWeaving($start_machine_log, $warps_request_form_item, $warps_product_id);
        // مقدار کنونی چله
        // مقدار کل - مقدار تخصیص های پایان یافته - مقدار بافته شده کارت جاری
        if ($warps_product_id && $warps_product_id == $warps_request_form_item->product_id) {
            $amount = round($input_amount - $amount_status[5310010] - $amount_status[5310020] + $amount_status["woven_amount"], 2);
        } else {
            $amount = 0;
        }

        return [
            "result" => true,
            // مقدار کل
            "input_amount" => $input_amount,
            "packing_form_item_id" => $packing_form_item_id,
            "consumed_amount" => $amount_status[5310010] + $amount_status[5310020],
            "amount" => $amount,
            "sub_amount" => $amount / $gram,
            "sum_amount_reserve" => $amount_status[5310040],
            "amount_begin_of_weaving" => $amount - $amount_status[5310040],
            "current_weaving" => $amount_status[5310010],
            "woven_amount" => $amount_status["woven_amount"],
//            "end_machine_log" => $end_machine_log,
//            "start_machine_log" => $start_machine_log,
        ];


    }

    public static function getAmountBeginOfWeaving(MachineLog $start_warps_machine_log, ProductRequestFormItem $warps_request_form_item, $warps_product_id = null)
    {
        // محاسبه مقدار چله باقمی مانده با توجه به تخصیص های انجام شده برای ماشین

        $amount = [
            5310010 => 0,
            5310020 => 0,
            5310040 => 0,
            "woven_amount" => 0
        ];

        // لیست تخصیص های جاری
        $current_allocation = MachineAllocation::where([
            "band_code" => 1,
            "machine_id" => $start_warps_machine_log->machine_id
        ])->
        where("status_id", 5310010)->first();
        if ($current_allocation) {
            $bom_warps_item = BOM\BOMItem::where([
                "product_id" => $current_allocation->product_id,
                "material_id" => $warps_product_id ?? $warps_request_form_item->product_id
            ])->first();
            $warp_amount = $bom_warps_item->amount ?? 0;

            $amount [5310010] += $current_allocation->allocation_amount * $warp_amount;

        }

        // لیست تخصیص های خاتمه یافته
        $start_allocation_machine_log = MachineLog::find($start_warps_machine_log->id);

        while ($start_allocation_machine_log) {

            $end_allocation_machine_log = MachineLog::where([
                "machine_id" => $start_warps_machine_log->machine_id,
                "machine_event_type_id" => 520
            ])->
            where("id", ">", $start_allocation_machine_log->id)->
            first();

            if ($end_allocation_machine_log) {

                $allocation = $end_allocation_machine_log->allocation;

                if ($allocation) {

                    $machine_allocation = $allocation->items->first();
                    if (isset($machine_allocation)) {

                        $fabric_row_amount = GoodsKind::getAmountFromMachineLog($machine_allocation->product, $start_allocation_machine_log, $end_allocation_machine_log);

                        $bom_warps_item = BOM\BOMItem::where([
                            "product_id" => $machine_allocation->product_id,
                            "material_id" => $warps_product_id ?? $warps_request_form_item->product_id
                        ])->first();
                        $warp_amount = $bom_warps_item->amount ?? 0;

                        $amount [5310020] += $fabric_row_amount * $warp_amount;

//                    if($end_allocation_machine_log->id>9670){
//                    $amount[5310020]= $fabric_row_amount;//$machine_allocation->production_id;
//                    return $amount;
//                    }
                    }

                }

            }
            $start_allocation_machine_log = $end_allocation_machine_log;

        }

        // لیست تخصیص های رزرو
        $list_reserve = MachineAllocation::where([
            "band_code" => 1,
            "machine_id" => $start_warps_machine_log->machine_id
        ])->
        where("status_id", 5310040)->get();
        foreach ($list_reserve as $item) {
            $bom_warps_item = BOM\BOMItem::where([
                "product_id" => $item->product_id,
                "material_id" => $warps_product_id ?? $warps_request_form_item->product_id
            ])->first();
            $warp_amount = $bom_warps_item->amount ?? 0;

            $amount [5310040] += $item->allocation_amount * $warp_amount;

        }

        // محاسبه مقداری از چله که از کارت رزرو یا جاری بافته شده است.
        $before_start_warps_machine_log = MachineLog::where([
            "machine_id" => $start_warps_machine_log->machine_id,
            "machine_event_type_id" => 520
        ])->
        where("id", "<", $start_warps_machine_log->id)->
        orderByDesc("id")->
        first();
        if ($before_start_warps_machine_log) {

            $allocation = $before_start_warps_machine_log->allocation;
            if (!$allocation) {
                $allocation = $current_allocation->allocation ?? null;
            }

            if ($allocation) {

                $machine_allocation = $allocation->items->first();
                if (isset($machine_allocation)) {

                    $fabric_row_amount = GoodsKind::getAmountFromMachineLog($machine_allocation->product, $before_start_warps_machine_log, $start_warps_machine_log);

                    $bom_warps_item = BOM\BOMItem::where([
                        "product_id" => $machine_allocation->product_id,
                        "material_id" => $warps_product_id ?? $warps_request_form_item->product_id
                    ])->first();
                    $warp_amount = $bom_warps_item->amount ?? 0;

                    $amount ["woven_amount"] = $fabric_row_amount * $warp_amount;

//                    if($end_allocation_machine_log->id>9670){
//                    $amount[5310020]= $fabric_row_amount;//$machine_allocation->production_id;
//                    return $amount;
//                    }
                }

            }

        }

        return $amount;

    }

    public static function updateCarrierInCurrentInputOutputBand(Machine $machine, $type, $allocation = null)
    {

        if (!$allocation) {
            $allocation = $machine->getCurrentAllocation();
            if (!isset($allocation)) {
                return false;
            }
        }

        $current_input_list = CurrentMachineInput::
        where(["machine_id" => $machine->id, "allocation_id" => $allocation->id, "goods_kind_id" => 3])->
        orderBy("input_line_code")->
        get();

        switch ($type) {
//            case "setCurrentCarrier":
//                1/0;
//                $production_channel_capacity = 9999999999;
//                // به روز رسانی ورودی های چله در ماشین
//                $product_request_form = ProductRequestForm::getLatestRequestForm( $machine->id, 10 );
//                if ( ! isset( $product_request_form ) ) {
//                    return false;
//                }
//                // لیست آیتم های آولین فرم انبار که بابت درخواست تحویل شده است.
//                if ( ! isset( $product_request_form->forms[0]->form ) ) {
//                    return false;
//                }
//                $form_items = $product_request_form->forms[0]->form->item;
//                foreach ( $current_input_list as $current_input ) {
//
//                    foreach ( $form_items as $item ) {
//
//                        if ( $current_input->input_line_code == $item->io_line_code ) {
//                            $current_input->carrier_id    = $item->carrier_id ?? - 1;
//                            $current_input->lot_number_id = $item->lot_number_id ?? null;
//                            $current_input->save();
//                        }
//                    }
//                    $production_channel_capacity = min( $production_channel_capacity, $item->amount );
//
//                }
//
//                // بروز رسانی ظرفیت کانال تولید و قرار دادن مقدار چله
//              //  ProductionChannel::UpdateProductionChannel( $machine->getCurrentProductionChannel(), $production_channel_capacity );
//
//                break;
            case "updateProductionChannel":
                $production_channel_capacity = 999999999;
                foreach ($current_input_list as $current_input) {
                    // ظرفیت کانال تولید را برابر با مقدار چله موجود در بسته بندی قرار می دهیم.
                    $packing_form = $current_input->packing_form;
                    $packing_form_final_amount = PackingFormItem::where("packing_form_id", $packing_form->id ?? 0)->
                    where("product_id", $current_input->material_id)->sum("final_amount");
                    // هر بار که مقدار کانال را بروز می کند، مقدار اولیه چله را ملاک قرار می دهد.
                    // چون مقدار نهایی، با مصرف شدن در حال کاهش است.

                    $production_channel_capacity = min($production_channel_capacity, $packing_form_final_amount);

                }

                // بروز رسانی ظرفیت کانال تولید و قرار دادن مقدار چله
                ProductionChannel::UpdateProductionChannel($machine->getCurrentProductionChannel(), $production_channel_capacity);

                break;

            case "setEmpty":
                // حذف ورودی ها چله در ماشین
                foreach ($current_input_list as $current_input) {
                    $current_input->carrier_id = null;
                    $current_input->save();
                }

                return true;
                break;
        }


    }

    public static function GetLastProductRequestFromDelivered()
    {
        $allocation = $machine->getCurrentAllocation();

        $warps_form = WarpsRequestForm::where([
            "applicant_id" => $machine->warehouse->id,
            "applicant_type_id" => 40,
        ])->whereNotIn("status_id", [7005002, 7005006])->
        orderByDesc("id")->first();
    }
}
