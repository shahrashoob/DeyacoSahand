<?php

namespace App\Http\Controllers\Report;

use App\Exports\Report\Report1011_1Export;
use App\Exports\Report\Report1013_1Export;
use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Form\FormItemCurrency;
use App\Models\Form\FormLog;
use App\Models\LineProduct\GoodsKind\GoodsKindClassification;
use App\Models\LineProduct\GoodsKind\GoodsKindClassificationOption;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Product;
use App\Models\Order\OrderFactor;
use App\Models\Order\OrderList;
use App\Models\Order\OrderLog;
use App\Models\Production\Production;
use App\Models\Report\Report1010\Report1010MachineLog;
use App\Models\Utility\Option;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class Report1013Controller extends Controller
{
    // گزارش سفارشات
    var $view_path = "report.1013.";
    var $route_path = "report.1013.";

//    public static $route_path_static = "report.1013.";

    public function index(Request $request)
    {

        $cross_sectional_management_request = session("cross_sectional_management_request");
        if (!$cross_sectional_management_request) {
            return redirect()->route("report.cross_sectional_management.dashboard.index")->withErrors("لطفا تاریخ شروع و پایان گزارش را مشخص نمایید.");
        }
        $goods_kind_classification_id = $cross_sectional_management_request["classification_id"];

        $start_date_time = Carbon::parse($cross_sectional_management_request["start_date"] . " ");

        $end_date_time = Carbon::parse($cross_sectional_management_request["end_date"] . " ");

        $goods_kind_id = $cross_sectional_management_request["goods_kind_id"];

        $start_jdate = jdate(Carbon::parse($start_date_time)->timestamp)->format('از ساعت  H:i  %A Y/m/d ');
        $end_jdate = jdate(Carbon::parse($end_date_time)->timestamp)->format('تا ساعت  H:i  %A Y/m/d ');


        $status_list = self::getOrderStatusList();
        $exist_status_list = self::getExistStatusList();
        $chart_percent = self::getChartPercent();

        $bar_list["customer_price"] =
            self::getGroupByCustomer($start_date_time, $end_date_time, $goods_kind_id, $status_list, $exist_status_list, "price", $chart_percent["customer"]["price"], $goods_kind_classification_id);

        $bar_list["customer_amount"] =
            self::getGroupByCustomer($start_date_time, $end_date_time, $goods_kind_id, $status_list, $exist_status_list, "amount", $chart_percent["customer"]["amount"], $goods_kind_classification_id);

        $bar_list["product_price"] =
            self::getGroupByProduct($start_date_time, $end_date_time, $goods_kind_id, $status_list, $exist_status_list, "price", $chart_percent["product"]["price"], $goods_kind_classification_id);

        $bar_list["product_amount"] =
            self::getGroupByProduct($start_date_time, $end_date_time, $goods_kind_id, $status_list, $exist_status_list, "amount", $chart_percent["product"]["amount"], $goods_kind_classification_id);

        $bar_list["classification_price"] =
            self::getGroupByClassification($start_date_time, $end_date_time, $goods_kind_id, $status_list, $exist_status_list, "price", $chart_percent["classification"]["price"], $goods_kind_classification_id);

        $bar_list["classification_amount"] =
            self::getGroupByClassification($start_date_time, $end_date_time, $goods_kind_id, $status_list, $exist_status_list, "amount", $chart_percent["classification"]["amount"], $goods_kind_classification_id);


        return view($this->view_path . "show_report", compact("start_jdate", "end_jdate", "bar_list", "chart_percent"));

    }

    public function download_excel()
    {
        $customer_option = Option::get("customer");
        $status_list = [
            304020,
            304030,
            304040,
            304050,
            304060,
            304070,
            304075,
            304080,
            500000515,
            500000520,
            500000525,
            500000500
        ];
        $report_type_status_option = [
            ["value"=>"","caption"=>"لطفا یک مورد را انتخاب کنید."]
        ];
        foreach (Status::whereIn("id", $status_list)->get() as $item) {
            $report_type_status_option[] = ["value" => $item->id, "caption" => $item->caption];
        }
        return view($this->view_path . "download_excel", compact("customer_option","report_type_status_option"));
    }

    public function submit_download_excel(Request $request)
    {

        $price_or_product = $request->price_or_product;
        $status_id = $request->report_type_status_id;
        $customer_id = $request->customer_id;
        $cross_sectional_management_request = session("cross_sectional_management_request");
        if (!$cross_sectional_management_request) {
            return redirect()->route("report.cross_sectional_management.dashboard.index")->withErrors("لطفا تاریخ شروع و پایان گزارش را مشخص نمایید.");
        }
        $goods_kind_classification_id = $cross_sectional_management_request["classification_id"];

        $start_date_time = Carbon::parse($cross_sectional_management_request["start_date"] . " ");

        $end_date_time = Carbon::parse($cross_sectional_management_request["end_date"] . " ");

        $goods_kind_id = $cross_sectional_management_request["goods_kind_id"];

        $status_list = self::getOrderStatusList();
        $exist_status_list = self::getExistStatusList();
        $chart_percent = self::getChartPercent();

        return
            self::getReportGroupByCustomer($start_date_time, $end_date_time, $goods_kind_id, $status_list, $exist_status_list, $price_or_product, $chart_percent["customer"]["price"], $status_id, $customer_id);


    }

    public function set_chart_percent($percent, $type)
    {
        switch ($type) {
            case 1:
                session(["report_1013_customer_price" => $percent]);
                break;
            case 2:
                session(["report_1013_customer_amount" => $percent]);
                break;
            case 3:
                session(["report_1013_product_price" => $percent]);
                break;
            case 4:
                session(["report_1013_product_amount" => $percent]);
                break;
            case 5:
                session(["report_1013_classification_price" => $percent]);
                break;
            case 6:
                session(["report_1013_classification_amount" => $percent]);
                break;
        }

        return redirect()->route($this->route_path . "index");
    }

    public static function getReportForCrossSection($start_date_time, $end_date_time, $goods_kind_id, $machine_id = false)
    {

        $cross_sectional_management_request = session("cross_sectional_management_request");
        if (!$cross_sectional_management_request) {
            1 / 0;
        }
        $goods_kind_classification_id = $cross_sectional_management_request["classification_id"];
        $status_list = self::getOrderStatusList();

        $exist_form_status = self::getExistStatusList();


        $list_bar_stack_report =
            self::getGroupByCustomer($start_date_time, $end_date_time, $goods_kind_id, $status_list, $exist_form_status, "price", .1, $goods_kind_classification_id);

        return $list_bar_stack_report;
    }

    public static function getGroupByCustomer($start_date_time, $end_date_time, $goods_kind_id, $status_list_for_order, $exist_form_status, $type = "price", $percent_other = .1)
    {

        $customer_list = Customer::get()->keyBy("id");
        $report_result = [];
        $list_bar_stack_report["xAxis"]["data"] = [];

        $order_status = [];

        // وضعیت های سفارش
        foreach ($status_list_for_order as $status) {


            /// گرفتن وضعیت های سفارش
            $order_ids = self:: getOrderFromStatus($status, $start_date_time, $end_date_time);


            $order_status[$status->id]["data"] = $order_ids;
            $order_status[$status->id]["caption"] = $status->caption;
            $report_result["other"][$status->id] = 0; // مقدار سایر

            $order_factor = OrderFactor::
            join("products", "product_id", "products.id")->
            join("customers", "customer_id", "customers.id")->
            where("goods_kind_id", $goods_kind_id)->
            whereIn("order_id", $order_ids)->
            //where("customer_id",29)->
            when($type == "price", function ($query) {
                return $query->
                selectRaw("customer_id,customers.caption as caption, round(sum(total_price )) as value");
            })->

            when($type == "amount", function ($query) {
                return $query->
                selectRaw("customer_id,customers.caption as caption, round(sum(carton )) as value");
            })->

            groupBy("customer_id")->
            pluck("value", "customer_id")->
            toArray();

            $list_bar_stack_report["xAxis"]["data"][] = "در انتظار " . $status->caption . " (" . array_sum($order_factor) . ")";

            $report_result["other"][$status->id] = 0;
            $result =
                self::ReportResultArrayFromOrderFactor($report_result, $order_factor, $status, $customer_list, $percent_other);
            $report_result = $result["report_result"];
            $customer_list = $result["object_list"];


        }


        // کارت تولید


        $production_card_list = self::getProductionListValue(
            $goods_kind_id,
            $start_date_time,
            $end_date_time,
            0,
            $type,
            "customer"
        );

        $list_bar_stack_report["xAxis"]["data"][] = "کارت تولید صادر شده" . " (" . number_format(array_sum($production_card_list)) . ")";

        $report_result["other"]["production"] = 0;
        $result = self::ReportResultArrayFromProduction($report_result, $production_card_list, $customer_list, $percent_other);
        $report_result = $result["report_result"];
        $customer_list = $result["object_list"];


        // وضعیت های برگ خروخ از انبار
        foreach ($exist_form_status as $status) {


            $form_ids = self::getExistFormStatus(
                $status,
                $start_date_time,
                $end_date_time
            );

            $exit_form_list_values = self::getExistFromListValue(
                $goods_kind_id,
                0,
                $type,
                "customer",
                $form_ids);

            $list_bar_stack_report["xAxis"]["data"][] = "در انتظار " . $status->caption . " (" . number_format(array_sum($exit_form_list_values)) . ")";

            $report_result["other"][$status->id] = 0;
            $result = self::ReportResultArrayFromExistFrom($report_result, $exit_form_list_values, $status, $customer_list, $percent_other);
            $report_result = $result["report_result"];
            $customer_list = $result["object_list"];
        }


        foreach ($customer_list as $customer) {
            if (!isset($customer->show_result)) {
                unset($report_result[$customer->id]);
            }

        }

        $list_bar_stack_report["series"] = [];
        foreach ($report_result as $customer_id => $report) {

            $series = [
                "name" => $customer_id == "other" ? "سایر مشتریان" : $customer_list[$customer_id]->caption,
                "type" => "bar",
                "stack" => "R1013",
                "emphasis" => ["focus" => "series"]
            ];
            $series["data"] = $report;
            $list_bar_stack_report["series"] [] = $series;
        }


        return $list_bar_stack_report;
    }

    public static function getReportGroupByCustomer(
        $start_date_time, $end_date_time, $goods_kind_id, $status_list_for_order, $exist_form_status,
        $type = "price", $percent_other = .1, $status_id = 0, $customer_id = -1)
    {
        set_time_limit(300);
        $form_item_currency = [];
        $order_factor = [];
        $status = Status::find($status_id);
        switch ($status_id) {
            case 304020: // در انتظار تایید پیش نویس (واحد مالی)
            case 304030:
            case 304040:
            case 304050:
            case 304060:
            case 304070:
            case 304075:
            case 304080:
                /// گرفتن وضعیت های سفارش

                $order_ids = self:: getOrderFromStatus($status, $start_date_time, $end_date_time);
                $order_factor = OrderFactor::
                with(["order", "order.customer", "product"])->
                join("products", "product_id", "products.id")->
                join("customers", "customer_id", "customers.id")->
                where("goods_kind_id", $goods_kind_id)->
                whereIn("order_id", $order_ids)->
                where("customer_id", $customer_id)->
                when($type == "price", function ($query) {
                    return $query->
                    selectRaw("product_id,order_id,customer_id,customers.caption as caption, total_price as value");
                })->

                when($type == "amount", function ($query) {
                    return $query->
                    selectRaw("product_id,order_id,customer_id,customers.caption as caption, carton as value");
                })->
                get();

            case 500000515: //پیش نویس واحد مالی
            case 500000520:
            case 500000525:
            case 500000500:
                $form_ids = self::getExistFormStatus(
                    $status,
                    $start_date_time,
                    $end_date_time
                );

                $form_item_currency = FormItemCurrency::
                with("product", "form")->
                join("products", "form_item_currency.product_id", "products.id")->
                where("goods_kind_id", $goods_kind_id)->
                join("product_request_form_form", "product_request_form_form.form_id", "form_item_currency.form_id")->
                join("product_request_forms", "product_request_form_id", "product_request_forms.id")->
                join("orders", "product_request_forms.order_id", "orders.id")->
                join("customers", "customers.id", "customer_id")->
                where("product_request_forms.applicant_type_id", 30)->

                // نوع خروجی نمودار
                when($type == "price", function ($query) {
                    return $query->
                    selectRaw("product_id,orders.code as order_code,customers.caption as customer_caption,customers.code as customer_code,customer_id,product_request_form_form.form_id,total_price as value");
                })->

                when($type == "amount", function ($query) {
                    return $query->
                    selectRaw("product_id,orders.code as order_code,customers.caption as customer_caption,customers.code as customer_code,customer_id,product_request_form_form.form_id, amount  as value");
                })->
                where("customer_id", $customer_id)->
                whereIn("form_item_currency.form_id", $form_ids)->
                get();

                break;
        }


        $export = new Report1013_1Export();
        $export->order_factor = $order_factor;
        $export->form_item_currency = $form_item_currency;

        return Excel::download($export, 'report_1013' . "_" . jdate(Carbon::now()->timestamp)->format('Y_m_d') . "_" . $status_id . '.xlsx');

        // وضعیت های سفارش
        foreach ($status_list_for_order as $status) {


            $order_status[$status->id]["data"] = $order_ids;
            $order_status[$status->id]["caption"] = $status->caption;
            $report_result["other"][$status->id] = 0; // مقدار سایر


            $list_bar_stack_report["xAxis"]["data"][] = "در انتظار " . $status->caption . " (" . array_sum($order_factor) . ")";

            $report_result["other"][$status->id] = 0;
            $result =
                self::ReportResultArrayFromOrderFactor($report_result, $order_factor, $status, $customer_list, $percent_other);
            $report_result = $result["report_result"];
            $customer_list = $result["object_list"];


        }


        // کارت تولید


        $production_card_list = self::getProductionListValue(
            $goods_kind_id,
            $start_date_time,
            $end_date_time,
            0,
            $type,
            "customer"
        );

        $list_bar_stack_report["xAxis"]["data"][] = "کارت تولید صادر شده" . " (" . number_format(array_sum($production_card_list)) . ")";

        $report_result["other"]["production"] = 0;
        $result = self::ReportResultArrayFromProduction($report_result, $production_card_list, $customer_list, $percent_other);
        $report_result = $result["report_result"];
        $customer_list = $result["object_list"];


        // وضعیت های برگ خروخ از انبار
        foreach ($exist_form_status as $status) {


            $form_ids = self::getExistFormStatus(
                $status,
                $start_date_time,
                $end_date_time
            );


            $list_bar_stack_report["xAxis"]["data"][] = "در انتظار " . $status->caption . " (" . number_format(array_sum($exit_form_list_values)) . ")";

            $report_result["other"][$status->id] = 0;
            $result = self::ReportResultArrayFromExistFrom($report_result, $exit_form_list_values, $status, $customer_list, $percent_other);
            $report_result = $result["report_result"];
            $customer_list = $result["object_list"];
        }


        foreach ($customer_list as $customer) {
            if (!isset($customer->show_result)) {
                unset($report_result[$customer->id]);
            }

        }

        $list_bar_stack_report["series"] = [];
        foreach ($report_result as $customer_id => $report) {

            $series = [
                "name" => $customer_id == "other" ? "سایر مشتریان" : $customer_list[$customer_id]->caption,
                "type" => "bar",
                "stack" => "R1013",
                "emphasis" => ["focus" => "series"]
            ];
            $series["data"] = $report;
            $list_bar_stack_report["series"] [] = $series;
        }


        return $list_bar_stack_report;
    }

    public static function getGroupByProduct($start_date_time, $end_date_time, $goods_kind_id, $status_list_for_order, $exist_form_status, $type = "price", $percent_other = .1)
    {

        $product_list = Product::where("goods_kind_id", $goods_kind_id)->get()->keyBy("id");
        $report_result = [];
        $list_bar_stack_report["xAxis"]["data"] = [];

        $order_status = [];

        // وضعیت های سفارش
        foreach ($status_list_for_order as $status) {


            /// گرفتن وضعیت های سفارش
            $order_ids = self:: getOrderFromStatus($status, $start_date_time, $end_date_time);

            $order_status[$status->id]["data"] = $order_ids;
            $order_status[$status->id]["caption"] = $status->caption;
            $order_factor = OrderFactor::
            join("products", "product_id", "products.id")->
            where("goods_kind_id", $goods_kind_id)->
            whereIn("order_id", $order_ids)->
            groupBy("product_id")->
            when($type == "price", function ($query) {
                return $query->
                selectRaw("product_id,products.caption as caption, round(sum(total_price)) as value");
            })->

            when($type == "amount", function ($query) {
                return $query->
                selectRaw("product_id,products.caption as caption, round(sum(carton )) as value");
            })->
            pluck("value", "product_id")->
            toArray();


            $list_bar_stack_report["xAxis"]["data"][] = "در انتظار " . $status->caption . "(" . number_format(array_sum($order_factor)) . ")";

            $report_result["other"][$status->id] = 0;
            $result =
                self::ReportResultArrayFromOrderFactor($report_result, $order_factor, $status, $product_list, $percent_other);
            $report_result = $result["report_result"];
            $product_list = $result["object_list"];

        }


        // کارت تولید

        $production_card_list = self::getProductionListValue(
            $goods_kind_id,
            $start_date_time,
            $end_date_time,
            0,
            $type,
            "product"
        );
        $report_result["other"]["production"] = 0;

        $list_bar_stack_report["xAxis"]["data"][] = "کارت تولید صادر شده" . "(" . number_format(array_sum($production_card_list)) . ")";

        $result = self::ReportResultArrayFromProduction($report_result, $production_card_list, $product_list, $percent_other);
        $report_result = $result["report_result"];
        $product_list = $result["object_list"];


        // وضعیت های برگ خروخ از انبار
        foreach ($exist_form_status as $status) {


            $form_ids = self::getExistFormStatus(
                $status,
                $start_date_time,
                $end_date_time
            );

            $exit_form_list_values = self::getExistFromListValue(
                $goods_kind_id,
                0,
                $type,
                "product",
                $form_ids);

            $list_bar_stack_report["xAxis"]["data"][] = "در انتظار " . $status->caption . "(" . number_format(array_sum($exit_form_list_values)) . ")";
            $report_result["other"][$status->id] = 0;
            $result = self::ReportResultArrayFromExistFrom($report_result, $exit_form_list_values, $status, $product_list, $percent_other);
            $report_result = $result["report_result"];
            $product_list = $result["object_list"];
        }

        foreach ($product_list as $product) {
            if (!isset($product->show_result)) {
                unset($report_result[$product->id]);
            }

        }

        $list_bar_stack_report["series"] = [];
        foreach ($report_result as $product_id => $report) {

            $series = [
                "name" => $product_id == "other" ? "سایر کالا ها" : $product_list[$product_id]->caption,
                "type" => "bar",
                "stack" => "R1013",
                "emphasis" => ["focus" => "series"]
            ];
            $series["data"] = $report;
            $list_bar_stack_report["series"] [] = $series;
        }

        return $list_bar_stack_report;
    }

    public static function getGroupByClassification($start_date_time, $end_date_time, $goods_kind_id, $status_list_for_order, $exist_form_status, $type = "price", $percent_other = .1, $goods_kind_classification_id = 0)
    {

        $goods_kind_classification_option_list = GoodsKindClassificationOption::
        where("goods_kind_classification_id", $goods_kind_classification_id)->
        get()->
        keyBy("id");

        $report_result = [];
        $list_bar_stack_report["xAxis"]["data"] = [];

        $order_status = [];

        // وضعیت های سفارش
        foreach ($status_list_for_order as $status) {


            /// گرفتن وضعیت های سفارش
            $order_ids = self:: getOrderFromStatus($status, $start_date_time, $end_date_time);


            $order_status[$status->id]["data"] = $order_ids;
            $order_status[$status->id]["caption"] = $status->caption;
            $order_factor = OrderFactor::
            join("products", "product_id", "products.id")->
            join("goods_kind_classification_product", "goods_kind_classification_product.product_id", "products.id")->
            where("goods_kind_id", $goods_kind_id)->
            where("goods_kind_classification_id", $goods_kind_classification_id)->
            whereIn("order_id", $order_ids)->
            groupBy("goods_kind_classification_option_id")->
            when($type == "price", function ($query) {
                return $query->
                selectRaw("goods_kind_classification_option_id, round(sum(total_price )) as value");
            })->

            when($type == "amount", function ($query) {
                return $query->
                selectRaw("goods_kind_classification_option_id, round(sum(carton )) as value");
            })->
            pluck("value", "goods_kind_classification_option_id")->
            toArray();

            $list_bar_stack_report["xAxis"]["data"][] = "در انتظار " . $status->caption . " (" . number_format(array_sum($order_factor)) . ")";

            $report_result["other"][$status->id] = 0;
            $result =
                self::ReportResultArrayFromOrderFactor($report_result, $order_factor, $status, $goods_kind_classification_option_list, $percent_other);
            $report_result = $result["report_result"];
            $goods_kind_classification_option_list = $result["object_list"];

        }


        // کارت تولید

        $production_card_list = self::getProductionListValue(
            $goods_kind_id,
            $start_date_time,
            $end_date_time,
            $goods_kind_classification_id,
            $type,
            "goods_kind_classification_option"
        );

        $report_result["other"]["production"] = 0;

        $list_bar_stack_report["xAxis"]["data"][] = "کارت تولید صادر شده" . "(" . number_format(array_sum($production_card_list)) . ")";
        $result = self::ReportResultArrayFromProduction($report_result, $production_card_list, $goods_kind_classification_option_list, $percent_other);
        $report_result = $result["report_result"];
        $goods_kind_classification_option_list = $result["object_list"];

        // وضعیت های برگ خروخ از انبار
        foreach ($exist_form_status as $status) {


            $form_ids = self::getExistFormStatus(
                $status,
                $start_date_time,
                $end_date_time
            );

            $exit_form_list_values = self::getExistFromListValue(
                $goods_kind_id,
                $goods_kind_classification_id,
                $type,
                "goods_kind_classification_option",
                $form_ids);

            $list_bar_stack_report["xAxis"]["data"][] = "در انتظار " . $status->caption . "(" . number_format(array_sum($exit_form_list_values)) . ")";
            $report_result["other"][$status->id] = 0;
            $result = self::ReportResultArrayFromExistFrom($report_result, $exit_form_list_values, $status, $goods_kind_classification_option_list, $percent_other);
            $report_result = $result["report_result"];
            $goods_kind_classification_option_list = $result["object_list"];
        }


        foreach ($goods_kind_classification_option_list as $goods_kind_classification_option) {
            if (!isset($goods_kind_classification_option->show_result)) {
                unset($report_result[$goods_kind_classification_option->id]);
            }

        }

        $list_bar_stack_report["series"] = [];
        foreach ($report_result as $goods_kind_classification_option_id => $report) {

            $series = [
                "name" => $goods_kind_classification_option_id == "other" ? "سایر دسته ها" :
                    $goods_kind_classification_option_list[$goods_kind_classification_option_id]->caption,
                "type" => "bar",
                "stack" => "R1013",
                "emphasis" => ["focus" => "series"]
            ];
            $series["data"] = $report;
            $list_bar_stack_report["series"] [] = $series;
        }

        return $list_bar_stack_report;
    }

    public static function getOrderStatusList()
    {
        return Status::
        where("status_type_id", 351)->
        where("id", "!=", 304010)-> // تایید پیش نویس سفارش
        get()->
        keyBy("id");
    }

    public static function getExistStatusList()
    {
        $list = Status::whereIn("id", [500000515, 500000520, 500000525, 500000530, 500000500])->
        get()->keyBy("id");

        $order_list = [];
        $order_list[] = $list[500000515];
        $order_list[] = $list[500000520];
        $order_list[] = $list[500000525];
        $order_list[] = $list[500000530];
        $order_list[] = $list[500000500];

        return $order_list;
    }

    public static function getOrderFromStatus($status, $start_date_time, $end_date_time)
    {
        $order_ids = OrderLog::
        where("status_id", $status->id)->
        // به ازای هر سفارش، اولین لاگ را در نظر می گیریم.
        selectRaw("created_at, id,order_id,RANK() OVER(PARTITION BY order_id
                            ORDER BY id Asc) AS status_rank")->
        groupBy("order_id")->
        having("created_at", ">=", $start_date_time)->
        having("created_at", "<=", $end_date_time)->
        pluck("order_id")->toArray();

        return $order_ids;
    }

    public static function getExistFormStatus(
        $status, $start_date_time, $end_date_time
    )
    {

        $form_ids = FormLog::
        where("status_id", $status->id)->
        // به ازای هر فرم، اولین لاگ را در نظر می گیریم.
        selectRaw("created_at, id,form_id,RANK() OVER(PARTITION BY form_id
                            ORDER BY id Asc) AS status_rank")->
        groupBy("form_id")->
        having("created_at", ">=", $start_date_time)->
        having("created_at", "<=", $end_date_time)->
        pluck("form_id")->toArray();

        return $form_ids;

    }


    public static function getChartPercent()
    {

        $chart_percent["customer"]["price"] = session("report_1013_customer_price");
        if (!isset($chart_percent["customer"]["price"])) {
            $chart_percent["customer"]["price"] = .01;
        }

        $chart_percent["customer"]["amount"] = session("report_1013_customer_amount");
        if (!isset($chart_percent["customer"]["amount"])) {
            $chart_percent["customer"]["amount"] = .01;
        }
        $chart_percent["product"]["price"] = session("report_1013_product_price");
        if (!isset($chart_percent["product"]["price"])) {
            $chart_percent["product"]["price"] = .04;
        }
        $chart_percent["product"]["amount"] = session("report_1013_product_amount");
        if (!isset($chart_percent["product"]["amount"])) {
            $chart_percent["product"]["amount"] = .04;
        }
        $chart_percent["classification"]["price"] = session("report_1013_classification_price");
        if (!isset($chart_percent["classification"]["price"])) {
            $chart_percent["classification"]["price"] = .01;
        }
        $chart_percent["classification"]["amount"] = session("report_1013_classification_amount");
        if (!isset($chart_percent["classification"]["amount"])) {
            $chart_percent["classification"]["amount"] = .01;
        }

        return $chart_percent;
    }

    public static function getProductionListValue(
        $goods_kind_id,
        $start_date_time,
        $end_date_time,
        $goods_kind_classification_id,
        $type_value,
        $type_report_col_id
    )
    {

        return Production::
        join("order_factor", "order_factor.order_list_id", "production_cards.order_list_id")->
        join("products", "order_factor.product_id", "products.id")->
        where("production_cards.created_at", ">=", $start_date_time)->
        where("production_cards.created_at", "<=", $end_date_time)->
        where("goods_kind_id", $goods_kind_id)->

        // گزارش به تفکیک: مشتری، کالا، طبقه بندی
        when($type_report_col_id == "customer", function ($query) {
            return $query->groupBy("production_cards.customer_id")->
            selectRaw("production_cards.customer_id as col_id");
        })->

        when($type_report_col_id == "product", function ($query) {
            return $query->groupBy("products.id")->
            selectRaw("products.id as col_id");
        })->

        when($type_report_col_id == "goods_kind_classification_option", function ($query) use ($goods_kind_classification_id) {
            return $query->
            join("goods_kind_classification_product", "goods_kind_classification_product.product_id", "products.id")->
            where("goods_kind_classification_id", $goods_kind_classification_id)->
            groupBy("goods_kind_classification_option_id")->
            selectRaw(" goods_kind_classification_option_id as col_id");
        })->

        // نوع خروجی نمودار
        when($type_value == "price", function ($query) {
            return $query->
            selectRaw("round(sum(total_price )) as value");
        })->

        when($type_value == "amount", function ($query) {
            return $query->
            selectRaw(" round(sum(carton )) as value");
        })->
        pluck("value", "col_id")->
        toArray();
    }

    public static function getExistFromListValue(
        $goods_kind_id,
        $goods_kind_classification_id,
        $type_value,
        $type_report_col_id,
        $form_ids
    )
    {

        return FormItemCurrency::
        join("products", "form_item_currency.product_id", "products.id")->
        where("goods_kind_id", $goods_kind_id)->
        join("product_request_form_form", "product_request_form_form.form_id", "form_item_currency.form_id")->
        join("product_request_forms", "product_request_form_id", "product_request_forms.id")->
        where("product_request_forms.applicant_type_id", 30)->

        // گزارش به تفکیک: مشتری، کالا، طبقه بندی
        when($type_report_col_id == "customer", function ($query) {
            return $query->
            join("orders", "product_request_forms.order_id", "orders.id")->
            groupBy("orders.customer_id")->
            selectRaw("orders.customer_id as col_id");
        })->

        when($type_report_col_id == "product", function ($query) {
            return $query->groupBy("products.id")->
            selectRaw("products.id as col_id");
        })->

        when($type_report_col_id == "goods_kind_classification_option", function ($query) use ($goods_kind_classification_id) {
            return $query->
            join("goods_kind_classification_product", "goods_kind_classification_product.product_id", "products.id")->
            where("goods_kind_classification_id", $goods_kind_classification_id)->
            groupBy("goods_kind_classification_option_id")->
            selectRaw(" goods_kind_classification_option_id as col_id");
        })->

        // نوع خروجی نمودار
        when($type_value == "price", function ($query) {
            return $query->
            selectRaw("round(sum(total_price )) as value");
        })->

        when($type_value == "amount", function ($query) {
            return $query->
            selectRaw(" round(sum(amount )) as value");
        })->
        whereIn("form_item_currency.form_id", $form_ids)->
        pluck("value", "col_id")->
        toArray();
    }


    public static function ReportResultArrayFromOrderFactor($report_result, $order_factor, $status, $object_list, $percent_other)
    {
        $sum_all = array_sum($order_factor);
        foreach ($object_list as $object) {
            $report_result[$object->id][$status->id] = 0;
        }

        foreach ($object_list as $object) {
            if (isset($order_factor[$object->id])) {
                if ($order_factor[$object->id] / $sum_all > $percent_other) {
                    $object->show_result = 1;
                    $report_result[$object->id][$status->id] = $order_factor[$object->id];
                } else {
                    $report_result["other"][$status->id] += $order_factor[$object->id];
                }
            }
        }

        return ["report_result" => $report_result, "object_list" => $object_list];
    }

    public static function ReportResultArrayFromProduction($report_result, $production_card_list, $object_list, $percent_other)
    {
        $sum_all = array_sum($production_card_list);
        foreach ($object_list as $object) {
            $report_result[$object->id]["production"] = 0;
        }
        foreach ($object_list as $object) {
            if (isset($production_card_list[$object->id])) {

                if ($production_card_list[$object->id] / $sum_all > $percent_other) {
                    $object->show_result = 1;
                    $report_result[$object->id]["production"] = $production_card_list[$object->id];
                } else {
                    $report_result["other"]["production"] += $production_card_list[$object->id];
                }
            }
        }

        return ["report_result" => $report_result, "object_list" => $object_list];
    }

    public static function ReportResultArrayFromExistFrom($report_result, $exist_form_list, $status, $object_list, $percent_other)
    {
        $sum_all = array_sum($exist_form_list);
        foreach ($object_list as $object) {
            $report_result[$object->id][$status->id] = 0;
        }
        foreach ($object_list as $object) {
            if (isset($exist_form_list[$object->id])) {

                if ($exist_form_list[$object->id] / $sum_all > $percent_other) {
                    $object->show_result = 1;
                    $report_result[$object->id][$status->id] = $exist_form_list[$object->id];
                } else {
                    $report_result["other"][$status->id] += $exist_form_list[$object->id];
                }
            }
        }

        return ["report_result" => $report_result, "object_list" => $object_list];
    }
}
