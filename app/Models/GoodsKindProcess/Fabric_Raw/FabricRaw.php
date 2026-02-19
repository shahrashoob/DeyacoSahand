<?php

namespace App\Models\GoodsKindProcess\Fabric_Raw;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestFormItem;

use App\Models\LineProduct\GoodsKindPropertyOption;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineMaterialFlow;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineLotTmps;
use App\Models\LineProduct\Product;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionFormItemLotNumber;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FabricRaw extends Model
{
    use HasFactory;

    public static function getLotNumber($allocation, $machine, $band_code, $product)
    {

        $machine_effective_lot_code = $machine->machine_type->lot_effective_code;

// رسته نخ
        $current_input_yarns = CurrentMachineInput::where([
            "allocation_id" => $allocation->id,
            "goods_kind_id" => 2
        ])->
        orderBy("material_id")->
        get();


        if (count($current_input_yarns) == 0) {
            return [
                "result" => false,
                "message" => "لات جاری نخ ماشین ثبت نشده است."
            ];
        }

        $current_input_warps = CurrentMachineInput::where([
            "allocation_id" => $allocation->id,
            "goods_kind_id" => 3, // چله
        ])->
        orderBy("material_id")->
        get();

        if (count($current_input_warps) == 0) {
            return [
                "result" => false,
                "message" => "لات جاری چله ماشین ثبت نشده است."
            ];
        }


        // بررسی اینکه لات پارچه خام از قبل وجود داشته یا خیر
        $fabric_raw_lot = LotNumber::where([
            "product_id" => $product->id,
            "machine_lot_effective_code" => $machine_effective_lot_code,
        ]);

        // لات ها را به ترتیب رسته کالایی (چله، نخ) و سپس شناسه مواد اولیه در ستون های lot_number_1 تا lot_number_10 قرار می دهیم اگر مورد مشابه وجود داشت همان را برمی گردانیم در غیر این صورت یک لات جدید ایجاد می کنیم.
        $k = 0;
        $lot_number = 1;
        //بررسی چله
        foreach ($current_input_warps as $warp_item) {
            $fabric_raw_lot = $fabric_raw_lot->where("lot_number_" . ($lot_number) . "_id", $current_input_warps[$k]->lot_number_id);
            $k++;
            $lot_number++;
        }
        //بررسی نخ
        $k = 0;
        foreach ($current_input_yarns as $yarn_item) {
            $fabric_raw_lot = $fabric_raw_lot->where("lot_number_" . ($lot_number) . "_id", $current_input_yarns[$k]->lot_number_id);
            $k++;
            $lot_number++;
        }

        $fabric_raw_lot = $fabric_raw_lot->first();

        // بازگرداندن لات قبلی و پایان
        if (isset($fabric_raw_lot)) {
            return [
                "result" => true,
                "lot_number" => $fabric_raw_lot
            ];
        }

        // ایجاد لات جدید
        $before_lot = LotNumber::where([
            "product_id" => $product->id
        ])->orderByDesc("id")->first();

        // چک کردن لات موثر چله تغییر کرده است یا خیر
        $k = 0;
        $lot_number = 1;
        $warp_lot_is_equal = true;
        foreach ($current_input_warps as $warp_item) {
            $str = "lot_number_" . ($lot_number) . "_id";
            if (isset($before_lot->$str) &&
                $before_lot->$str != $current_input_warps[$k]->lot_number_id) {
                $warp_lot_is_equal = false;
            }
            $k++;
            $lot_number++;
        }
        $warps_lot_code = $before_lot ?
            $before_lot->lot_effective_code1 +
            ($warp_lot_is_equal ?
                0 : 1 // یک شماره اضافه شود در صورت تفاوت با قبلی
            )
            : 1; // کاملا جدید


        // چک کردن لات موثر نخ تغییر کرده است یا خیر
        $yarn_lot_is_equal = true;
        $k = 0;
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
            $before_lot->lot_effective_code2 +
            (
            $yarn_lot_is_equal
                ?
                0 : 1 // یک شماره اضافه شود در صورت تفاوت حداقل یکی از لات های نخ با قبلی
            )
            : 1;

        $new_lot_code = Option::getFormatCode($machine_effective_lot_code, 3) .
            Option::getFormatCode($warps_lot_code, 3) .
            Option::getFormatCode($yarn_lot_code, 3);

        $new_lot_number = new LotNumber();
        $new_lot_number->product_id = $product->id;
        $new_lot_number->machine_lot_effective_code = $machine_effective_lot_code;

// شناسه کالای چله
        $k = 0;
        $lot_number = 1;
        foreach ($current_input_warps as $warp_item) {
            $str = "lot_number_" . ($lot_number) . "_id";
            $new_lot_number->$str = $current_input_warps[$k]->lot_number_id;
            $k++;
            $lot_number++;
        }

        // شناسه کالای نخ
        $k = 0;
        foreach ($current_input_yarns as $yarn_item) {
            $str = "lot_number_" . ($lot_number) . "_id";
            $new_lot_number->$str = $current_input_yarns[$k]->lot_number_id;
            $k++;
            $lot_number++;

        }

        $new_lot_code_result = self::getNewCode($product->id, $new_lot_code, $machine_effective_lot_code, $yarn_lot_code, $warps_lot_code, $yarn_lot_is_equal, $warp_lot_is_equal);
        $new_lot_number->lot_effective_code1 = $new_lot_code_result["warps_lot_code"];
        $new_lot_number->lot_effective_code2 = $new_lot_code_result["yarn_lot_code"];
        $new_lot_number->code = $new_lot_code_result["new_lot_code"];
        $new_lot_number->user_id = Auth::user()->id;
        $new_lot_number->machine_id = $machine->id;


        $new_lot_number->save();

        return [
            "result" => true,
            "lot_number" => $new_lot_number,
            "is_new_lot" => true
        ];

    }

    public static function getNewCode($product_id, $new_lot_code, $machine_effective_lot_code, $yarn_lot_code, $warps_lot_code, $yarn_lot_is_equal, $warp_lot_is_equal)
    {

        // محاسبه مجدد کد لات، ممکن است، یک کالای جایگزنی انتخاب شده باشد، بنابراین باید کد جدید بدهد و نباید از 01 شروع کند.
        $exists_code = LotNumber::where(["product_id" => $product_id, "code" => $new_lot_code])->exists();

        if (!$exists_code) {
            return [
                "product_id" => $product_id,
                "new_lot_code" => $new_lot_code,
                "warps_lot_code" => $warps_lot_code,
                "yarn_lot_code" => $yarn_lot_code,
            ];
        }

        if (!$warp_lot_is_equal) {
            $warps_lot_code++;
        }

        if (!$yarn_lot_is_equal) {
            $yarn_lot_code++;
        }
        $new_lot_code = Option::getFormatCode($machine_effective_lot_code, 2) .
            Option::getFormatCode($warps_lot_code, 3) .
            Option::getFormatCode($yarn_lot_code, 3);

        return self::getNewCode($product_id, $new_lot_code, $machine_effective_lot_code, $yarn_lot_code, $warps_lot_code, $yarn_lot_is_equal, $warp_lot_is_equal);
    }

    public static function ChangeLot($allocation, $production_status_id = false, $production_form_id = false)
    {

        if (!$allocation) {
            return false;
        }
        $added_lot = false;
        if (!$production_status_id) {

            $production_form = ProductionForm::where([
                "machine_id" => $allocation->machine_id
            ])->whereIn(
                "status_id", [
                7002001, // در حال تکمیل)
                7002008, // در حال بافت پارچه پایانی)
                7002011,// در انتظار بارگذاری
            ])->
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
            return false;
        }
        $message = "";

        $machine_log = MachineLog::getLastLogWithContour($allocation->machine);


        foreach ($production_form->items as $item) {
            //return $item->allocation_id . "==" . $allocation->id;
            if ($item->allocation_id == $allocation->id) {
                // ایجاد لات پارچه

                $result = FabricRaw::getLotNumber(
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

    }

    public static function getCurrentLot($allocation, $order_desc = false, $only_current_allocation = false)
    {


        if ($only_current_allocation) {
            // فقط لات تخصیصی که به تابع پاس داده شده است.
            return ProductionFormItemLotNumber::
            join("production_form_item", "production_form_item.id", "production_form_item_id")->
            join("production_forms", "production_forms.id", "production_form_item.production_form_id")->
            where([
                "production_form_item.allocation_id" => $allocation->id ?? -1
            ])->
            whereNotIn("production_forms.status_id", [7002006])-> // بسته بندی شده
            orderBy("band_code")->
            select("production_form_item_lot_number.*")->
            get();
        }

        $production_form = ProductionForm::where([
            "machine_id" => $allocation->machine_id
        ])->whereIn(
            "status_id", [
            7002001, // در حال تکمیل)
            7002008, // در حال بافت پارچه پایانی)
            7002011,// در انتظار بارگذاری
        ])->orderByDesc("id")->first();

        // ارسال آخرین لات های هر باند
        if ($order_desc) {
            return ProductionFormItemLotNumber::
            join("production_form_item", "production_form_item.id", "production_form_item_id")->
            where([
                "production_form_item.production_form_id" => $production_form->id ?? -1
            ])->
            orderBy("band_code")->
            orderByDesc("production_form_item_lot_number.id")->
            select("production_form_item_lot_number.*")->
            get();
        }

        return ProductionFormItemLotNumber::where([
            "production_form_id" => $production_form->id ?? -1
        ])->get();
    }
//
//    public static function getAmountFromMachineLog( $product_id, $start_machine_log, $end_machine_log ) {
//
//        if ( ! isset( $start_machine_log ) ) {
//            return 0;
//        }
//        if ( ! isset( $end_machine_log ) ) {
//            return 0;
//        }
//
//        $sumCounter = $end_machine_log->sumCounter( "calculate" ) - $start_machine_log->sumCounter();
//
//
//        $tarakom_pod = GoodsKindPropertyValue::where( "product_id", $product_id )->whereIn( "goods_kind_property_id", [
//            220381 // تراکم نهایی پود (تئوری)
//        ] )->sum( "value" );
//
//        return $amount = $tarakom_pod > 0 ? ( $sumCounter ) / ( $tarakom_pod * 100 ) : - 1;
//
//
//    }

//    public static function getMachineContourValueFromAmount( $product_id, $amount ) {
//// این تابع مقدار کالا بر اساس واحد اصلی را می گیرد و مقدار مجموع کنتور ماشین را بر می گرداند.
//        $tarakom_pod = GoodsKindPropertyValue::where( "product_id", $product_id )->whereIn( "goods_kind_property_id", [
//            220381 // تراکم نهایی پود (تئوری)
//        ] )->sum( "value" );
//
//        return ( $amount ) * $tarakom_pod * 100;
//    }

    public static function getShrinkagePercent($initial_amount, $second_amount)
    {
        if (!isset($initial_amount) || !isset($second_amount)) {
            return -1000;
        }

        return round(1 - (
                    $second_amount /
                    ($initial_amount != 0 ? $initial_amount : 1)
                ), 3) * 100;
    }

    public static function ProductionTerminated($allocation_item)
    {

        // درصد مجاز اختلاف مقدار کارت تولید با مقدار تولید شده.
        $allow_diff_percent = $allocation_item->production->product->goods_kind->max_diff_of_production_and_allocation_in_the_end_of_production;

//        $sum_allocation_amount = MachineAllocation::
//        where("production_id", $allocation_item->production_id)->
//        whereIn("status_id", [5310010, 5310020, 5310040])->
//        sum("allocation_amount");


        $sum_production_amount = $allocation_item->production->get_production_amount();

        $count_doffs = MachineAllocation::
        where("production_id", $allocation_item->production_id)->
        whereIn("status_id", [5310010, 5310020, 5310040])->
        selectRaw("sum(max_number_of_doffs)- sum(number_of_doffs_done) as count")->
        first();

        if (
            $sum_production_amount >= ($allocation_item->production->number * (1 - $allow_diff_percent / 100))
            &&
            $count_doffs["count"] == 0
        ) {
            $allocation_item->production->waiting_status_id = "7001" . "004";// خاتمه یافته
            $allocation_item->production->status_id = 520; // خاتمه یافته
            $allocation_item->production->save();
            event(new ProductionCardLogEvent($allocation_item->production));
        }
    }
/*گرفتن یک مشخصه از کالای پارچه خام ( جنس نخ های پارچه خام)*/
    public static function GetYarnType(Product $product, $goods_kind_property_id)
    {
        $consumed_list = Product\ConsumedProduct\ConsumedProduct::
        where("product_id", $product->id)->groupBy("material_id")->with("product")->get();
        $yarn_ids = [];
        foreach ($consumed_list as $consumed) {
            if ($consumed->material->goods_kind_id == 2) {
                $yarn_ids[] = $consumed->material_id;
            } elseif ($consumed->material->goods_kind_id == 3) {
                $warps_consumed_list = Product\ConsumedProduct\ConsumedProduct::
                where("product_id", $consumed->material->id)->groupBy("material_id")->with("product")->get();
                foreach ($warps_consumed_list as $warps) {
                    $yarn_ids[] = $warps->material_id;
                }

            }

        }

        $option_ids = GoodsKindPropertyValue::
        whereIn("product_id", $yarn_ids)->
        where("goods_kind_property_id", $goods_kind_property_id)->pluck("value", "value");
        $list = GoodsKindPropertyOption::whereIn("id", $option_ids)->pluck("caption");
        $text = "";
        foreach ($list as $k => $v) {
            $text .= $v;
            if (count($list) > 1) {
                $text .= "<br/>";
            }
        }
        return $text;
    }

    public static function UpdateProductionStatus(Allocation $allocation)
    {
        $production=null;
        foreach ($allocation->items as $item) {
            $production = $item->production;
        }
        if(!$production){
            return [
                "result" => false,
                "error"=>"کارت تولید جهت بررسی وضعیت یافت نشد."
            ];
        }

        // آیا تخصیص جاری دیگری دارد؟
        $other_current_allocation=MachineAllocation::where("production_id", $production->id)->exists();
        if(!$other_current_allocation){
            return [
                "result" => true,
                "status_id"=>7001003 // در حال بافت
            ];
        }
    }
}
