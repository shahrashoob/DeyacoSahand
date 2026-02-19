<?php

namespace App\Models\Utility\Algorithm\ProductionAllocation;

use App\Http\Controllers\Contractor\Admin\ContractorAllocationController;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Product;
use App\Models\HR\Shift\Shift;
use App\Models\Production\Production;
use App\Models\Utility\Notification\SMSMessage;
use App\Models\Utility\Script\Script;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use Carbon\Carbon;

class Algorithm4ProductionAllocationToContractorByDownstreamTermination extends Controller
{

    /*
     *  این الگوریتم برای تخصیص کارتهای پیمانی است که وقتی کارتهای سطح پایین آنها خاتمه یافته است،
     * آنها را می توانیم به پیمانکار تخصیص دهیم.
     * و فقط باید یک پیمانکار برای کالا تعریف شده باشد.
     *
     */
    public static function getSetting($type)
    {
        switch ($type) {
            case "check_batch_coordination_in_reference_number": // هماهنگی بچ در شماره مرجع چک شود
                return 1;
                break;
            case "reference_number_type": // نوع شماره مرجع
                return "order_id";
                break;
            case "batch_is_determined_based_on_goods_kind_property_id":
                return [
                    "property_id" => 220343, // نوع تکمیل
                    "value_check" => [
                        404, //  : تکمیل
                        500 // شست و شو و تکمیل
                    ]
                ];
                break;
        }
    }

    public static function handel(GoodsKind $goods_kind, $script, $user_id)
    {
        $production_list = Production::join("products", "products.id", "product_id")->
        where("goods_kind_id", $goods_kind->id)->
        where("production_cards.waiting_status_id", 7008001)-> // در انتظار تخصیص پیمانکار
        orderByDesc("production_cards.id")->
        select("production_cards.*")->
        get();
        $log_algorithm  ["algorithm_id"] = 4;
        $log_algorithm["all_production_count"] = count($production_list);
        $log_algorithm["production_list_where_is_ok"] =0;
        $log_algorithm["goods_kind_id"] = 0;
        $log_algorithm["production"] = [];

        $result_setting_type1 = self::GetProductionBySettingType1($production_list);

        $production_line_product_id = $result_setting_type1["production_line_product_id"];
        $production_child = $result_setting_type1["production_child"];
        $production_list_where_is_ok = $result_setting_type1["production_list_where_is_ok"];
        $production_allocation_amount = $result_setting_type1["production_allocation_amount"];


        $log_algorithm["production_list_where_is_ok"] = count($production_list_where_is_ok);
        $log_algorithm["goods_kind_id"] = $goods_kind->id;
        $log_algorithm["production"] = [];

        if(count($production_list_where_is_ok) ==0){
            return [];
        }
        foreach ($production_list_where_is_ok as $production) {

            $line_product_station_id = $production_line_product_id[$production->id];
            $allocation_amount = $production_allocation_amount[$production->id];
            $child_production_ids = $production_child[$production->id];


            $result_allocation = ContractorAllocationController::PostSubmit($production, $line_product_station_id, $allocation_amount, "", $user_id);

//            if (!$result_allocation["result"] && $script) {
//                Script::SendSmd($script, $production->id, "در تخصیص کارت پیمان *** به پیمانکار خطای زیر رخ داده است." . $result_allocation["error"]);
//            }

            if ($result_allocation["result"]) {
                $log_algorithm["production"][$production->serial] = [
                    "result" => true,
                    "allocation_id" => $result_allocation["allocation_id"],
                    "child_production" => $child_production_ids,
                ];
            } else {
                $log_algorithm["production"][$production->serial] = [
                    "result" => false,
                    "error" => $result_allocation["error"],
                    "child_production" => $child_production_ids,
                ];
            }


        }

        return $log_algorithm;

    }

    public static function GetProductionBySettingType1($production_list)
    {

        $production_child = [];
        $production_line_product_id = [];
        $production_allocation_amount = [];
        // بررسی اینکه کدام کارت ها را می توان تخصیص داد
        $production_list_where_self_ok = [];

        // مرحله 1: فقط اطلاعات خود کارت را چک می کنیم.
        foreach ($production_list as $production) {

            $production_list_where_self_ok[$production->id] = false;
            // مقدار قابل تخصیص
            $allocation_amount = $production->number - $production->get_allocation_amount(false, 3);

            if ($allocation_amount <= 0) {
                continue; // کل کارت تخصیص داده شده است و نیاز به بررسی نیست.
            }
            $production_allocation_amount[$production->id] = $allocation_amount;


            $list_line_product_station = $production->product->line_product_station()->
            orderBy("id")->
            whereNotNull("contractor_operation_id")->
            groupBy("product_route_id")->
            get();
            if (count($list_line_product_station) != 1) {
                // فقط برای وقتی الگوریتم اجرا می شود که فقط یک پیمانکار برای کالا تعریف شده باشد.
                continue;
            }

            // لیست کارت های پایین دستی
            $child_production_list = Production::where("parent_production_id", $production->id)->
            select("waiting_status_id", "product_id", "serial")->
            get();

            $child_production_ids = [];
            $all_production_terminate = true;
            foreach ($child_production_list as $child_production) {

                $terminate_status_id = GoodsKind::getStatusIdFromProduct($child_production->product->goods_kind, null, "termination_status_of_production_card");
                if ($child_production->waiting_status_id != $terminate_status_id) {
                    $all_production_terminate = false;
                }
                $child_production_ids[$child_production->serial] = $child_production->waiting_status_id;

            }

            // لیست فرزندان هر کارت را ذخیره می کنیم.
            $production_child[$production->id] = $child_production_ids;


            if (!$all_production_terminate) {

                continue;// همه کارت های تولید پایین دستی خاتمه یافته نشده اند.
            }

            $line_product_station_id = $list_line_product_station[0]->id;
            $production_list_where_self_ok[$production->id] = true;

            $production_line_product_id[$production->id] = $line_product_station_id;


        }

        // مرحله 2: بررسی هماهنگی در  بچ شماره مرجع
        $reference_number_type = self::getSetting("reference_number_type");
        if ($reference_number_type == "order_id") {
            // نوع شماره مرجع: شماره سفارش
//            $order_list_where_ok = []; // به ازای هر سفارش بررسی می کنیم که می توانیم کارت های تولید آن سفارش را تخصیص دهیم یا خیر
//            foreach ($production_list as $production) {
//                if (!isset($order_list_where_ok[$production->order_id])) {
//                    $order_list_where_ok[$production->order_id] = true; // پیش فرض اوکی است.
//                }
//                $order_list_where_ok[$production->order_id] = $order_list_where_ok[$production->order_id] && $production_list_where_self_ok[$production->id];
//            }

            // مرحله سه: بچ پارت از روی کدام مشخصه کالا تعیین می شود.
            // یعنی کالاهایی که نوع تامین آنها تکمیل است و در یک سفارش هستند
            // مرحله سه وابسته به مرحله دو است.
            $order_property_where_ok = []; // به ازای هر سفارش و مقدار فیلد تکمیل بررسی می کنیم که می توان کارت را تخصیص داد یا خبر
            $batch_is_determined_based_on_goods_kind_property_id = self::getSetting("batch_is_determined_based_on_goods_kind_property_id");

            $goods_kind_property_id = $batch_is_determined_based_on_goods_kind_property_id["property_id"];
            $value_check = $batch_is_determined_based_on_goods_kind_property_id["value_check"];
            $product_ids = [];
            foreach ($production_list as $item) {
                $product_ids[] = $item->product_id + 0;
            }
            //   return $product_ids;
            // مقدار مشخصه های تکمیل برای هر کالا
            $property_values = GoodsKindPropertyValue::where("goods_kind_property_id", $goods_kind_property_id)->
            whereIn("product_id", $product_ids)->
            pluck("value", "product_id");

            foreach ($production_list as $production) {
                if (!isset($property_values[$production->product_id])) {

                    SMSMessage::ExceptionError("مشخصات کالا با کد ".$production->product->code."  ناقص می باشد و اسکریپت  1026 (الگوریتم 4) متوقف گردید.");

                    $property_values[$production->product_id];
                    // مقدار مشخصه برای کالا ست نشده است.
                    $value = 0;
                } else {
                    $value = $property_values[$production->product_id];
                }


                if (!isset($order_property_where_ok[$production->order_id][$value])) {
                    $order_property_where_ok[$production->order_id][$value] = true; // پیش فرض اوکی است.
                }
                if (in_array($value,$value_check) ) {
                    $order_property_where_ok[$production->order_id][$value] = $order_property_where_ok[$production->order_id][$value] && $production_list_where_self_ok[$production->id];
                }

            }

            // فعلا
        }

        $production_list_where_is_ok = []; // لیست کارتهای تولید که باید برای آنها تصخیص انجام شود.
        $check_batch_coordination_in_reference_number = self::getSetting("check_batch_coordination_in_reference_number");
        foreach ($production_list as $production) {

            $value = $property_values[$production->product_id];
            if (
                $check_batch_coordination_in_reference_number &&
                $production_list_where_self_ok[$production->id] // خود کارت تولید به تنهایی اوکی است.
//                &&
//                $order_list_where_ok[$production->order_id]   // سفارش کارت تولید هم اوکی است.
                &&
                $order_property_where_ok[$production->order_id][$value] // با توجه به مشخصه کالا امکان تخصیص کارت وجود دارد یا خیر
            ) {
                $production_list_where_is_ok[] = $production;
            }
        }

        return [
            "result" => true,
            "production_line_product_id" => $production_line_product_id,
            "production_child" => $production_child,
            "production_list_where_is_ok" => $production_list_where_is_ok,
            "production_allocation_amount" => $production_allocation_amount
        ];

    }


}
