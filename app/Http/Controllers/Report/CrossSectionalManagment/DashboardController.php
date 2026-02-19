<?php

namespace App\Http\Controllers\Report\CrossSectionalManagment;

use App\Exports\Report\Report1009_1Export;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Report\Report1009Controller;
use App\Http\Controllers\Report\Report1010Controller;
use App\Http\Controllers\Report\Report1011Controller;
use App\Http\Controllers\Report\Report1012Controller;
use App\Http\Controllers\Report\Report1013Controller;
use App\Models\LineProduct\GoodsKind\GoodsKindPost;
use App\Models\Utility\DateTime;
use App\Models\Utility\Option;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // report/cross_sectional_management/dashboard
    var $view_path = "report.cross_sectional_management.dashboard.";
    var $route_path = "report.cross_sectional_management.dashboard.";

    public function index()
    {
        $cross_sectional_data=session("cross_sectional_management_request");
        $start_date=null;
        $end_date=null;
        $goods_kind_id=0;
        $report_break_type_id=1;
        $classification_id=0;
        if($cross_sectional_data){
            $start_date=$cross_sectional_data["start_date"];
            $end_date=$cross_sectional_data["end_date"];
            $goods_kind_id=$cross_sectional_data["goods_kind_id"];
            $classification_id=$cross_sectional_data["classification_id"];
            $report_break_type_id=$cross_sectional_data["report_break_type_id"];
        }
        $allowed_goods_kind_ids = GoodsKindPost::getAllowedGoodsKindId();
        $goods_kind_option = Option::get("goods_kind", $goods_kind_id, 0, $allowed_goods_kind_ids);
        $classification_option = Option::get("goods_kind_classification_group_option", $classification_id, $goods_kind_id);
        $report_break_option = Option::get("report_break", $report_break_type_id);

        return view($this->view_path . "index", compact("goods_kind_option","start_date","end_date","report_break_option","classification_option"));
    }

    public function DCCSM_SortLink()
    {
        // cross_sectional_management
        return $this->index();
    }

    public function submit(Request $request)
    {


        $start_date_time = Carbon::parse($request->start_date);
        $end_date_time = Carbon::parse($request->end_date);
        $report_break_type_id=$request->report_break_type_id;

        if (!isset($request->report)) {
            return back()->withErrors("لطفا حداقل یک گزارش انتخاب نمایید.");
        }
        $report_list = $request->report;
        if (count($report_list) > 4) {
            return back()->withErrors("لطفا حداکثر 4 گزارش انتخاب نمایید.");
        }

        $start_date_day = Carbon::parse($request->start_date)->format("Ymd");
        $end_date_day = Carbon::parse($request->end_date)->format("Ymd");

        $start_date_minute = Carbon::parse($request->start_date)->format("YmdHi");
        $end_date_minute = Carbon::parse($request->end_date)->format("YmdHi");


        $goods_kind_id = $request->goods_kind_id;

        session(["cross_sectional_management_request" => $request->all()]);

        $start_jdate = jdate(Carbon::parse($start_date_time)->timestamp)->format('از ساعت  H:i  %A Y/m/d ');
        $end_jdate = jdate(Carbon::parse($end_date_time)->timestamp)->format('تا ساعت  H:i  %A Y/m/d ');

        $start_jdate_days = jdate(Carbon::parse($start_date_time)->timestamp)->format('از     %A Y/m/d ');
        $end_jdate_days = jdate(Carbon::parse($end_date_time)->timestamp)->format('تا     %A Y/m/d ');

        $line_list_1009 = null;
        $line_list_1010 = null;
        $line_list_1011 = null;
        $line_list_1012 = null; // گزارش وضعیت بسته بندی ها
        $line_list_1013 = null;

        $result_check_time = self::checkTimeOfReport($start_date_time, $end_date_time,$report_break_type_id);
        if (!$result_check_time["result"]) {
            return back()->withErrors($result_check_time["error"]);
        }

        if (isset($report_list[1009])) {

            $end_date_day = Carbon::parse($request->end_date)->addDay()->format("Ymd");
            $end_date_minute = Carbon::parse($request->end_date)->addDay()->format("YmdHi");

            $chart_data[1009] = $line_list_1009 = Report1009Controller::getReportForCrossSection($start_date_time, $end_date_time, $start_date_day, $end_date_day, $goods_kind_id,$report_break_type_id);
            $chart_caption[1009] = '***';
        }
        if (isset($report_list[1010])) {

            $chart_data[1010] = $line_list_1010 = Report1010Controller::getReportForCrossSection($start_date_time, $end_date_time, $start_date_minute, $end_date_minute, $goods_kind_id);
            $chart_caption[1010] = '***';
        }
        if (isset($report_list[1011])) {
            $chart_data[1011] = $line_list_1011 = Report1011Controller::getReportForCrossSection($start_date_time, $end_date_time, $start_date_minute, $end_date_minute, $goods_kind_id,$report_break_type_id,false,true);
            $chart_caption[1011] = '***';
        }

        if (isset($report_list[1012])) {
            $chart_data[1012] = null;
            $chart_caption[1012] = "گزارش وضعیت بسته بندی ها  " . $start_jdate . " " . $end_jdate;
        }

        if (isset($report_list[1013])) {
            $chart_data[1013] = Report1013Controller::getReportForCrossSection($start_date_time, $end_date_time, $goods_kind_id);
            $chart_caption[1013] = "گزارش سفارشات به تفکیک مشتری (ریالی)  " . $start_jdate . " " . $end_jdate;
        }

        return view($this->view_path . "show_report",
            compact(
                "line_list_1009", "line_list_1010", "line_list_1011", "report_list", "chart_data",
                "goods_kind_id", "start_date_time", "end_date_time",
                "start_jdate", "end_jdate",
                "start_jdate_days", "end_jdate_days",
                "chart_caption"
            ));

    }

    public function checkTimeOfReport($start_datetime, $end_datetime,$report_break_type_id)
    {
switch ($report_break_type_id){
    case 1: // روز
        if (Carbon::now()->diffInDays($start_datetime) > 60 && $start_datetime->diffInMinutes($start_datetime->format("Y/m/d H:00")) > 0) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه از تاریخ شروع درخواستی شما بیش از دو ماه گذشته است، امکان دریافت گزارش به تفکیک دقیقه مقدور نمی باشد. لطفا مقدار  دقیقه را صفر انتخاب نمایید."
            ];
        }
        if (Carbon::now()->diffInDays($end_datetime) > 60 && $end_datetime->diffInMinutes($end_datetime->format("Y/m/d H:00")) > 0) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه از تاریخ پایان درخواستی شما بیش از دو ماه گذشته است، امکان دریافت گزارش به تفکیک دقیقه مقدور نمی باشد. لطفا مقدار  دقیقه را صفر انتخاب نمایید."
            ];
        }

        break;
    case 2: // ماه
        if (Carbon::now()->diffInDays($start_datetime) > 60 && $start_datetime->diffInHours($start_datetime->format("Y/m/d 00:00")) > 0) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه از نوع گزارش به تفکیک ماه می باشد، امکان دریافت گزارش به تفکیک دقیقه و ساعت مقدور نمی باشد. لطفا مقدار  دقیقه و ساعت را صفر انتخاب نمایید."
            ];
        }
        if (Carbon::now()->diffInDays($end_datetime) > 60 && $end_datetime->diffInHours($end_datetime->format("Y/m/d 00:00")) > 0) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه از نوع گزارش به تفکیک ماه می باشد، امکان دریافت گزارش به تفکیک دقیقه و ساعت مقدور نمی باشد. لطفا مقدار  دقیقه و ساعت را صفر انتخاب نمایید."
            ];
        }

        break;
}

        return [
            "result" => true
        ];
    }

}
