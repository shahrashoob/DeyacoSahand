<?php

namespace App\Http\Controllers\Report;

use App\Exports\Report\Report1011_1Export;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind\GoodsKindClassificationOption;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Report\EfficiencyBasedOnMachineLog;
use App\Models\Report\EfficiencyBasedOnMachineLogM;
use App\Models\Utility\Option;
use App\Models\Utility\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class Report1011Controller extends Controller
{
    // گزارش میزان کالای تولید شده
    var $view_path = "report.1011.";
    var $route_path = "report.1011.";
     static $dayBreak = 60;
    public static $route_path_static = "report.1011.";

    public function index()
    {

    }

    public function submit($machine_id = 0)
    {

        if (!$this->checkPermission()) {
            return back()->withErrors("شما مجوز دسترسی به گزارش را ندارید");
        }
        $cross_sectional_management_request = session("cross_sectional_management_request");
        if (!$cross_sectional_management_request) {
            return redirect()->route("report.cross_sectional_management.dashboard.index")->withErrors("لطفا تاریخ شروع و پایان گزارش را مشخص نمایید.");
        }


        $start_date_time = Carbon::parse($cross_sectional_management_request["start_date"]);
        $end_date_time = Carbon::parse($cross_sectional_management_request["end_date"]);
        $report_break_type_id = $cross_sectional_management_request["report_break_type_id"];

        $start_date_minute = $start_date_time->format("YmdHi");
        $end_date_minute = $end_date_time->addMinute()->format("YmdHi");

        $goods_kind_id = $cross_sectional_management_request["goods_kind_id"];
        $classification_id = $cross_sectional_management_request["classification_id"];


        $EfficiencyBasedOnMachineLogTable = EfficiencyBasedOnMachineLogM::class;
        $table_name = "efficiency_based_on_machine_logs_m";
        if ($start_date_time->LessThan(Carbon::now()->addDay(-self::$dayBreak))) {
            $EfficiencyBasedOnMachineLogTable = EfficiencyBasedOnMachineLog::class;
            $table_name = "efficiency_based_on_machine_logs";
            $end_date_time = Carbon::parse($cross_sectional_management_request["end_date"]);
            $end_date_minute = $end_date_time->addMinute()->format("YmdHi");
        }
        //محاسبه مجموع متراژ سیستم
        $sum_amount = $EfficiencyBasedOnMachineLogTable::
        join("products", "products.id", "product_id")->
        where("created_minute_number", ">", $start_date_minute)->
        where("created_minute_number", "<", $end_date_minute)->
        groupBy("unit_id")->
        selectRaw('round( sum(amount)) as amount,unit_id ')->
        pluck("amount", "unit_id")->
        toArray();

        $unit_list = [];
        if (count($sum_amount) > 0) {
            $unit_list = Unit::whereIn("id", array_keys($sum_amount))->pluck("caption", "id")->toArray();
        }

// نمودار به تفکیک هر طبقه
        $line_list_classification_value = Report1011Controller::getReportClassificationList($start_date_time, $end_date_time, $start_date_minute, $end_date_minute, $goods_kind_id, $classification_id, $machine_id, $report_break_type_id);

        $bar_stack_chart_data = Report1011Controller::get_bar_stack_chart_data($start_date_time, $end_date_time, $start_date_minute, $end_date_minute, $goods_kind_id, $classification_id);

        $machine = Machine::find($machine_id);

        return view($this->view_path . "show_report",
            compact(
                "line_list_classification_value", "sum_amount", "bar_stack_chart_data",
                "goods_kind_id", "start_date_time", "end_date_time", "machine", "unit_list"
            ));
    }


    public static function getReportForCrossSection($start_date_time, $end_date_time, $start_date_minute, $end_date_minute, $goods_kind_id, $report_break_type_id, $machine_id = false, $set_date_time = false,)
    {
        if (!Report1011Controller::checkPermission()) {
            return false;
        }
        $break_info = self::getFormat($report_break_type_id);
        $group_by = $break_info["group_by"];
        $diff_function = $break_info["diff_function"];
        $add_time_function = $break_info["add_time_function"];
        $format = $break_info["format"];
        $format_id = $break_info["format_id"];
        $format_caption = $break_info["format_caption"];
        $list_line_report["amount"] = ["name" => "مقدار تولید شده", "data" => []];

        $EfficiencyBasedOnMachineLogTable = EfficiencyBasedOnMachineLogM::class;
        $table_name = "efficiency_based_on_machine_logs_m";
        if ($start_date_time->LessThan(Carbon::now()->addDay(-self::$dayBreak))) {
            $EfficiencyBasedOnMachineLogTable = EfficiencyBasedOnMachineLog::class;
            $table_name = "efficiency_based_on_machine_logs";
        }

        $list_line_all = $EfficiencyBasedOnMachineLogTable::
        join("products", "products.id", "product_id")->
        where("created_minute_number", ">", $start_date_minute)->
        where("created_minute_number", "<", $end_date_minute)->
        where("goods_kind_id", $goods_kind_id)->
        when($machine_id, function ($query) use ($machine_id, $table_name) {
            return $query->where("$table_name.machine_id", $machine_id);
        })->
        selectRaw("$group_by as date ,round( sum(amount)) as amount ")->
        groupBy("date")->
        orderBy("date")->get();

        $list_line_all = collect($list_line_all)->
        keyBy('date');

        $list_line=[];
        $diff_day = $start_date_time->$diff_function($end_date_time) + 1;
        $line_start_date = Carbon::parse($start_date_time);
        for ($i = 0; $i <= $diff_day; $i++) {

            $string_date = $line_start_date->format($format);
            $string_date_number = $line_start_date->format($format_id);

            if (!isset($list_line_all[$string_date_number])) {
                $list_line[$string_date_number] = ["date" => $string_date, "amount" => 0];
            } elseif ($set_date_time) {
                $list_line[$string_date_number]=$list_line_all[$string_date_number];
                $list_line[$string_date_number]["date"] = $string_date;
            }
            $line_start_date->$add_time_function();
        }


        foreach ($list_line as $item) {

            $list_line_report["amount"]["data"][] = [
                "caption" => jdate(Carbon::parse($item["date"])->timestamp)->format($format_caption),
                "value" => $item["amount"]
            ];
        }

        return $list_line_report;
    }


    public static function getReportClassificationList($start_date_time, $end_date_time, $start_date_minute, $end_date_minute, $goods_kind_id, $classification_id, $machine_id, $report_break_type_id)
    {

        if (!Report1011Controller::checkPermission()) {
            return false;
        }
        $list_line_report = [];
        $list_line_report_data = [];

        $break_info = self::getFormat($report_break_type_id);
        $group_by = $break_info["group_by"];
        $diff_function = $break_info["diff_function"];
        $add_time_function = $break_info["add_time_function"];
        $format = $break_info["format"];
        $format_id = $break_info["format_id"];
        $format_caption = $break_info["format_caption"];

        $option_list = $goods_kind_classification_item = GoodsKindClassificationOption::where([
            "goods_kind_classification_id" => $classification_id
        ])->get();

        $list_all = [
        ];
        $diff_day = $start_date_time->$diff_function($end_date_time) + 1;
        $line_start_date = Carbon::parse($start_date_time);
        for ($i = 0; $i <= $diff_day; $i++) {
            $string_date = $line_start_date->format($format);
            $string_day_number = $line_start_date->format($format_id);
            $list_all[$string_day_number] = ["date" => $string_date, "amount" => 0];
            $line_start_date->$add_time_function();
        }

        foreach ($option_list as $goods_kind_classification_item) {
            $list_line_report["classification_item_" . $goods_kind_classification_item->id] = [
                "name" => $goods_kind_classification_item->caption,
                "data" => []
            ];

            $EfficiencyBasedOnMachineLogTable = EfficiencyBasedOnMachineLogM::class;
            $table_name = "efficiency_based_on_machine_logs_m";
            if ($start_date_time->LessThan(Carbon::now()->addDay(-self::$dayBreak))) {
                $EfficiencyBasedOnMachineLogTable = EfficiencyBasedOnMachineLog::class;
                $table_name = "efficiency_based_on_machine_logs";
            }
            ############ نمودار خطی

            // به ازای هر طبقه بندی یک لیست از مقدار و کد تاریخ به دست می آوریم.
            $list_line_list = $EfficiencyBasedOnMachineLogTable::
            join("products", "products.id", "product_id")->
            join("goods_kind_classification_product", "products.id", "goods_kind_classification_product.product_id")->
            where("created_minute_number", ">", $start_date_minute)->
            where("created_minute_number", "<", $end_date_minute)->
            where("goods_kind_id", $goods_kind_id)->
            where("goods_kind_classification_option_id", $goods_kind_classification_item->id)->
            where("goods_kind_classification_id", $goods_kind_classification_item->goods_kind_classification_id)->
            when($machine_id, function ($query) use ($machine_id, $table_name) {
                return $query->where("$table_name.machine_id", $machine_id);
            })->
            selectRaw("$group_by as date ,round( sum(amount)) as amount ")->
            groupBy("date")->
            orderBy("date")->
            get()->
            keyBy("date");

            // لیست قیلی را با کد تاریخ و تاریخ صحیح میلادی ذخیره می کنیم.
            $list_line_by_valid_id=[];
            foreach ($list_line_list as $item) {
                $dateString=$item["date"]."";
                $key = substr($dateString, 0, 4) . '-' .
                    substr($dateString, 4, 2) . '-' .
                    substr($dateString, 6, 2);
                $list_line_by_valid_id[$item["date"]]=[
                       "date" => $key,
                       "amount" => $item["amount"]
                   ];
            }

            $diff_day = $start_date_time->$diff_function($end_date_time) + 1;
            $line_start_date = Carbon::parse($start_date_time);
            $list_line=[]; // لیست تولید به ازای هر طبقه بندی به ترتیب تاریخ
            for ($i = 0; $i <= $diff_day; $i++) {

                $string_date = $line_start_date->format($format);
                $string_day_number = $line_start_date->format($format_id);

                if (!isset($list_line[$string_day_number])) {
                    $list_line[$string_day_number] = ["date" => $string_date, "amount" => 0];
                }

                if (!isset($list_all[$string_day_number])) {
                    $list_all[$string_day_number] = ["date" => $string_date, "amount" => 0];
                }

                if(isset($list_line_by_valid_id[$string_day_number])){
                    $list_line[$string_day_number]["amount"]=$list_line_by_valid_id[$string_day_number]["amount"];
                }

                if (isset($list_line[$string_day_number]["amount"])) {
                    $list_all[$string_day_number]["amount"] += $list_line[$string_day_number]["amount"];
                }

                $line_start_date->$add_time_function();
            }

            // لیست همه تولیدات به تفکیک طبقه بندی
            $list_line_report_data[$goods_kind_classification_item->id] = $list_line;

        }

        if (!$machine_id) {
            foreach ($option_list as $goods_kind_classification_item) {

                foreach ($list_line_report_data[$goods_kind_classification_item->id] as $item) {
                    // تبدیل تاریخ
                    $list_line_report["classification_item_" . $goods_kind_classification_item->id]["data"][] = [
                        "caption" => jdate(Carbon::parse($item["date"])->timestamp)->format($format_caption),
                        "value" => $item["amount"]
                    ];
                }
            }
        }
        $list_line_report["classification_item"] = $list_line_report["classification_item_" . $goods_kind_classification_item->id];
        unset($list_line_report["classification_item_" . $goods_kind_classification_item->id]);


        $list_line_report["list_line_report"]["name"] = "مقدار تولید شده";
        foreach ($list_all as $item) {
            // تبدیل تاریخ
            $list_line_report["list_line_report"]["data"][] = [
                "caption" => jdate(Carbon::parse($item["date"])->timestamp)->format($format_caption),
                "value" => $item["amount"]
            ];
        }

        return ["list_line_report" => $list_line_report];
    }

    public static function getFormat($report_break_type_id)
    {
        switch ($report_break_type_id) {
            case 1: // روز
                return
                    ["group_by" => "created_day_number",
                        "diff_function" => "diffInDays",
                        "add_time_function" => "addDay",
                        "format" => "Y-m-d",
                        "format_id" => "Ymd",
                        "format_caption" => "Y/m/d",
                    ];
                break;
            case 2: // ماه
                return
                    ["group_by" => "created_month_number",
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
        if (!$post_user->checkButtonPermission(Report1011Controller::$route_path_static . "index")) {
            return false;
        }

        return true;
    }

    public static function get_bar_stack_chart_data($start_date_time, $end_date_time, $start_date_minute, $end_date_minute, $goods_kind_id, $classification_id)
    {

        $data_report = [
            "data" => [
//                [ "caption" => "ماشین 1" ],
//                [ "caption" => "ماشین 2" ]
            ],
            "series" => [
//                [ "name" => "لباسی", "data" => [ 5, 2 ] ],
//                [ "name" => "پرده ای", "data" => [ 3, 7 ] ],
            ]
        ];


        $option_list = $goods_kind_classification_item = GoodsKindClassificationOption::where([
            "goods_kind_classification_id" => $classification_id
        ])->get();


        $EfficiencyBasedOnMachineLogTable = EfficiencyBasedOnMachineLogM::class;
        $table_name = "efficiency_based_on_machine_logs_m";
        if ($start_date_time->LessThan(Carbon::now()->addDay(-self::$dayBreak))) {
            $EfficiencyBasedOnMachineLogTable = EfficiencyBasedOnMachineLog::class;
            $table_name = "efficiency_based_on_machine_logs";
        }

        // به ازای هر ماشین مقدار کالای تولید شده در طبقه بندی های مختلف را به دست می آوریم.
        $list = $EfficiencyBasedOnMachineLogTable::
        join("products", "products.id", "product_id")->
        join("machines", "machines.id", "machine_id")->
        join("goods_kind_classification_product", "products.id", "goods_kind_classification_product.product_id")->
        join("goods_kind_classification_options", "goods_kind_classification_options.id", "goods_kind_classification_option_id")->
        where("created_minute_number", ">", $start_date_minute)->
        where("created_minute_number", "<=", $end_date_minute)->
        where("goods_kind_classification_options.goods_kind_classification_id", $classification_id)->
        selectRaw('machine_id,machines.caption as machine_caption ,goods_kind_classification_options.caption,goods_kind_classification_option_id,round( sum(amount)) as amount ')->
        groupBy("machine_id")->
        groupBy("goods_kind_classification_option_id")->
        orderBy("station_id")->
        orderBy("number_code")->
        get();

        $machine_data = [];
        foreach ($list as $item) {
            $machine_data[$item->machine_id] = 0;
            $data_report["data"][$item->machine_id] = ["caption" => $item->machine_caption];
        }

        foreach ($option_list as $goods_kind_classification_item) {
            $data_report["series"]["classification_item_" . $goods_kind_classification_item->id] = [
                "name" => $goods_kind_classification_item->caption,
                "data" => $machine_data
            ];

        }

        foreach ($list as $machine_item) {
            $data_report["series"]["classification_item_" . $machine_item->goods_kind_classification_option_id]["data"][$machine_item->machine_id] = $machine_item->amount;

        }


        return $data_report;
    }

    public function export_ReportForCrossSection()
    {
        if (!$this->checkPermission()) {
            return back()->withErrors("شما مجوز دسترسی به گزارش را ندارید");
        }

        $cross_sectional_management_request = session("cross_sectional_management_request");
        if (!$cross_sectional_management_request) {
            return redirect()->route("report.cross_sectional_management.dashboard.index")->withErrors("لطفا تاریخ شروع و پایان گزارش را مشخص نمایید.");
        }
        $start_date_time = Carbon::parse($cross_sectional_management_request["start_date"]);
        $end_date_time = Carbon::parse($cross_sectional_management_request["end_date"]);

        $start_date_minute = $start_date_time->format("YmdHi");
        $end_date_minute = $end_date_time->addMinute()->format("YmdHi");

        $goods_kind_id = $cross_sectional_management_request["goods_kind_id"];


        $EfficiencyBasedOnMachineLogTable = EfficiencyBasedOnMachineLogM::class;
        $table_name = "efficiency_based_on_machine_logs_m";
        if ($start_date_time->LessThan(Carbon::now()->addDay(-self::$dayBreak))) {
            $EfficiencyBasedOnMachineLogTable = EfficiencyBasedOnMachineLog::class;
            $table_name = "efficiency_based_on_machine_logs";
            $end_date_time = Carbon::parse($cross_sectional_management_request["end_date"]);
            $end_date_minute = $end_date_time->addDay()->format("YmdHi");
        }

        $list = $EfficiencyBasedOnMachineLogTable::
        join("products", "products.id", "product_id")->
        join("machines", "machines.id", "machine_id")->
        where("created_minute_number", ">", $start_date_minute)->
        where("created_minute_number", "<=", $end_date_minute)->
        where("goods_kind_id", $goods_kind_id)->
        selectRaw('machines.caption,round( sum(amount)) as amount ')->
        groupBy("machine_id")->
        orderBy("machine_id")->
        get();;


        $export = new Report1011_1Export();
        $export->list = $list;

        return Excel::download($export, 'contractor_report_1_' . jdate(Carbon::now()->timestamp)->format('Y_m_d') . '.xlsx');


    }
}
