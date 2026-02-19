<?php

namespace App\Http\Controllers\Report\RealTime;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Sales\ProductRequestPermissionController;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\FormLog;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Order\Order;
use App\Models\Order\OrderFactor;
use App\Models\Production\Production;
use App\Models\Report\RealTime\RealTimeOrder;
use App\Models\Utility\Setting;
use App\Models\Warehouse\WarehouseProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $setting_data = Setting::getIntegerValueList(RealTimeOrder::$list_setting);

        session(["back_url" => route("report.real_time.dashboard.index")]);

        $allow_show_bar_weigh_in_real_time_dashboard = $setting_data["allow_show_bar_weigh_in_real_time_dashboard"];
        $allow_show_bar_price_in_real_time_dashboard = $setting_data["allow_show_bar_price_in_real_time_dashboard"];
        $allow_show_polar_in_real_time_dashboard = $setting_data["allow_show_polar_in_real_time_dashboard"];

        $allow_show_line_weight_in_real_time_dashboard = $setting_data["allow_show_line_weight_in_real_time_dashboard"];
        $allow_show_line_price_in_real_time_dashboard = $setting_data["allow_show_line_price_in_real_time_dashboard"];


        $real_time_top_customer_show = $setting_data["real_time_top_customer_show"];
        $real_time_top_customer_days = $setting_data["real_time_top_customer_days"];
        $real_time_top_customer_number = $setting_data["real_time_top_customer_number"];
        $real_time_top_order_delay_show = $setting_data["real_time_top_order_delay_show"];
        $real_time_top_order_delay_number = $setting_data["real_time_top_order_delay_number"];

        $order_report = [];
        $order_report_line = [];
        $waiting_production = 0;
        if ($allow_show_bar_weigh_in_real_time_dashboard || $allow_show_bar_price_in_real_time_dashboard) {
            $order_report = self::GetOrderReport();
            $allowed_status_ids = [
                500,
                7001001, 7001002,
                7201001, 7201002,
                7301001, 7201002, 7301005,
            ];
            $x = Production::whereIn("waiting_status_id", $allowed_status_ids)->where("status_id", 500)->sum("number");
            $x += MachineAllocation::whereIn("status_id", [5310010, 5310040])->sum("allocation_amount");
            $waiting_production = round($x / 1000, 2);
        }
        if ($allow_show_line_weight_in_real_time_dashboard || $allow_show_line_price_in_real_time_dashboard) {
            $order_report_line = self::GetRealTimeOrder();
        }
        $top_customer = [];
        if ($real_time_top_customer_show) {
            $top_customer = self::TopCustomer($setting_data["real_time_top_customer_days"], $setting_data["real_time_top_customer_number"]);
        }
        $waiting_order = [];
        if ($real_time_top_order_delay_show) {
            $waiting_order = self::WaitingOrder($real_time_top_order_delay_number);
        }
        $unit = Product::where("id", ">", 0)->orderBy("id", "desc")->first()->unit;

        $order_remaining = 0; // مقدار سفارش باقی مانده
        if ($allow_show_polar_in_real_time_dashboard) {
            $order_sum = Order::join("order_list", "orders.id", "=", "order_id")->
            whereIn("orders.status_id", ProductRequestPermissionController::$order_status_list)->sum("carton");

            $product_request_form = ProductRequestForm::join("product_request_form_item", "product_request_forms.id", "product_request_form_id")->
            whereIn("product_request_forms.status_id", ProductRequestPermissionController::$product_request_forms_status_list)->sum("amount_sent");

            $status_were_transaction_not_ok = ProductRequestForm::Get_CurrentExistFromForDashboard(30);
            $sum_delivery = Form::join("form_item", "forms.id", "form_id")->
            whereIn("status_id", $status_were_transaction_not_ok)->sum("amount");

            $order_remaining = $order_sum - $product_request_form - $sum_delivery;
            $order_remaining = $order_remaining / 1000;


            $product_ids = Product\TypeOfSaleProduct\TypeOfSaleProductProduct::
            pluck("product_id")->
            toArray();

            $order_sum = Order::join("order_list", "orders.id", "=", "order_id")->
            whereIn("orders.status_id", ProductRequestPermissionController::$order_status_list)->
            selectRaw("sum(carton) as carton,product_id")->
            groupBy("product_id")->
            pluck( "carton","product_id")->toArray();


            $product_ids[] = -1;
            $inventory_all = PackingForm::
            join("packing_form_item", "packing_forms.id", "packing_form_item.packing_form_id")->
            whereIn("product_id", $product_ids)->
            where("packing_forms.status_id", 7007003)->
            selectRaw("sum(final_amount) as final_amount, product_id")->
            groupby("product_id")->
            pluck("final_amount", "product_id")->toArray();


            $sum_s=0;
            foreach ($inventory_all as $product_id=>$inventory) {
                $x=$inventory;
                $y=isset($order_sum[$product_id])?$order_sum[$product_id]:0;

                $z=min($y,$x);

                $sum_s+=$z;
            }



        }

        return view('report.real_time.dashboard.index', [
            "order_report" => $order_report,
            "setting_data" => $setting_data,
            "top_customer" => $top_customer,
            "allow_show_bar_weigh_in_real_time_dashboard" => $allow_show_bar_weigh_in_real_time_dashboard,
            "allow_show_bar_price_in_real_time_dashboard" => $allow_show_bar_price_in_real_time_dashboard,
            "allow_show_line_weight_in_real_time_dashboard" => $allow_show_line_weight_in_real_time_dashboard,
            "allow_show_line_price_in_real_time_dashboard" => $allow_show_line_price_in_real_time_dashboard,
            "real_time_top_customer_show" => $real_time_top_customer_show,
            "real_time_top_customer_number" => $real_time_top_customer_number,
            "real_time_top_customer_days" => $real_time_top_customer_days,
            "real_time_top_order_delay_number" => $real_time_top_order_delay_number,
            "real_time_top_order_delay_show" => $real_time_top_order_delay_show,
            "allow_show_polar_in_real_time_dashboard" => $allow_show_polar_in_real_time_dashboard,
            "waiting_order" => $waiting_order,
            "unit" => $unit,
            "inventory_all" => $inventory_all,
            "order_remaining" => $order_remaining,
            "waiting_production" => $waiting_production,
            "order_report_line" => $order_report_line,
        ]);
    }

    public static function GetOrderReport()
    {
        $order_status_ids = [35030, 35040, 35090, 304040, 304050, 304060, 304070, 304075, 304080];
        // 1- به دست آوردن لیست سفارش ها در x روز گذشته که وضعیت آنها در انتظار آماده سازی و ارسال ناقص، تایید برگ خروج باشد
        $order_ids = Order::
        whereIn("orders.status_id", $order_status_ids)-> // از تایید پیش فاکتور به بعد
        pluck("id")->toArray();
        $order_ids[] = -1;
        //   $order_ids=[627];
        // 2- به دست آوردن مقدار کل سفارش: جمع زدن مقدار ستون کارتون و مقدار قیمت با ارزش افزودن
        $sum_order_all = OrderFactor::
        join("products", "products.id", "order_factor.product_id")->
        whereIn("order_id", $order_ids)->
        selectRaw("sum(carton * weight) as sum_amount , sum(fea * carton) as sum_price")->
        first();

        //3- به دست آوردن مقدار ارسال شده سفارشها
        // جمع زدن مقدار درخواست های کالا از انبار که شامل این سفارش ها باشد، مقدار ارسال شده
        $data = ProductRequestForm::
        join('product_request_form_item', 'product_request_forms.id', '=', 'product_request_form_id')
            ->join('order_factor', function ($join) {
                $join->on("order_factor.order_id", "=", "product_request_forms.order_id");
                $join->on("order_factor.product_id", "=", "product_request_form_item.product_id");
            })
            ->join('products', 'products.id', '=', 'order_factor.product_id')
            ->where('applicant_type_id', 30)
            ->whereIn('product_request_forms.order_id', $order_ids)
            ->groupBy('product_request_form_item.id')

//        products.id as product_id,
//        products.weight,
//        carton,
//        amount_sent,
//        total_price,
//        SUM(products.weight) as total_weight,
            ->selectRaw('
        (SUM(amount_sent) / COUNT(amount_sent)) * products.weight as avg_amount_weight,
        (SUM(amount_sent) / COUNT(amount_sent)) * fea  as total_price_amount
    ')
            ->get()->toArray();
        $totalAvgAmountWeightSent = array_sum(array_column($data, 'avg_amount_weight'));
        $totalPriceAmountSent = array_sum(array_column($data, 'total_price_amount'));


        // 5- جمع کل کارت های تولید صادر شده
        $production_amount = Production:: whereIn("production_cards.order_id", $order_ids)->
        join("products", "production_cards.product_id", "products.id")->
        join('order_factor', function ($join) {
            $join->on("order_factor.order_id", "=", "production_cards.order_id");
            $join->on("order_factor.product_id", "=", "production_cards.product_id");
        })->
        whereNull("parent_production_id")->
        selectRaw("sum(number * weight ) as sum_amount,sum(number * fea) as sum_price_amount ")->
        first();
        //4- به دست آوردن مقدار فرم های تولید سفارش ها

        $production_form_amount = Production::
        join("production_form_item", "production_cards.id", "production_id")->
        join("products", "production_cards.product_id", "products.id")->
        join('order_factor', function ($join) {
            $join->on("order_factor.order_id", "=", "production_cards.order_id");
            $join->on("order_factor.product_id", "=", "production_cards.product_id");
        })->
        whereIn("production_cards.order_id", $order_ids)->
        whereNull("parent_production_id")->
        selectRaw("sum(final_amount * weight ) as sum_amount,sum(price ) as sum_price_amount  ")->
        first();
        // جمع کل کارت های صادر شده - جمع کل تولید شده => جمع کل کارت های صادر شده تولید نشده
        $ton = 1000;
        $miliyard = 1000000000;
        $round_number = 2;
        return [
            "production_form_amount" => round($production_form_amount->sum_amount / $ton, $round_number),
            "production_form_amount_price" => round($production_form_amount->sum_price_amount / $miliyard, $round_number),

            "production_amount" => round($production_amount->sum_amount / $ton, $round_number),
            "production_amount_price" => round($production_amount->sum_price_amount / $miliyard, $round_number),

            "sum_amount_sent" => round($totalAvgAmountWeightSent / $ton, $round_number),
            "sum_amount_sent_price" => round($totalPriceAmountSent / $miliyard, $round_number),

            "sum_order_all" => round($sum_order_all->sum_amount / $ton, $round_number),
            "sum_order_all_price" => round($sum_order_all->sum_price / $miliyard, $round_number),

            "waiting_production" => (round($production_amount->sum_amount / $ton, $round_number) - round($production_form_amount->sum_amount / $ton, $round_number)),
            "waiting_production_price" => (round($production_amount->sum_price_amount / $miliyard, $round_number) - round($production_form_amount->sum_price_amount / $miliyard, $round_number)),
        ];

    }

    public static function WaitingOrder($real_time_top_order_delay_number)
    {
        $sum_order_all = Order::
        whereIn("orders.status_id", [35030, 35040, 35090])->
        orderBy("order_datetime", "asc")->
        with("customer")->
        limit($real_time_top_order_delay_number)->   // n تا
        get();
        $order_ids = [-1];
        foreach ($sum_order_all as $item) {
            $order_ids[] = $item->id;
        }
        $carton = OrderFactor::whereIn("order_id", $order_ids)->selectRaw("sum(carton) as carton , order_id")->
        groupBy("order_id")->
        pluck("carton", "order_id")->toArray();

        $amount_sent = ProductRequestForm::
        join("product_request_form_item", "product_request_forms.id", "product_request_form_id")->
        whereIn("product_request_forms.order_id", $order_ids)->
        groupBy("product_request_forms.order_id")->
        selectRaw("sum(amount_sent) as amount_sent, order_id")->
        pluck("amount_sent", "order_id")->toArray();

        $percent = [];
        foreach ($carton as $order_id => $value) {
            if (isset($amount_sent[$order_id])) {
                $percent[$order_id] = round($amount_sent[$order_id] / $value * 100, 2);
            }

        }

        return [
            "order" => $sum_order_all,
            "amount_of_sent" => $percent,
        ];

    }

    public static function TopCustomer($days, $real_time_top_customer_number)
    {


        $top_customers = RealTimeOrder::
        where("current_date", ">=", Carbon::now()->subDays($days))->
        groupBy("customer_id")->
        selectRaw("customer_id,sum(weight) as sum_amount_buy, sum(total_price) as sum_price_buy")->
        orderBy("sum_amount_buy", "desc")->
        limit($real_time_top_customer_number)->
        get();


        $customer_ids = [];
        $list_buy = [];
        foreach ($top_customers as $top_customer) {
            $customer_ids[] = $top_customer->customer_id;
            $list_buy[$top_customer->customer_id] = [
                "sum_amount_buy" => $top_customer->sum_amount_buy,
                "sum_price_buy" => $top_customer->sum_price_buy,
            ];
        }
        $customer_ids[] = -1;

        $sum_order_all = Order::join("order_factor", "order_factor.order_id", "orders.id")->
        where("order_datetime", ">=", Carbon::now()->subDays($days))->
        whereIn("orders.status_id", [35030, 35040, 35090])->
        whereIn("orders.customer_id", $customer_ids)->
        groupBy("customer_id")->
        selectRaw("orders.customer_id,sum(carton) as sum_amount,orders.id as order_id , sum(total_price_with_tax) as sum_price")->
        with("customer")->
        get()->keyBy("customer_id");

        $list = [];
        foreach ($top_customers as $top_customer) {

            $top_customer->customer = $sum_order_all[$top_customer->customer_id]->customer;
            $top_customer->sum_amount = $sum_order_all[$top_customer->customer_id]->sum_amount;
            $top_customer->sum_price = $sum_order_all[$top_customer->customer_id]->sum_price;
            $top_customer->order_id = $sum_order_all[$top_customer->customer_id]->order_id;
        }


        return $top_customers;
    }

    public static function GetRealTimeOrder($days = 365)
    {
        $data = RealTimeOrder::where("current_date", ">=", Carbon::now()->subDays($days))->
        groupBy("date_number")->
        selectRaw("sum(weight) as weight, sum(total_price) as total_price , date_number")->
        orderBy("date_number", "asc")->
        get();
        $weight_list = [];
        $price_list = [];
        $date_number = [];
        foreach ($data as $value) {

            $weight_list[$value->date_number] = round($value->weight / 1000, 2);
            $price_list[$value->date_number] = round($value->total_price / 1000000000, 2);
            $date_number[$value->date_number] = $value->date_number;
        }

        return [
            "weight_list" => $weight_list,
            "price_list" => $price_list,
            "date_number" => $date_number,
        ];
    }

}
