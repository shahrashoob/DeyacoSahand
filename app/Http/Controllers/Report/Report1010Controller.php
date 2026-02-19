<?php

namespace App\Http\Controllers\Report;

use App\Exports\Report\Report1010_1Export;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Report\EfficiencyBasedOnMachineLog;
use App\Models\Report\EfficiencyBasedOnMachineLogM;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class Report1010Controller extends Controller
{
    // گزارش بهره وری
    var $view_path = "report.1010.";
    var $route_path = "report.1010.";
    public static $route_path_static = "report.1010.";
public static $dayBreak=60;
    public function index()
    {

    }

    public function submit()
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

        $start_date_minute = Carbon::parse($cross_sectional_management_request["start_date"])->format("YmdHi");
        $end_date_minute = Carbon::parse($cross_sectional_management_request["end_date"])->format("YmdHi");

        $goods_kind_id = $cross_sectional_management_request["goods_kind_id"];

        $machine_bar_chart = Report1010Controller::getReportForCrossSection($start_date_time, $end_date_time, $start_date_minute, $end_date_minute, $goods_kind_id);
        $user_bar_chart = Report1010Controller::getReportUserBarChart($start_date_time, $end_date_time, $start_date_minute, $end_date_minute, $goods_kind_id);

        return view($this->view_path . "show_report",
            compact(
                "machine_bar_chart", "user_bar_chart",
                "goods_kind_id", "start_date_time", "end_date_time"
            ));
    }


    public static function getReportForCrossSection($start_date_time, $end_date_time, $start_date_minute, $end_date_minute, $goods_kind_id)
    {

        if (!Report1010Controller::checkPermission()) {
            return false;
        }

        $EfficiencyBasedOnMachineLogTable=EfficiencyBasedOnMachineLogM::class;
        $table_name="efficiency_based_on_machine_logs_m";
        if($start_date_time->LessThan(Carbon::now()->addDay(-self::$dayBreak))){
            $EfficiencyBasedOnMachineLogTable=EfficiencyBasedOnMachineLog::class;
            $table_name="efficiency_based_on_machine_logs";
        }

        $time_in_seconds = $end_date_time->diffInSeconds($start_date_time);
        $report_list = $EfficiencyBasedOnMachineLogTable::
        join("products", "products.id", "$table_name.product_id")->
        join("machine_allocation", "machine_allocation.id", "machine_allocation_id")->
        where("created_minute_number", ">", $start_date_minute)->
        where("created_minute_number", "<=", $end_date_minute)->
        where("goods_kind_id", $goods_kind_id)->
        where("band_code", 1)-> // برای اینکه ماشین های دویا چند بانده تعداد کنتور آنها دوبرابر نمایش ندهد، فقط باند یک را بر می گردانیم.
        selectRaw(
            "machine_allocation.machine_id,sum(operation_contour) as operation_contour"
        )->
        groupBy("$table_name.machine_id")->
        pluck("operation_contour", "machine_id")->
        toArray();

        $machine_ids = array_keys($report_list);
        $machine_ids[] = -1;
        $machine_list = Machine::with("machine_type")->
        whereIn("id", $machine_ids)->
        orderBy("station_id")->
        orderBy("number_code")->
        get()->keyBy("id");

        foreach ($machine_list as &$machine) {
            $machine->theory_contour = $machine->machine_type->number_of_contour_in_minute * $time_in_seconds / 60;
            $machine->operation_contour=$report_list[$machine->id];
            if ($machine->theory_contour <= 0) {
                $machine->value = 0;
            } else {
                $machine->value =round(  $machine->operation_contour / $machine->theory_contour,2)*100;
            }


        }

        return $machine_list;
    }

    public static function getReportUserBarChart($start_date_time, $end_date_time, $start_date_minute, $end_date_minute, $goods_kind_id)
    {

        if (!Report1010Controller::checkPermission()) {
            return false;
        }
      //  $time_in_seconds = $end_date_time->diffInSeconds($start_date_time);
        $EfficiencyBasedOnMachineLogTable=EfficiencyBasedOnMachineLogM::class;
        $table_name="efficiency_based_on_machine_logs_m";
        if($start_date_time->LessThan(Carbon::now()->addDay(-self::$dayBreak))){
            $EfficiencyBasedOnMachineLogTable=EfficiencyBasedOnMachineLog::class;
            $table_name="efficiency_based_on_machine_logs";
        }
        return $EfficiencyBasedOnMachineLogTable::
        join("users", "users.id", "operator_id")->
        join("machines", "machines.id", "$table_name.machine_id")->
        join("machine_types", "machine_types.id", "machine_type_id")->
        join("products", "products.id", "product_id")->
        where("created_minute_number", ">", $start_date_minute)->
        where("created_minute_number", "<=", $end_date_minute)->
        where("goods_kind_id", $goods_kind_id)->
        selectRaw("concat(firstname,' ',lastname) as caption, round(sum(operation_contour)/sum(number_of_contour_in_minute * time_in_seconds/60 )*100) as value ")->
        groupBy("operator_id")->
        orderBy("operator_id")->get();
    }

    public static function checkPermission()
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission(Report1010Controller::$route_path_static . "index")) {
            return false;
        }

        return true;
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
        $end_date_minute = $end_date_time->format("YmdHi");

        $goods_kind_id = $cross_sectional_management_request["goods_kind_id"];

         $list = Report1010Controller::getReportForCrossSection($start_date_time, $end_date_time, $start_date_minute, $end_date_minute, $goods_kind_id);


        $export = new Report1010_1Export();
        $export->list = $list;

        return Excel::download($export, 'contractor_report_1_' . jdate(Carbon::now()->timestamp)->format('Y_m_d') . '.xlsx');


    }
}
