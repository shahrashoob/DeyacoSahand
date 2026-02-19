<?php

namespace App\Http\Controllers\HR\Personal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\WorkerController;
use App\Http\Controllers\Utility\Script\Script1023Controller;
use App\Models\HR\LeaveOvertime\LeaveRemainder;
use App\Models\Post\PostUser;
use App\Models\HR\Shift\DailyShiftOperation;
use App\Models\HR\Shift\ShiftWorkDay;
use App\Models\HR\User\UserEntryLog;
use App\Models\HR\User\UserOperation;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;

class ShiftWorkDayController extends Controller
{
    //
    var $view_path = "hr.personal.shift_work_day.";
    var $route_path = "hr.personal.shift_work_day.";
    var $dashboard_route = "hr.personal.shift_work_day";

    public function index(Worker $worker, $year = 0, $month = -1)
    {
        $result = self::AllowUserShow($worker);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $result_info = self::GetList($worker, $year, $month);
        $caption = $result_info["caption"];
        $list = $result_info["list"];
        $operation_list = $result_info["operation_list"];
        $year = $result_info["year"];
        $month = $result_info["month"];

        $app_debug=Setting::getIntegerValue("app_debug");
        // نمایش مانده مرخصی
        $leave_reminder = LeaveRemainder::getLeaveReminder($worker, 'hours_all', $year);
        return view($this->view_path . "index", compact("app_debug","list", "year", "month", "caption", "worker", "operation_list", "leave_reminder"));

    }

    public static function GetList(Worker $worker, $year = 0, $month = -1, $has_operation_list_entry_log = false)
    {
        if ($year == 0) {
            $year = jdate(Carbon::parse(Carbon::now())->timestamp)->format('Y');
        }


        if ($month == -1) {
            $month = jdate(Carbon::parse(Carbon::now())->timestamp)->format('m');
        }


        $datetime = \Morilog\Jalali\CalendarUtils:: toGregorian($year, $month, 1);
        $caption = jdate(Carbon::parse($datetime[0] . "/" . $datetime[1] . "/" . $datetime[2])->timestamp)->format('%B %Y ');

        $shift_work_query = ShiftWorkDayController::GetDateWorkQuery($worker, $year, $month);
        $list = $shift_work_query->orderBy("datetime")->get();


        $monthDays = jdate(Carbon::parse($datetime[0] . "/" . $datetime[1] . "/" . $datetime[2])->timestamp)->getMonthDays();

        $start_date = \Morilog\Jalali\CalendarUtils:: toGregorian($year, $month, 1);
        $start_date = Carbon::parse($start_date[0] . "/" . $start_date[1] . "/" . $start_date[2]);

        $end_date = \Morilog\Jalali\CalendarUtils:: toGregorian($year, $month, $monthDays);
        $end_date = Carbon::parse($end_date[0] . "/" . $end_date[1] . "/" . $end_date[2]);

        $operation_list = UserOperation::where("user_id", $worker->id)->
        where("current_date", ">=", $start_date)->
        where("current_date", "<=", $end_date)->
        orderBy("current_date")->get();

        $operation_list_entry_log = [];
        if ($has_operation_list_entry_log) {
            foreach ($operation_list as $operation) {
                $next_date = Carbon::parse($operation->current_date)->addDay();
                $operation_list_entry_log[$operation->id] = UserEntryLog::GetUserEntryForDay($worker, $operation->current_date, $next_date);
            }
        }

        return [
            "operation_list" => $operation_list,
            "list" => $list,
            "caption" => $caption,
            "year" => $year,
            "month" => $month,
            "operation_list_entry_log" => $operation_list_entry_log
        ];
    }

    public static function GetDateWorkQuery($worker, $year, $month)
    {

        $shift_work_ids = PostUser::join("posts", "posts.id", "post_id")->
        where("user_id", $worker->id)->
        select("shift_work_id", "shift_id")->
        get();
        if ($year != 0) {
            $datetime = \Morilog\Jalali\CalendarUtils:: toGregorian($year, $month, 1);
            $monthDays = jdate(Carbon::parse($datetime[0] . "/" . $datetime[1] . "/" . $datetime[2])->timestamp)->getMonthDays();

            $start_date = \Morilog\Jalali\CalendarUtils:: toGregorian($year, $month, 1);
            $end_date = \Morilog\Jalali\CalendarUtils:: toGregorian($year, $month, $monthDays);
            $end_date = Carbon::parse($end_date[0] . "/" . $end_date[1] . "/" . $end_date[2])->addDay();

            $shift_work_query = ShiftWorkDay::
            where("start_datetime", ">=", $start_date[0] . "/" . $start_date[1] . "/" . $start_date[2])->
            where("end_datetime", "<", $end_date);
        } else {
            $shift_work_query = ShiftWorkDay::where("id", ">", 0);
        }
        $shift_work_query->Where(function ($query) use ($shift_work_ids) {
            if (count($shift_work_ids) == 0) {
                return $query->where("id", "<", 0);
            }
            foreach ($shift_work_ids as $item) {
                $shift_id = $item->shift_id;
                $shift_work_id = $item->shift_work_id;

                $query->orWhere(function ($query) use ($shift_id, $shift_work_id) {
                    return $query->where("shift_id", $shift_id)->
                    where("shift_work_id", $shift_work_id);

                });
            }

            return $query;
        }
        );
        $shift_work_query->select("shift_work_days.*")->selectRaw("Date(start_datetime) as datetime");

        return $shift_work_query;

    }

    public function show_entry_log_for_day(Worker $worker, UserOperation $user_operation)
    {
        $result = self::AllowUserShow($worker);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if ($worker->id != $user_operation->user_id) {
            return back()->withErrors("صفحه مورد نظر یافت نشد.");
        }
        $next_date = Carbon::parse($user_operation->current_date)->addDay();
        $persian_date = jdate(Carbon::parse($user_operation->current_date)->timestamp)->format("Y/m/d");
        $entry_log_list = UserEntryLog::GetUserEntryForDay($worker, $user_operation->current_date, $next_date);
        return view($this->view_path . "show_entry_log_for_day", compact("worker", "entry_log_list", "persian_date"));
    }

    public function show_entry_log_calc(Worker $worker, UserOperation $user_operation)
    {
        $result = self::AllowUserShow($worker);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $date = $user_operation->current_date;
        $operation = Script1023Controller::calculate($worker, $date);
        $result = DailyShiftOperation::CalculateForDay($worker, $date);
        $intervals = $result["intervals"];
        return view("hr.personal.shift_work_day.calculator_log", compact("intervals", "worker", "date", "operation"));

    }

    public static function download_operation_list(Worker $worker, $year = 0, $month = -1)
    {
        $result = self::create_pdf_file($worker, $year, $month, "download");
        Pdf::createAsHtml($result["html"],
            "L",
            $worker->id, "A4", " "
        );
    }

    public static function create_pdf_file(Worker $worker, $year = 0, $month = -1, $type = "download")
    {

        $result = self::AllowUserShow($worker);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $result_info = self::GetList($worker, $year, $month, true);
        $caption = $result_info["caption"];
        $operation_list = $result_info["operation_list"];
        $operation_list_entry_log = $result_info["operation_list_entry_log"];
        $software_name = Setting::getStringValue("software_name");
        $view_path = "hr.personal.print.operation_list.";
        $html[0] = view($view_path . "_print_info", compact("worker", "caption", "operation_list", "software_name", "operation_list_entry_log"))->render();
        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $worker->id . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => 0,
                "printer_id" => $worker->default_printer_id
            ]);
        }
        return ["html" => $html, "print_file" => $print_file];
    }

    public static function AllowUserShow($worker)
    {
        $worker_auth_id = Auth::id();
        if ($worker_auth_id == $worker->id) {
            return [
                "result" => true
            ];
        }
        $post_user = Auth::user()->posts->first();

        if (!WorkerController::AllowUserShow($post_user, $worker->id)) {

            return [
                "result" => false,
                "error" => "شما به عملیات مورد نظر دسترسی ندارید"
            ];
        }
        return [
            "result" => true
        ];
    }
}
