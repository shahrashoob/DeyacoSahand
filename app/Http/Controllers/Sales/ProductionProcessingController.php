<?php

namespace App\Http\Controllers\Sales;

use App\Events\Order\OrderLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBandPackingType;
use App\Models\LineProduct\Machine\MachineTypeOutputBandPackingType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\Order\Order;
use App\Models\Order\OrderConsumedProduct;
use App\Models\Order\OrderList;
use App\Models\Order\OrderLog;
use App\Models\Production\Production;
use App\Models\Production\ProductionPackingType;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Warehouse\WarehouseProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionProcessingController extends Controller
{
    //
    var $view_path = "sales.production_processing.";
    var $route_path = "sales.production_processing.";
    var $dashboard_route = "sales.dashboard.";

    public function index(OrderList $order_list)
    {

        $result = $this->checkPermission();
        if ($result != "") {
            return $result;
        }
        $will_be_processed_later = 0;// $order_list->order->will_be_processed_later();
        if ($order_list->production_card_id == null && ($order_list->order->status_id != 304080 && !$will_be_processed_later)) {
            return back()->withErrors("هیچ کارت تولیدی (دستور پیمانی) برای این سطر سفارش در سیستم وجود ندارد.");
        }
        $allow_process = \Auth::user()->posts->first()->checkButtonPermission("sales.304080");
        $allow_process = $allow_process && ($order_list->order->status_id == 304080 || $will_be_processed_later);

        $result_process = self::CanProcessOrder($order_list->order);
        if (!$result_process["result"]) {
            return back()->withErrors($result_process["error"]);
        }


        $max_delivery_datetime1_fa = jdate(Carbon::parse($order_list->order->delivery_datetime)->timestamp)->format('Y/m/d');


        // bom سطح 2
        $order_list = OrderList::find($order_list->id);

        $product_option_result = self::GetOptionByProductIds([$order_list->product_id], $order_list);
        $product_option = $product_option_result["product_options"];
        $material_level2_ids = $product_option_result["material_ids"];


        $level2_production_list = Production::where("parent_production_id", $order_list->production_card_id ?? -1)->get();

        $product_inventory = WarehouseProduct::getProductInventoryList([$order_list->product_id])[$order_list->product_id];

        $list = self::getAllSellingAmount2(null, "sale_planing_status_list", $order_list->order_id, $order_list->product_id);
        $amount_of_other_sales = $list["sum_order_and_warehouse_amount"];
        $sum_packing_forms = $list["sum_packing_forms"];
        $sum_production = $list["production_form_item_sum_production_sum"];

        $priority_option = Option::get("priority");


        // کارت تولید سطح 3
        $order_list = OrderList::find($order_list->id);

        $product_option_level3 = [];
        $product_option_level3["items"] = [];
        if (count($level2_production_list) > 0) {
            $product_level2_ids = [];
            foreach ($level2_production_list as $item) {
                $product_level2_ids[$item->product_id] = $item->product_id;
            }

            $product_option_level3 = self::GetOptionByProductIds($product_level2_ids, $order_list)["product_options"];
        }

        session([
            "submit_over_production" => true, // برای اینکه بیشتر از یکبار بنتواند کارت تحصیص دهد.
        ]);


        return view($this->view_path . "index", compact("order_list", "material_level2_ids", "allow_process", "allow_process", "sum_packing_forms", "sum_production", "level2_production_list", "product_option_level3", "priority_option", "max_delivery_datetime1_fa", "product_option", "product_inventory", "amount_of_other_sales"));

    }


    public function submit(Request $request, OrderList $order_list)
    {

        $result = $this->checkPermission(true);
        if ($result != "") {
            return $result;
        }
        // چک کردن اینکه در هر لحظه فقط یک درخواست در حال پردازش دستی باشد.
        $first_order_list = OrderList::join("orders", "orders.id", "order_id")->
        where("orders.status_id", "304080")-> // در انتظار تایید پردازش
        where("order_list.status_id", 301)->
        first();

        if ($first_order_list && $first_order_list->order_id != $order_list->order_id) {
            return back()->withErrors("با توجه به اینکه سفارش " . $first_order_list->order->code() . " در حال پردازش می باشد، امکان پردازش این سفارش وجود ندارد، لطفا پس از پردازش سفارش " . $first_order_list->order->code() . "، نسبت به پردازش این سفارش اقدام نمایید.");
        }

        $product = $order_list->product;

        if ($order_list->production_card_id == null) { // کارت تولید سطح 1

            $result = self::CreateProductionLevel2_3(1, 0, $order_list, $request->amount, $request->number_of_packing_form);
            if (!$result["result"]) {
                return back()->withErrors($result["error"]);
            }


        } else {

            // کارت تولید سطح 2

            $result = self::CreateProductionLevel2_3(2, $request->material_id, $order_list, $request->amount, $request->number_of_packing_form);
            if (!$result["result"]) {
                return back()->withErrors($result["error"]);
            }
            $product = $result["production"]->product;
        }

        if ($product->supply_type_id == 3) {
            return back()->with(["success" => "یک دستور پیمان برای " . $product->caption . " با موفقیت ایجاد گردید."]);
        } else {
            return back()->with(["success" => "یک کارت تولید " . $product->caption . " با موفقیت ایجاد گردید."]);
        }


    }

    public function submit_3(Request $request, OrderList $order_list)
    {
        $result = $this->checkPermission(true);
        if ($result != "") {
            return $result;
        }
        // چک کردن اینکه در هر لحظه فقط یک درخواست در حال پردازش دستی باشد.
        $first_order_list = OrderList::join("orders", "orders.id", "order_id")->
        where("orders.status_id", "304080")-> // در انتظار تایید پردازش
        where("order_list.status_id", 301)->
        first();

        if ($first_order_list && $first_order_list->order_id != $order_list->order_id) {
            return back()->withErrors("با توجه به اینکه سفارش " . $first_order_list->order->code() . " در حال پردازش می باشد، امکان پردازش این سفارش وجود ندارد، لطفا پس از پردازش سفارش " . $first_order_list->order->code() . "، نسبت به پردازش این سفارش اقدام نمایید.");
        }


        if ($order_list->production_card_id == null) { // کارت تولید سطح 1

            return back()->withErrors("با توجه به اینکه کارت سطح 1 برای کالا صادر نشده است، امکان صدور کارت سطح 3 وجود ندارد.");
        } else {

            // کارت تولید سطح 3

            $result = self::CreateProductionLevel2_3(3, $request->material_id_level_3, $order_list, $request->amount_level3, null);
            if (!$result["result"]) {
                return back()->withErrors($result["error"]);
            }
            $product = $result["production"]->product;
        }

        if ($product->supply_type_id == 3) {
            return back()->with(["success" => "یک دستور پیمان برای " . $product->caption . " با موفقیت ایجاد گردید."]);
        } else {
            return back()->with(["success" => "یک کارت تولید " . $product->caption . " با موفقیت ایجاد گردید."]);
        }
    }

    public static function CanProcessOrder($order)
    {
        // چک کردن اینکه در هر لحظه فقط یک درخواست در حال پردازش دستی باشد.
        $first_order_list = OrderList::join("orders", "orders.id", "order_id")->
        where("orders.status_id", "304080")-> // در انتظار تایید پردازش
        where("order_list.status_id", 301)->
        first();

        if ($first_order_list && $first_order_list->order_id != $order->id) {

            return [
                "result" => false,
                "error" => "با توجه به اینکه سفارش " . $first_order_list->order->code() . " در حال پردازش می باشد، امکان پردازش این سفارش وجود ندارد، لطفا پس از پردازش سفارش " . $first_order_list->order->code() . "، نسبت به پردازش این سفارش اقدام نمایید."
            ];

        }

        return ["result" => true];
    }

    /*
     * $maretila= $request->material
     *
     */
    public static function CreateProductionLevel2_3($level, $material_id, OrderList $order_list, $amount, $number_of_packing_form, $user_id = null, $production_event_id = 7008003)
    {

        if ($level == 1) { // اگر کارت سطح یک می باشد.
            $production_result = Production::CreateHandmadeProduction($order_list->order, $order_list,
                $order_list->product, null, $order_list->order->delivery_datetime, $amount,
                1, $order_list->getPackingType("packing_types"),
                null,
                $number_of_packing_form,
                "",
                $production_event_id,
                null,
                $user_id

            );

            if ($production_result["result"]) {

                $production = $production_result["production"];

                $order_list->production_card_id = $production->id;
                $order_list->status_id = 301;
                $order_list->save();
                $order_list->log("",$user_id);

                return [
                    "result" => true,
                    "production" => $production
                ];
            } else {
                return $production_result;
            }
        }

        /**
         * انجام عملیات برای کارت های سطح 2 و 3
         */

        $material = Product::find($material_id);
        if (!$material) {
            return [
                "result" => false,
                "error" => "کالا جهت صدور کارت تولید نامعتبر می باشد، لطفا یکبار دیگر تلاش کنید."
            ];
        }
        $before_production_number_sum = Production::where([
            "product_id" => $material->id,
            "parent_production_id" => $order_list->production->id
        ])->sum("number");


        //بررسی ارتباط بین کارت اصلی و کارت فرعی
        $bom_item_list = BOMItem::where([
            "product_id" => $order_list->product_id,
            "material_id" => $material->id
        ])->
        groupBy("bill_of_material_id")->
        get();

        // بررسی انیکه مقدار ثبت شده برای کارت فرعی از مقدار کارت اصلی بیشتر نباشد.
        $max_amount_in_bom = 0;
        foreach ($bom_item_list as $item) {
            $max_amount_in_bom = max(
                $max_amount_in_bom,
                BOMItem::where([
                    "product_id" => $order_list->product_id,
                    "material_id" => $material->id,
                    "bill_of_material_id" => $item->bill_of_material_id
                ])->
                sum(DB::raw("amount * percent_of_use/100"))
            );
        }
        $max_amount = $max_amount_in_bom * $order_list->production->number;


        if ($material->supply_type_id == 4) {

            // اگر نوع تامین ماده اولیه تحویل امانی است و کارت به صورت کلی ثبت می شود، نوع بسته بندی کارت  به کارت  تامین اضافه می گردد.
            $order_consumed_product = OrderConsumedProduct::where([
                "order_id" => $order_list->order_id,
                "material_id" => $material->id,
            ])->
            whereNotNull("form_general_item_id")->
            first();

            if (!$order_consumed_product) {
                return [
                    "result" => false,
                    "error" => "مشخصات نوع تامین مواد اولیه برای مواد یافت نشد، لطفا مشخصات ارسال مواد اولیه را تکمیل نمایید."
                ];

            }

            // حداکثر مقدار کارت تامین
            $form_general_item = $order_consumed_product->form_general_item;
            if ($form_general_item) {
                $max_amount = $form_general_item->amount;
            }

        }

        if ($amount + $before_production_number_sum > $max_amount) {


            $error_message =
                "برای تولید " .
                $order_list->production->number . " " . $order_list->production->product->unit->caption . " " . $order_list->production->product->caption
                . " حداکثر به " .
                $max_amount . " " . ($material->unit->caption ?? "واحد") . " " . ($material->caption ?? "ماده اولیه")
                . " نیاز است، لطفا تعداد(واحد اصلی)  را اصلاح فرمایید.";
            if ($before_production_number_sum != 0) {
                $error_message = $error_message . "<br/>" . "لازم به ذکر است که قبلا به مقدار $before_production_number_sum " . $order_list->product->unit->caption . " کارت تولید صادر شده است.";
            }
            return [
                "result" => false,
                "error" => $error_message
            ];

        }

        // بسته بندی های مجا
        // لیست بسته بندی های مجاز هر سطح برابر است
        // با لیست بسته بندی های مجاز در ورودی های  گروه ماشین با اولویت 1
        $machine_type_ids = LineProductStation::where([
            "product_id" => $material->id,
            "priority_number" => 1
        ])->
        pluck("machine_type_id", "machine_type_id");
        if (count($machine_type_ids) == 0) {
            return [
                "result" => false,
                "error" => "لیست خط محصول های " . $order_list->product->caption . " تعریف نشده است."
            ];

        }


        $packing_type_ids = Product\ProductPackingType::
        where("product_id", $material->id)->
        pluck("packing_type_id")->
        toArray();

        if (count($packing_type_ids) == 0) {
            return [
                "result" => false,
                "error" => "در مشخصات " . $material->caption . " هیچ نوع بسته بندی برای این کالا مجاز نشده است."
            ];

        }

        if ($material->supply_type_id == 4) { // اگر تحویل امانی است، نوع بسته بندی را قبلا مشخص کرده اند.

            $form_general_item = $order_consumed_product->form_general_item;

            if ($form_general_item) {
                $machine_type_out_band_packing_type_ids = [];
                $machine_type_out_band_packing_type_ids[] = $form_general_item->packing_type_id;
            }
        } else {
            $machine_type_out_band_packing_type_ids = MachineTypeOutputBandPackingType::
            whereIn("machine_type_id", $machine_type_ids)->
            where("goods_kind_id", $material->goods_kind_id)->
            pluck("packing_type_id", "packing_type_id");


            if (count($machine_type_out_band_packing_type_ids) == 0) {
                $text = "";
                foreach (MachineType::whereIn("id", $machine_type_ids)->get() as $item) {
                    $text .= "<br/>" . $item->capiton;
                }

                if ($text != "") {
                    return [
                        "result" => false,
                        "error" => " هیچ نوع بسته بندی مجاز برای تولید  " . $material->caption . " با توجه به ورودی های ماشین ها وجود ندارد." . "<br/>" .
                            "باید حداقل در یکی از ورودی های " . $material->goods_kind->caption . " در گروه های ماشین زیر، بسته بندی مجاز وجود داشته باشد. " . $text
                    ];
                }
            }
        }
        $machine_type_input_band_packing_type = PackingType::whereIn("id", $machine_type_out_band_packing_type_ids)->get();
        // ایجاد یک ردیف سفارش فرعی
        $new_order_list = new OrderList();
        $max_delivery_datetime = $order_list->order->delivery_datetime;

        $new_order_list->product_id = $material->id;
        $new_order_list->order_id = $order_list->order_id;
        $new_order_list->customer_id = $order_list->customer_id;
        $new_order_list->carton = $amount;


        $new_order_list->number_in_carton = $material->number_in_carton;
        $new_order_list->amount = $amount;
        $new_order_list->amount_remaining = $amount;

        $new_order_list->order_type_id = $order_list->order_type_id;
        $new_order_list->order_status_id = $order_list->order_status_id;
        $new_order_list->erp_status_id = $order_list->erp_status_id;
        $new_order_list->status_id = $order_list->status_id;
        $new_order_list->prefactor_number = 0;
        $new_order_list->priority_id = $order_list->priority_id;


        $new_order_list->from_order_id = $order_list->order_id;
        $new_order_list->from_order_list_id = $order_list->from_order_list_id;
        $new_order_list->from_production_card_id = $order_list->production_card_id;

        $new_order_list->save();


        $production_result = Production::CreateHandmadeProduction(
            $new_order_list->order, $new_order_list, $new_order_list->product, $order_list->production->id,
            $new_order_list->order->delivery_datetime, $amount, 1,
            $machine_type_input_band_packing_type, null, $number_of_packing_form,
            "",
            $production_event_id,
            null,
            $user_id
        );


        if (!$production_result["result"]) {
            $new_order_list->delete();
            return $production_result;
        }

        $production = $production_result["production"];

        $new_order_list->production_card_id = $production->id;
        $new_order_list->save();
        $new_order_list->log("",$user_id);


        return [
            "result" => true,
            "production" => $production
        ];
    }

    public static function getAllSellingAmount2($customer_id, $setting_types = null, $invalid_order_id = null, $product_id = null)
    {
        // // براساس وضعیت های مجاز برنامه ریزی در تنظیمات فروش
        $status_list = Setting::getStringValue($setting_types ?? "sale_formal_status_list");
        $status_list = json_decode($status_list, true);

        $warehouse_status = [];

        // برای استفاده جهت درخواست های کالا از انبار
        if (in_array(35090, $status_list)) { // تایید برگ خروج
            $status_list = array_diff($status_list, [35090]);
            $status_list[] = 7005008; // در انتظار تایید برگ خروج (انبار)
        }
        if (in_array(35040, $status_list)) { // ارسال ناقص
            $status_list = array_diff($status_list, [35040]);
            $status_list[] = 7005004; // در انتظار ارسال باقی مانده درخواست
        }


        $list = Order::join("order_factor", "orders.id", "order_id")->
        when($customer_id, function ($query) use ($customer_id) {
            return $query->where("orders.customer_id", $customer_id);
        })->
        when($invalid_order_id, function ($query) use ($invalid_order_id) {
            return $query->where("orders.id", "!=", $invalid_order_id);
        })->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("order_factor.product_id", $product_id);
        })->
        whereIn("orders.status_id", $status_list)->
        selectRaw("sum(total_price_with_tax) as total_price_with_tax,sum(carton* number_in_carton) as amount")->
        first();

        $list_warehouse = Product\ProductRequest\ProductRequestForm::join("product_request_form_item", "product_request_forms.id", "product_request_form_id")->
        where("applicant_type_id", 30)->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("product_id", $product_id);
        })->
        whereIn("product_request_forms.status_id", $status_list)->

        selectRaw("sum(amount_remaining) as amount_remaining")->
        first();


        $amount_list["total_selling_type"] = $list["total_price_with_tax"];
        $amount_list["sum_amount_order"] = $list["amount"];
        $amount_list["sum_amount_remaining"] = $list_warehouse["amount_remaining"];
        $amount_list["sum_order_and_warehouse_amount"] = $list["amount"] + $list_warehouse["amount_remaining"];


        $final_amount = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_id")->
        where("product_id", $product_id)->
        whereIn("packing_forms.status_id",
            [
                7007002, // در انتظار تایید انبار
                7007005, // در انتظار تحویل به انبار
                7007026, // در انتظار کنترل کیفیت
            ]
        )->sum("final_amount");


        $amount_list["sum_packing_forms"] = $final_amount;


        $production_sum = Production::where("status_id", "!=", 520)->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("product_id", $product_id);
        })->sum("number");


        $production_form_item_sum = Production::
        join("production_form_item", "production_cards.id", "production_form_item.production_id")->
        where("production_cards.status_id", "!=", 520)->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("production_form_item.product_id", $product_id);
        })->sum("final_amount");

        $amount_list["production_sum"] = $production_sum;
        $amount_list["production_form_item_sum"] = $production_form_item_sum;
        $amount_list["production_form_item_sum_production_sum"] = $production_sum - $production_form_item_sum < 0 ?
            0 : $production_sum - $production_form_item_sum;

        return $amount_list;
    }


    public static function GetOptionByProductIds($product_ids, OrderList $order_list, $with_material = false)
    {
        $bom_list = BOMItem::join("products", "material_id", "products.id")->
        whereIn("product_id", $product_ids)->
        whereIn("supply_type_id", [1, 3, 4])-> // تولید داخل
        groupBy("material_id")->
        select("bill_of_material_item.*")->
        get();
        $options = null;
        $options[] = ["id" => "0", "text" => "لطفا یک کالا را انتخاب کنید ", "value" => ""];


        //مقداری از کالا که قبلا برای آن کارت تولید صادر شده است.
        $material_ids = [];
        $materials = [];
        $max_amounts = [];
        foreach ($bom_list as $item) {
            $material_ids[] = $item->material_id;

        }
        $material_ids[] = -1;
        $before_production_number_sum = Production::
        whereIn("product_id", $material_ids)->
        where([
            "parent_production_id" => $order_list->production->id ?? 0
        ])->selectRaw("sum(number) as number,product_id")->
        groupBy("product_id")->
        pluck("number", "product_id");;

        foreach ($bom_list as $item) {


            // چک کردن اینکه اگر کارت زدن دیگه باکس آن نمایش داده نشود.
            $max_amount_in_bom = max(
                0,
                BOMItem::where([
                    "product_id" => $item->product_id,
                    "material_id" => $item->material_id,
                    "bill_of_material_id" => $item->bill_of_material_id
                ])->
                sum(DB::raw("amount * percent_of_use/100"))
            );

            $max_amount = $max_amount_in_bom * ($order_list->production->number ?? 0);
            if ($item->material->supply_type_id == 4) {

                // اگر نوع تامین ماده اولیه تحویل امانی است و کارت به صورت کلی ثبت می شود، نوع بسته بندی کارت  به کارت  تامین اضافه می گردد.
                $order_consumed_product = OrderConsumedProduct::where([
                    "order_id" => $order_list->order_id,
                    "material_id" => $item->material->id,
                ])->
                whereNotNull("form_general_item_id")->
                first();

                if (!$order_consumed_product) {
                    continue;
//                    return [
//                        "result" => false,
//                        "error" => "مشخصات نوع تامین مواد اولیه برای مواد یافت نشد، لطفا مشخصات ارسال مواد اولیه را تکمیل نمایید."
//                    ];

                }

                // حداکثر مقدار کارت تامین
                $form_general_item = $order_consumed_product->form_general_item;
                if ($form_general_item) {
                    $max_amount = $form_general_item->amount;
                } else {
                    $max_amount = 0;
                }

            }

            if (!isset($before_production_number_sum[$item->material_id])) {
                $before_production_number_sum[$item->material_id] = 0;
            }
            // یا کارت نزده و اگر کارت زده به مقدار کافی کارت نزدند.
            if ($before_production_number_sum[$item->material_id] < $max_amount) {
                $option = [
                    "value" => $item->material_id,
                    "text" => $item->material->code . " - " . $item->material->caption
                ];

                $options[] = $option;
                $max_amounts[$item->material_id] = $max_amount;
                if ($with_material) {
                    $materials[] = $item->material;
                }
            }

        }

        $product_option = [

            "id" => "product_id",
            "items" => $options,
            "value" => "",
            "text" => "",
        ];

        return [
            "material_ids" => $material_ids,
            "product_options" => $product_option,
            "max_amounts" => $max_amounts,
            "before_production_number_sum" => $before_production_number_sum,
            "materials" => $materials
        ];
    }

    public function submit_over_production(OrderList $order_list, Request $request)
    {


        $result = $this->checkPermission(true);
        if ($result != "") {
            return $result;
        }
        // بررسی اینکه یکبار درخواست بیشتر ارسال نشود.
        $submit_over_production = session("submit_over_production");
        if (!$submit_over_production) {
            return back()->withErrors("ثبت درخواست تکراری، لطفا بررسی بفرمایید در صورتی که کارت مورد نظر صادر نگردید بود، یکبار دیگر فرم را تکمیل نمایید.");
        }

        $product = $order_list->product;
        $parent_production_id = null;
        $amount = $request->over_amount;
        $production_type_id = 1; // کارت تولیدی
        $priority_id = $request->over_priority_id;
        $number_of_packing_form = $request->number_of_packing_form;


        if ($amount < 0) {
            return back()->withErrors("لطفا مقدار کارت تولید را به درستی وارد نمایید.");
        }

        if ($number_of_packing_form < 0) {
            return back()->withErrors("لطفا تعداد بسته بندی را به درستی وارد نمایید.");
        }

        if (!$order_list->product->default_packing_type) {
            return back()->withErrors("نوع بسته بندی پیش فرض تولید در تب انبارش کالا  مشخص نشده است، لطفا با واحد اطلاعات پایه تماس بگیرید.");
        }

        $packing_types = [];
        $packing_types[] = $order_list->product->default_packing_type;


        $production_result = Production::CreateHandmadeProduction(
            $order ?? null, $new_order_list ?? null, $product,
            $parent_production_id,
            $request->max_delivery_datetime, $amount,
            $production_type_id,
            $packing_types,
            $priority_id,
            $number_of_packing_form,
            "مازاد تولید  سفارش " . $order_list->order->code(),
            7008008
        );

        if (!$production_result["result"]) {

            return redirect()->route('utility.planing.production_order_demo')->withErrors($production_result["error"]);
        }


        $production = $production_result["production"];

        event(new OrderLogEvent($order_list->order, 35099, $production->serial()));
        session()->forget('submit_over_production');
        return back()->with(["success" => "کارت تولید " . $production->serial() . " با موفقیت صادر گردید."]);

    }

    public function checkPermission($allow_production_check = false)
    {
        if ($allow_production_check && !\Auth::user()->posts->first()->checkButtonPermission("sales.304080")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }
        if (!\Auth::user()->posts->first()->checkButtonPermission("sales.show_production_processing")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید");
        }

    }
}
