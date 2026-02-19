<?php

namespace App\Http\Controllers\Report;

use App\Exports\Report\Report1014_1Export;
use App\Http\Controllers\Controller;
use App\Models\HR\User\UserOperation;
use App\Models\Utility\Option;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class Report1014Controller extends Controller
{
    // گزارش فایل حضور و غیاب افراد (گزارش عملکرد پرسنل)
    var $view_path = "report.1014.";
    var $route_path = "report.1014.";
    var $dashboard_path = "report.1014.";
    public static $route_path_static = "report.1014.";

    public function index()
    {

        $worker_option = Option::get("worker", 0, 1);

        return view($this->view_path . "index", compact("worker_option"));
    }

    public function submit(Request $request)
    {
return $this->submit_sum($request);
        $start_date_time = Carbon::parse($request->start_date);
        $end_date_time = Carbon::parse($request->end_date);
        $from_user_id = $request->from_user_id ?? 0;
        $to_user_id = $request->to_user_id ?? 0;
        $operation_list = UserOperation::
        where("current_date", ">=", $start_date_time)->
        where("current_date", "<", $end_date_time)->
        when($from_user_id, function ($query) use ($from_user_id) {
            return $query->where("user_id", ">=", $from_user_id);
        })->
        when($to_user_id, function ($query) use ($to_user_id) {
            return $query->where("user_id", "<=", $to_user_id);
        })->
        orderBy("user_id")->
        orderBy("current_date")->
        get();

        $export = new Report1014_1Export();
        $export->operation_list = $operation_list;

        return Excel::download($export, 'report_1014' . "_" . jdate(Carbon::now()->timestamp)->format('Y_m_d') . '.xlsx');

    }


    public function submit_sum(Request $request)
    {

        $start_date_time = Carbon::parse($request->start_date);
        $end_date_time = Carbon::parse($request->end_date);
        $from_user_id = $request->from_user_id ?? 0;
        $to_user_id = $request->to_user_id ?? 0;
        $user_ids = Worker::
        whereIn("cooperation_type_id",[ 1,11])->
        when($from_user_id, function ($query) use ($from_user_id) {
            return $query->where("id", ">=", $from_user_id);
        })->
        when($to_user_id, function ($query) use ($to_user_id) {
            return $query->where("id", "<=", $to_user_id);
        })->
        pluck("id","id")->toArray();

        $operation_list = UserOperation::
        where("current_date", ">=", $start_date_time)->
        where("current_date", "<=", $end_date_time)->
        whereIn("user_id", $user_ids)->
        groupBy("user_id")->
        orderBy("user_id")->
        orderBy("current_date")->
        selectRaw(
            "user_id,
                sum(present_in_organ) as present_in_organ,
                sum(not_allowed_present_in_organ) as not_allowed_present_in_organ,
                sum(allowed_present_in_organ) as allowed_present_in_organ,
                sum(allowed_operation) as allowed_operation,
                sum(normal_operation) as normal_operation,
                sum(morning) as morning,
                sum(afternoon) as afternoon,
                sum(night) as night,
                sum(internal_leave) as internal_leave,
                sum(legal_leave) as legal_leave,
                sum(internal_absence) as internal_absence,
                sum(legal_absence) as legal_absence,
                sum(overtime) as overtime,
                sum(mission) as mission,
                sum(allowed_earlier_time_for_entry) as allowed_earlier_time_for_entry,
                sum(allowed_delay_time_for_entry) as allowed_delay_time_for_entry,
                sum(allowed_earlier_time_for_exit) as allowed_earlier_time_for_exit,
                sum(allowed_delay_time_for_exit)as allowed_delay_time_for_exit"
        )->
        get();

        $export = new Report1014_1Export();
        $export->operation_list = $operation_list;
        $export->template = "_sum";

        return Excel::download($export, 'report_1014' . "_" . jdate(Carbon::now()->timestamp)->format('Y_m_d') . '.xlsx');

    }

}
