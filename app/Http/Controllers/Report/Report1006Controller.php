<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingFormLog;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Product\Fault\ProductFaultProperties;
use App\Models\LineProduct\Product\Fault\ProductFaultPropertyOption;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionFormLog;
use App\Models\QualityControl\QualityControlPackingForm;
use App\Models\QualityControl\QualityControlProductFault;
use App\Models\Report\R1005\Report1005;
use App\Models\Report\R1006\Report1006ProductionForm;
use App\Models\Utility\DateTime;
use App\Models\Utility\Option;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Report1006Controller extends Controller
{
    public function index()
    {
        return view("report/1006/index");
    }

    public function submit_form(Request $request)
    {

        $request_sesssion = session("request_1006");



        if ($request->start_date && $request->end_date) {

            $start_date_time = DateTime::getDateTimeFromRequest($request, "start_date");
            $end_date_time = DateTime::getDateTimeFromRequest($request, "end_date");
            session(["request_1006" => $request->all()]);
        }
        else if (  $request_sesssion) {
            // $request = $request_sesssion;
            $start_date_time=Carbon::parse($request_sesssion["start_date"]);
            $end_date_time=Carbon::parse($request_sesssion["end_date"]);
        }



        if (!$start_date_time) {
            return redirect()->route("report.1006.index")->with("error", "لطفا تاریخ شروع و پایان را مشخص نمایید.");
        }

        // محاسبه راندمان بر اساس گزارش 1007
         $machine_efficiency = Report1007Controller::getMachineEfficiency($start_date_time, $end_date_time);

        // لیست بسته بندی هایی که کنترل کیفیت  شده اند
        $log_list = PackingFormLog::
        where("created_at", ">=", $start_date_time)->
        where("created_at", "<=", $end_date_time)->
        where("event_id", 7007037)-> // پایان کنترل کیفیت
        select(["event_id"])->
        selectRaw("id as packing_form_id,created_at as log_created_at")->
        selectRaw(Auth::id() . ' as  owner_user_id')->
        get()->
        toArray();
        Report1006ProductionForm::where("owner_user_id", Auth::id())->delete();
        Report1006ProductionForm::insert($log_list);

        // لیست فرم هایی که درجه بندی آنها حذف شده است
        // این تکه را نمی دانم برای چیست و حذف کردم
//        $log_list_for_delete = ProductionFormLog::
//        where( "created_at", ">=", $start_date_time )->
//        where( "created_at", "<=", $end_date_time )->
//        where( "event_id", 700213 )-> // حذف درجه بندی
//        select( [ "production_form_id", "event_id" ] )->
//        selectRaw( "created_at as log_created_at" )->
//        get();
//        foreach ( $log_list_for_delete as $item ) {
//
//            Report1006ProductionForm::
//            where( "production_form_id", $item->production_form_id )->
//            where( "log_created_at", "<", $item->log_created_at )->
//            delete();
//        }

        // مجموع تولید
        $sum_all_list = QualityControlPackingForm::
        join("packing_form_item", "packing_form_item.packing_form_id", "quality_control_packing_forms.packing_form_id")->

        selectRaw("round( sum(packing_form_item.amount),2) as sum_amount,round(sum(packing_form_item.amount_after_control),2) as sum_amount_after_control,count(DISTINCT  packing_form_item.packing_form_id) as count_packing_forms ")->

        where("quality_control_packing_forms.created_at", ">=", $start_date_time)->
        where("quality_control_packing_forms.created_at", "<=", $end_date_time)->
        first();

        $list_line = QualityControlPackingForm::
        join("packing_form_item", "packing_form_item.packing_form_id", "quality_control_packing_forms.packing_form_id")->

        selectRaw('DATE(quality_control_packing_forms.created_at) as date ,round( sum(packing_form_item.amount),2) as amount ,round( sum(packing_form_item.amount_after_control),2) as amount_after_control')->
        where("quality_control_packing_forms.created_at", ">=", $start_date_time)->
        where("quality_control_packing_forms.created_at", "<=", $end_date_time)->
        groupBy("date")->
        orderBy("date")->
        get();

        // اضافه کردن روزهای تعطیل با مقدار صفر
        $list_line = collect($list_line)->
        keyBy('date');
        $diff_day = $start_date_time->diff($end_date_time)->days;
        for ($i = 0; $i <= $diff_day; $i++) {

            $string_date = $start_date_time->addDay()->format("Y-m-d");
            if (!isset($list_line[$string_date])) {
                $list_line[$string_date] = ["date" => $string_date, "amount" => 0, "amount_after_control" => 0];
            }
        }

        $list_line_report["amount"] = ["name" => "مقدار سیستم", "data" => []];
        $list_line_report["amount_after_control"] = ["name" => "مقدار کنترل کیفیت", "data" => []];

        $list_line = $list_line->sortBy("date");
        foreach ($list_line as $item) {

            $list_line_report["amount"]["data"][] = [
                "caption" => jdate(Carbon::parse($item["date"])->timestamp)->format('Y/m/d'),
                "value" => $item["amount"]
            ];
            $list_line_report["amount_after_control"]["data"][] = [
                "caption" => $item["date"],
                "value" => $item["amount_after_control"]
            ];
        }


       // $start_date_time = DateTime::getDateTimeFromRequest($request, "start_date");
        //$end_date_time = DateTime::getDateTimeFromRequest($request, "end_date");
        if ($request->start_date && $request->end_date) {

            $start_date_time = DateTime::getDateTimeFromRequest($request, "start_date");
            $end_date_time = DateTime::getDateTimeFromRequest($request, "end_date");
            session(["request_1006" => $request->all()]);
        }
        else if (  $request_sesssion) {
            // $request = $request_sesssion;
            $start_date_time=Carbon::parse($request_sesssion["start_date"]);
            $end_date_time=Carbon::parse($request_sesssion["end_date"]);
        }

        $product_pie_chart = QualityControlPackingForm::
        join("products", "products.id", "product_id")->

        selectRaw("concat( products.code, '- ',products.caption) as caption, sum(amount_after_control) as value")->

        where("quality_control_packing_forms.created_at", ">=", $start_date_time)->
        where("quality_control_packing_forms.created_at", "<=", $end_date_time)->

        groupBy("product_id")->
        get();


        $degree_pie_chart = QualityControlPackingForm::
        join("degrees", "degrees.id", "degree_id")->
        groupBy("degree_id")->
        selectRaw("concat( degrees.code, '- ',degrees.caption) as caption, sum(amount_after_control) as value")->

        where("quality_control_packing_forms.created_at", ">=", $start_date_time)->
        where("quality_control_packing_forms.created_at", "<=", $end_date_time)->
        get();

        $degree_list = QualityControlPackingForm::
        join("degrees", "degrees.id", "degree_id")->
        join("products", "products.id", "product_id")->
        groupBy("product_id", "degree_id")->
        orderBy("degree_id")->
        selectRaw("concat( products.code, '- ',products.caption) as product_caption, products.id as product_id,degree_id, concat(degrees.code,'- ',degrees.caption) as code, sum(amount_after_control) as value")->


        where("quality_control_packing_forms.created_at", ">=", $start_date_time)->
        where("quality_control_packing_forms.created_at", "<=", $end_date_time)->

        get();

        $degree_pie_charts = [];
        $degree_ids = [];
        foreach ($degree_list as $item) {
            $degree_pie_charts[$item->product_id]["title"] = $item->product_caption;
            $degree_pie_charts[$item->product_id]["product_id"] = $item->product_id;
            $degree_pie_charts[$item->product_id]["data"][$item->degree_id] = [
                "caption" => $item->code,
                "value" => $item->value
            ];
            $degree_pie_charts[$item->product_id]["sum"] = (
                isset($degree_pie_charts[$item->product_id]["sum"]) ? $degree_pie_charts[$item->product_id]["sum"] : 0
                ) + $item->value;

            $degree_ids[$item->degree_id] = $item->degree_id;
        }
        $degree_header_list = Degree::where("goods_kind_id", 4)->whereIn("id", $degree_ids)->orderBy("code")->get();


// product -machine tab جدول ماشین - کالا
        $machine_list = QualityControlPackingForm::
        join("production_forms", "production_forms.id", "production_form_id")->
        join("degrees", "degrees.id", "degree_id")->
        join("machines", "machines.id", "machine_id")->
        groupBy("machine_id", "degree_id")->
        selectRaw("concat( machines.code, '- ',machines.caption) as machine_caption,machine_id,degree_id, sum(amount_after_control) as value")->get();

        $machine_table_list = [];
        foreach ($machine_list as $item) {
            $machine_table_list[$item->machine_id]["machine_id"] = $item->machine_id;
            $machine_table_list[$item->machine_id]["title"] = $item->machine_caption;
            $machine_table_list[$item->machine_id]["data"][$item->degree_id] = ["value" => $item->value];
            $machine_table_list[$item->machine_id]["sum"] = (
                isset($machine_table_list[$item->machine_id]["sum"]) ? $machine_table_list[$item->machine_id]["sum"] : 0
                ) + $item->value;
        }


        // کنترل کیفی
        $quality_control_product_faults = QualityControlProductFault::
        where("created_at", ">=", $start_date_time)->
        where("created_at", "<=", $end_date_time)->
            whereNull("parent_quality_control_product_fault_id")->
            orderBy("created_at","desc")->
        paginate(30);

        $product_fault_properties=ProductFaultProperties::get();




        return view("report.1006.show_report",
            compact(
                "sum_all_list",
                "list_line_report",
                "product_pie_chart",
                "degree_pie_chart",
                "degree_pie_charts",
                "degree_header_list",
                "machine_table_list",
                "machine_efficiency",
                "quality_control_product_faults",
                "product_fault_properties",
            ));
    }
}
