<?php

namespace App\Models\HR\LeaveOvertime;

use App\Http\Controllers\HR\Personal\ShiftWorkDayController;
use App\Models\HR\User\UserEntryLog;
use App\Models\HR\User\UserOperation;
use App\Models\Post\Post;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function jdate;

class LeaveRemainder extends Model
{
    use HasFactory;

    protected $table = "leave_remainders";
    protected $fillable = [
        "user_id",
        "leave_type_id",
        "start_date",
        "end_date",
        "leave_in_start",
        "leave_in_end",
        "leave_remainder",
        "year"
    ];

    public function start_date()
    {
        return jdate(Carbon::parse($this->start_date)->timestamp)->format('Y/m/d');

    }

    public function end_date()
    {
        return jdate(Carbon::parse($this->end_date)->timestamp)->format('Y/m/d');

    }

    public static function getLeaveReminder(Worker $worker, $type = "hours_1", $year = null)
    {
        $leave_reminder = self::where("user_id", $worker->id)->
        when($year, function ($query) use ($year) {
            return $query->where("year", $year);
        })->
        when(!$year, function ($query) use ($year) {
            return $query->
            where("start_date", "<=", Carbon::now())->
            where("end_date", ">=", Carbon::now());
        })->
        first();
        if (!$leave_reminder) {

            // اگر جدول مانده مرخصی ها وجود ندارد، مانده مرخصی را برابر می کند با min (اولین حضور فرد و سال جاری یا پایان آخرین بخشی که براش بازه مرخصی ثبت کردیم هر کدام زودتر بود)
            // آخرین زمانی که ثبت شده
            $start_datetime = Carbon::now();

            if(in_array($worker->status_id , [4620007,4620014,4620012]) ){
                switch ($type) {
                    case "hours_1":
                        return "غیرقابل محاسبه ";
                    case "hours_all":
                        return null;
                }
            }
            // اولین ورود فرد
            $first_user_entry = UserEntryLog::where("user_id", $worker->id)->orderBy("entry_datetime")->first();
            if ($first_user_entry) {
                $start_datetime = $start_datetime->greaterThan(Carbon::parse($first_user_entry->entry_datetime)) ? Carbon::parse($first_user_entry->entry_datetime) : $start_datetime;
            }else{
                switch ($type) {
                    case "hours_1":
                        return "غیرقابل محاسبه ";
                    case "hours_all":
                        return null;
                }
            }
            // آخرین زمانی که دوره مرخصی وجود داشته
            $leave_reminder = self::where("user_id", $worker->id)->orderByDesc("start_date")->first();
            if ($leave_reminder) {
                $start_datetime = $start_datetime->greaterThan(Carbon::parse($leave_reminder->end_date)) ? Carbon::parse($leave_reminder->end_date) : $start_datetime;
            }

            $year = jdate(Carbon::parse(Carbon::now())->timestamp)->format('Y');
            $month = jdate(Carbon::parse(Carbon::now())->timestamp)->format('m');

            $datetime_day_years_g = \Morilog\Jalali\CalendarUtils:: toGregorian($year, $month, 1);
            $datetime_day_years = Carbon::parse($datetime_day_years_g[0] . "/" . $datetime_day_years_g[1] . "/" . $datetime_day_years_g[2]);

            $start_datetime = $start_datetime->greaterThan($datetime_day_years) ? $datetime_day_years : $start_datetime;


            $datetime_day_years_end_g = \Morilog\Jalali\CalendarUtils:: toGregorian($year, 12, 29);
            $datetime_day_years_end = Carbon::parse($datetime_day_years_end_g[0] . "/" . $datetime_day_years_end_g[1] . "/" . $datetime_day_years_end_g[2]);

            LeaveRemainder::create([
                "user_id" => $worker->id,
                "leave_type_id" => 1,
                "start_date" => $start_datetime->format('Y-m-d'),
                "end_date" => $datetime_day_years_end->format('Y-m-d'),
                "leave_in_start" => 0,
                "leave_in_end" => 0,
                "leave_remainder" => 0,
                "year" => $year,
            ]);
            $leave_reminder = self::where("user_id", $worker->id)->
            when($year, function ($query) use ($year) {
                return $query->where("year", $year);
            })->
            when(!$year, function ($query) use ($year) {
                return $query->
                where("start_date", "<=", Carbon::now())->
                where("end_date", ">=", Carbon::now());
            })->
            first();
            self::UpdateLeaveReminder($worker);
            switch ($type) {
                case "hours_1":
                    return "غیرقابل محاسبه ";
                case "hours_all":
                    return null;
            }
        }
        switch ($type) {
            case "hours_1":
                $minutes = abs($leave_reminder->leave_remainder);
                $hours = floor($minutes / 60);
                $min = $minutes - ($hours * 60);
                return ($leave_reminder->leave_remainder < 0 ? "-" : "") . $hours . ":" . $min . " ساعت";
            case "hours_all":
                // leave_remainder
                $minutes = abs($leave_reminder->leave_remainder);
                $hours = floor($minutes / 60);
                $min = $minutes - ($hours * 60);
                $leave_reminder->leave_remainder = ($leave_reminder->leave_remainder < 0 ? "-" : "") . $hours . ":" . $min . " ساعت";
                // leave_in_start
                $minutes = abs($leave_reminder->leave_in_start);
                $hours = floor($minutes / 60);
                $min = $minutes - ($hours * 60);
                $leave_reminder->leave_in_start = ($leave_reminder->leave_in_start < 0 ? "-" : "") . $hours . ":" . $min . " ساعت";
                // leave_in_end
                $minutes = abs($leave_reminder->leave_in_end);
                $hours = floor($minutes / 60);
                $min = $minutes - ($hours * 60);
                $leave_reminder->leave_in_end = ($leave_reminder->leave_in_end < 0 ? "-" : "") . $hours . ":" . $min . " ساعت";
                return $leave_reminder;
        }
        return "---";
    }


//    public static function createFirstLeaveReminder(Worker $worker)
//    {
//
//        $start_date=
//        $live_reminder =
//        [
//            "user_id"=>$worker->id,
//            "leave_type_id"=>1,
//            "start_date",
//            "end_date",
//            "leave_in_start",
//            "leave_in_end",
//            "leave_remainder",
//            "year"
//        ];
//
//    }
    public static function UpdateLeaveReminder(Worker $worker, $leave_type_id = 1, $current_date = null)
    {
        $current_date = $current_date ?? Carbon::now();

        $leave_reminder = self::where("user_id", $worker->id)->
        where("start_date", "<=", $current_date)->
        where("end_date", ">=", $current_date)->
        first();
        if (!$leave_reminder) {
            return [
                "result" => false,
                "message" => "ردیف مانده مرخصی یافت نشد."
            ];
        }

        $legal_leave = UserOperation::where("user_id", $worker->id)->
        // where("leave_type_id",$leave_type_id)->
        where("current_date", ">=", $leave_reminder->start_date)->
        where("current_date", "<=", $leave_reminder->end_date)->
        sum("legal_leave");


        $diff_current_with_start_in_month = $current_date->diffInMonths(Carbon::parse($leave_reminder->start_date)) + 1;
        $legal_leave_in_minutes = Setting::getIntegerValue("legal_leave_in_month");

        // مرخصی باقی مانده = مرخصی ابتدای دوره + تعداد ماه سپری شده * مرخصی هر ماه - مرخصی انجام شده
        $leave_reminder_in_minute = $leave_reminder->leave_in_start +
            $diff_current_with_start_in_month * $legal_leave_in_minutes -
            $legal_leave / 60;

        $leave_reminder->leave_remainder = $leave_reminder_in_minute;
        $leave_reminder->save();
        return [
            "result" => true,
            "leave_reminder" => $leave_reminder,
            "diff_current_with_start_in_month" => $diff_current_with_start_in_month,
            "legal_leave_in_minutes" => $legal_leave_in_minutes,
            "legal_leave" => $legal_leave,
        ];

    }

}
