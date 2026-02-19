<?php

namespace App\Http\Controllers\Report;

use App\Exports\Report\Report1009_1Export;
use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\GoodsKind\GoodsKindClassification;
use App\Models\LineProduct\GoodsKind\GoodsKindClassificationOption;
use App\Models\LineProduct\Product;
use App\Models\Production\ProductionFormLog;
use App\Models\Report\R1006\Report1006ProductionForm;
use App\Models\Utility\DateTime;
use App\Models\Utility\Option;
use App\Models\Utility\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class Report1009Controller extends Controller
{
    // گزارش میزان کالای استخراج شده
    var $view_path = "report.1009.";
    var $route_path = "report.1009.";
    public static $route_path_static = "report.1009.";

    public function index()
    {

    }

    public function submit($percent_pie_chart = ".01")
    {
        // بروز رسانی ستون تاریخ ایجاد عددی
        PackingForm::UpdateCratedAtNumber();

        if (!$this->checkPermission()) {
            return back()->withErrors("شما مجوز دسترسی به گزارش را ندارید");
        }

        $cross_sectional_management_request = session("cross_sectional_management_request");
        if (!$cross_sectional_management_request) {
            return redirect()->route("report.cross_sectional_management.dashboard.index")->withErrors("لطفا تاریخ شروع و پایان گزارش را مشخص نمایید.");
        }
//return  $cross_sectional_management_request;

        $report_break_type_id = $cross_sectional_management_request["report_break_type_id"];

        $start_date_time = Carbon::parse($cross_sectional_management_request["start_date"]);
        $end_date_time = Carbon::parse($cross_sectional_management_request["end_date"]);

        $start_date_number = Carbon::parse($cross_sectional_management_request["start_date"])->format("Ymd");
        $end_date_number = Carbon::parse($cross_sectional_management_request["end_date"])->addDay()->format("Ymd");

        $goods_kind_id = $cross_sectional_management_request["goods_kind_id"];
        $classification_id = $cross_sectional_management_request["classification_id"];
        $classification = GoodsKindClassification::find($classification_id);

//     return   $packing_list = PackingForm::
//        join("packing_form_item", "packing_forms.id", "packing_form_id")->
//        join("products", "products.id", "packing_form_item.product_id")->
//        whereNull("packing_forms.deleted_at")->
//        where("packing_forms.status_id", "!=", 7007007)-> // تغییر یافته
//        where("goods_kind_id", $goods_kind_id)->
//        where("packing_forms.created_at", ">=", $start_date_time)->
//        where("packing_forms.created_at", "<", $end_date_time)->
//     distinct("packing_form_id")->
//        select("packing_forms.code")->
//        pluck("code");

        $sum_all_list = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        join("products", "products.id", "packing_form_item.product_id")->
        addSelect(DB::raw("unit_id,round(SUM(amount),2) as sum_amount, round(SUM(final_amount),2) as sum_final_amount,round(SUM(sub_amount),2) as sum_sub_amount, Count(packing_form_id) as count_packing_form,product_id"))->
        whereNull("packing_forms.deleted_at")->
        where("packing_forms.status_id", "!=", 7007007)-> // تغییر یافته
        where("goods_kind_id", $goods_kind_id)->
        where("packing_forms.created_at", ">=", $start_date_time)->
        where("packing_forms.created_at", "<", $end_date_time)->
        groupBy("unit_id")->
        get()->
        keyBy("unit_id")->
        toArray();

        $sum_sub_all_list = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        join("products", "products.id", "packing_form_item.product_id")->
        addSelect(DB::raw("sub_unit_id,round(SUM(sub_amount),2) as sum_sub_amount"))->
        whereNull("packing_forms.deleted_at")->
        where("packing_forms.status_id", "!=", 7007007)->
        where("goods_kind_id", $goods_kind_id)->
        where("packing_forms.created_at", ">=", $start_date_time)->
        where("packing_forms.created_at", "<", $end_date_time)->
        groupBy("sub_unit_id")->
        get()->
        keyBy("sub_unit_id")->
        toArray();

        $count_packing_form = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        join("products", "products.id", "packing_form_item.product_id")->
        whereNull("packing_forms.deleted_at")->
        where("packing_forms.status_id", "!=", 7007007)->
        where("goods_kind_id", $goods_kind_id)->
        where("packing_forms.created_at", ">=", $start_date_time)->
        where("packing_forms.created_at", "<", $end_date_time)->
        distinct("packing_form_id")->
        count();

        $unit_list = Unit::pluck("caption", "id")->toArray();


        ############################# نمودار خطی
      //  $line_list_value = Report1009Controller::getReportForCrossSection($start_date_time, $end_date_time, $start_date_number, $end_date_number, $goods_kind_id, $report_break_type_id);

        ############################# نمودار خطی به تفکیک طبقه بندی
        $line_list_classification_value = Report1009Controller::getReportClassificationList($start_date_time, $end_date_time, $start_date_number, $end_date_number, $goods_kind_id, $classification_id, $report_break_type_id);

        ######################## نمودار دایره ای


        $sum_all_pie_chart = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        join("products", "products.id", "packing_form_item.product_id")->
        whereNull("packing_forms.deleted_at")->
        where("packing_forms.status_id", "!=", 7007007)->
        where("goods_kind_id", $goods_kind_id)->
        where("packing_forms.created_at", ">=", $start_date_time)->
        where("packing_forms.created_at", "<", $end_date_time)->
        sum("final_amount");

        $all_product_pie_chart = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        join("products", "products.id", "packing_form_item.product_id")->
        whereNull("packing_forms.deleted_at")->
        where("packing_forms.status_id", "!=", 7007007)->
        where("goods_kind_id", $goods_kind_id)->
        where("packing_forms.created_at", ">=", $start_date_time)->
        where("packing_forms.created_at", "<", $end_date_time)->
        groupBy("product_id")->
        selectRaw("concat( products.code, '- ',products.caption) as caption,
        sum(final_amount) as value, sum(final_amount)/ " . $sum_all_pie_chart . " as percent")->
        get();

        $other_count = 0;
        $product_pie_chart = [];
        foreach ($all_product_pie_chart as $item) {
            if ($item->percent < $percent_pie_chart) {
                $other_count += $item->value;
            } else {
                $product_pie_chart[] = $item;
            }
        }

        $product_pie_chart[] = [
            "value" => $other_count,
            "caption" => "سایر کالا ها ( کمتر از " . ($percent_pie_chart * 100) . " درصد )"
        ];

        $list_classification = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        join("products", "products.id", "packing_form_item.product_id")->
        join("goods_kind_classification_product", "products.id", "goods_kind_classification_product.product_id")->
        selectRaw('unit_id,sub_unit_id, goods_kind_classification_option_id as id,round( sum(amount),2) as amount ,round( sum(final_amount),2) as final_amount,round( sum(sub_amount),2) as sub_amount')->
        whereNull("packing_forms.deleted_at")->
        where("packing_forms.status_id", "!=", 7007007)->
        where("goods_kind_id", $goods_kind_id)->
        where("goods_kind_classification_id", $classification_id)->
        where("packing_forms.created_at", ">=", $start_date_time)->
        where("packing_forms.created_at", "<", $end_date_time)->
        groupBy("unit_id")->
        groupBy("goods_kind_classification_option_id")->
        get()->keyBy(function ($key) {
            return $key->unit_id . "_" . $key->id;
        });

        $goods_kind_classification_options = GoodsKindClassificationOption::where("goods_kind_classification_id", $classification_id)->pluck("caption", "id")->toArray();


//        ######################## نمودار دایره ای به تفکیک درجه
//        $degree_pie_chart = PackingForm::
//        join( "packing_form_item", "packing_forms.id", "packing_form_id" )->
//        join( "degrees", "degrees.id", "degree_id" )->
//        selectRaw( 'DATE(packing_forms.created_at) as date ,round( sum(amount),2) as amount ,round( sum(final_amount),2) as amount_after_control' )->
//        whereNull( "packing_forms.deleted_at" )->
//        where( "packing_forms.status_id", "!=", 7007007 )->
//        where( "goods_kind_id", $goods_kind_id )->
//        where( "packing_forms.created_at", ">", $start_date_time )->
//        where( "packing_forms.created_at", "<", $end_date_time )->
//        groupBy( "degree_id" )->
//        selectRaw( "concat( degrees.code, '- ',degrees.caption) as caption, sum(amount_after_control) as value" )->get();
//

        return view($this->view_path . "show_report",
            compact(
                "sum_all_list", "line_list_classification_value", "product_pie_chart", "percent_pie_chart", "list_classification",
                "classification", "goods_kind_classification_options",
                "goods_kind_id", "start_date_time", "end_date_time", "count_packing_form", "unit_list", "sum_sub_all_list"
            ));
    }

    public static function getReportForCrossSection($start_date_time, $end_date_time, $start_date_number, $end_date_number, $goods_kind_id, $report_break_type_id)
    {
        if (!Report1009Controller::checkPermission()) {
            return false;
        }

        $break_info = self::getFormat($report_break_type_id);
        $group_by = $break_info["group_by"];
        $diff_function = $break_info["diff_function"];
        $add_time_function = $break_info["add_time_function"];
        $format = $break_info["format"];
        $format_id = $break_info["format_id"];
        $format_caption = $break_info["format_caption"];

        ############################# نمودار خطی
        $list_line_all = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        join("products", "products.id", "packing_form_item.product_id")->
        selectRaw("$group_by, round( sum(amount),2) as amount ,round( sum(final_amount),2) as amount_after_control")->
        whereNull("packing_forms.deleted_at")->
        where("packing_forms.status_id", "!=", 7007007)->
        where("goods_kind_id", $goods_kind_id)->
        where("created_at_number", ">", $start_date_number)->
        where("created_at_number", "<", $end_date_number)->
        groupBy($group_by)->
        orderBy("created_at_number")->
        get();

        $list_line_all = collect($list_line_all)->
        keyBy($group_by);

        $list_line=[];

        $diff_day = $start_date_time->$diff_function($end_date_time) + 1;
        $line_start_date = Carbon::parse($start_date_time);
        for ($i = 0; $i <= $diff_day; $i++) {

            $string_date = $line_start_date->format($format_id);
            $string_caption = $line_start_date->format($format);
            if (!isset($list_line_all[$string_date])) {
                $list_line[$string_date] = ["date" => $string_caption, "amount" => 0, "amount_after_control" => 0];
            } else {
                $list_line[$string_date]=$list_line_all[$string_date];
                $list_line[$string_date]["date"] = $string_caption;
            }
            $line_start_date->$add_time_function();
        }

        $list_line_report["amount"] = ["name" => "مقدار اولیه", "data" => []];
        $list_line_report["amount_after_control"] = ["name" => "مقدار نهایی", "data" => []];


        foreach ($list_line as $item) {

            $list_line_report["amount"]["data"][] = [
                "caption" => jdate(Carbon::parse($item["date"])->timestamp)->format($format_caption),
                "value" => $item["amount"]
            ];
            $list_line_report["amount_after_control"]["data"][] = [
                "caption" => $item["date"],
                "value" => $item["amount_after_control"]
            ];
        }

//"list_line"=>$list_line,
        return ["list_line_report" => $list_line_report];
    }

    public static function getReportClassificationList($start_date_time, $end_date_time, $start_date_number, $end_date_number, $goods_kind_id, $classification_id, $report_break_type_id)
    {

        if (!Report1009Controller::checkPermission()) {
            return false;
        }

        $break_info = self::getFormat($report_break_type_id);
        $group_by = $break_info["group_by"];
        $diff_function = $break_info["diff_function"];
        $add_time_function = $break_info["add_time_function"];
        $format = $break_info["format"];
        $format_id = $break_info["format_id"];
        $format_caption = $break_info["format_caption"];
        $list_line_report = [];
        $list_line_report_data = [];

        $option_list = GoodsKindClassificationOption::where([
            "goods_kind_classification_id" => $classification_id
        ])->get();

        $list_line_all = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        join("products", "products.id", "packing_form_item.product_id")->
        join("goods_kind_classification_product", "products.id", "goods_kind_classification_product.product_id")->
        selectRaw("$group_by, round( sum(amount),2) as amount ,goods_kind_classification_option_id,goods_kind_classification_option_id")->
        whereNull("packing_forms.deleted_at")->
        where("packing_forms.status_id", "!=", 7007007)->
        where("goods_kind_id", $goods_kind_id)->
        where("created_at_number", ">", $start_date_number)->
        where("created_at_number", "<", $end_date_number)->
        orderBy("created_at_number")->
        where("goods_kind_classification_id", $classification_id)->
        groupBy("goods_kind_classification_option_id")->
        groupBy($group_by)->
        get()->
        keyBy(function ($item) use ($group_by) {
            return $item[$group_by] . "_" . $item["goods_kind_classification_option_id"];
        });;;

        $list_all = [
        ];
        foreach ($option_list as $goods_kind_classification_item) {
            $list_line_report["classification_item_" . $goods_kind_classification_item->id] = [
                "name" => $goods_kind_classification_item->caption,
                "data" => []
            ];


            ############################# نمودار
            $list_line = [];


            $diff_day = $start_date_time->$diff_function($end_date_time) + 1;
            $line_start_date = Carbon::parse($start_date_time);
            for ($i = 0; $i <= $diff_day; $i++) {

                $string_date = $line_start_date->format($format_id);
                $string_caption = $line_start_date->format($format);

                // ست کردن برای طبقه بندی
                if (!isset($list_line[$string_date."_".$goods_kind_classification_item->id])) {
                    $list_line[$string_date] = ["date" => $string_caption, "amount" => 0];
                } else {
                    $list_line[$string_date]["date"] = $string_caption;
                }

                if (isset($list_line_all[$string_date."_".$goods_kind_classification_item->id])) {
                    $list_line[$string_date]["amount"] = $list_line_all[$string_date."_".$goods_kind_classification_item->id]->amount;
                }

                // ست کردن برای کلی
                if (!isset($list_all[$string_date])) {
                    $list_all[$string_date] = ["date" => $string_caption, "amount" => 0];
                } else {
                    $list_all[$string_date]["date"] = $string_caption;
                }

                if (isset($list_line_all[$string_date."_".$goods_kind_classification_item->id])) {
                    $list_all[$string_date]["amount"] += $list_line_all[$string_date."_".$goods_kind_classification_item->id]->amount;
                }

                $line_start_date->$add_time_function();
            }


            $list_line_report_data[$goods_kind_classification_item->id] = $list_line;


        }



        foreach ($option_list as $goods_kind_classification_item) {
            foreach ($list_line_report_data[$goods_kind_classification_item->id] as $item) {

                $list_line_report["classification_item_" . $goods_kind_classification_item->id]["data"][] = [
                    "caption" => jdate(Carbon::parse($item["date"])->timestamp)->format($format_caption),
                    "value" => $item["amount"]
                ];
            }

        }
        $list_line_report["classification_item"] = $list_line_report["classification_item_" . $goods_kind_classification_item->id];
        unset($list_line_report["classification_item_" . $goods_kind_classification_item->id]);

        $list_line_report["list_line_report"]["name"]="مقدار اولیه";
        foreach ($list_all as $item) {

            $list_line_report["list_line_report"]["data"][] = [
                "caption" => jdate(Carbon::parse($item["date"])->timestamp)->format($format_caption),
                "value" => $item["amount"]
            ];
        }



//"list_line"=>$list_line,
        return ["list_line_report" => $list_line_report];
    }

    public function export(Request $request)
    {

        $start_date_time = $request->start_date_time;
        $end_date_time = $request->end_date_time;
        $goods_kind_id = $request->goods_kind_id;

        $list = PackingFormItem::
        join("packing_forms", "packing_forms.id", "packing_form_id")->
        join("products", "products.id", "packing_form_item.product_id")->
        whereNull("packing_forms.deleted_at")->
        where("packing_forms.status_id", "!=", 7007007)->
        where("goods_kind_id", $goods_kind_id)->
        where("packing_forms.created_at", ">", $start_date_time)->
        where("packing_forms.created_at", "<", $end_date_time)->
        select("packing_form_item.*")->
        get();


        $export = new Report1009_1Export();
        $export->list = $list;

        return Excel::download($export, "report_1009" . jdate(Carbon::now()->timestamp)->format('Y_m_d') . '.xlsx');

    }

    public static function getFormat($report_break_type_id)
    {
        switch ($report_break_type_id) {
            case 1: // روز
                return
                    ["group_by" => "created_at_number",
                        "diff_function" => "diffInDays",
                        "add_time_function" => "addDay",
                        "format" => "Y-m-d",
                        "format_id" => "Ymd",
                        "format_caption" => "Y/m/d",
                    ];
                break;
            case 2: // ماه
                return
                    ["group_by" => "created_at_month_number",
                        "diff_function" => "diffInMonths",
                        "add_time_function" => "addMonth",
                        "format" => "Y-m",
                        "format_id" => "Ym",
                        "format_caption" => "Y/m",
                    ];

                break;

        }
    }

    public static function checkPermission()
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission(Report1009Controller::$route_path_static . "index")) {
            return false;
        }

        return true;
    }
}
