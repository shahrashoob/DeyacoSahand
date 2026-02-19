<?php

namespace App\Models\HR\Shift;

use App\Http\Controllers\HR\Personal\ShiftWorkDayController;
use App\Models\Post\PostUser;
use App\Models\User;
use App\Models\HR\LeaveOvertime\LeaveOvertime;
use App\Models\HR\User\UserEntryLog;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyShiftOperation
{
    var $start; // timestamp
    var $end; // timestamp
    var $start_datetime;
    var $end_datetime;
    var $master_interval;
    var $namber;
    var $split_shift_type_group_id; // گروه شیفت: صبح، ظهر، شب
    var $shift_work_day_id; // کد پاره وقتی شیفت
    var $min; // حداقل مقدار// timestamp
    var $max; // حداکثر مثدار// timestamp
    var $allowed_earlier_time_for_entry; //تعجیل مجاز برای ورود به سازمان (شناسه پست)
    var $allowed_delay_time_for_entry; //تاخیر مجاز برای ورود به سازمان (شناسه پست)
    var $allowed_earlier_time_for_exit; // تعجیل مجاز برای خروج به سازمان (شناسه پست)
    var $allowed_delay_time_for_exit; //تاخیر مجاز برای خروج به سازمان (شناسه پست)

    var $leave_id;
    var $is_for_leave;
    var $overtime_id;
    var $mission_id;
    var $replacement_id;
    //var $leave
    var $present_in_organ;

    var $present_in_organ_for_other;

    var $legal_working_hours_in_minute;

    var $is_legal_operation_hours; // آیا ساعت کار قانونی است یا خیر

    public function __construct($start, $end, $number = 0, $time_interval_copy = null, $master_interval = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->number = $number;
        $this->master_interval = $master_interval;
        if ($time_interval_copy) {
            $this->copyData($time_interval_copy);
        }
    }

    public function data()
    {
        return [
            "start" => $this->start,
            "end" => $this->end,
        ];
    }

    public function start_time()
    {
        return jdate($this->start)->format('H:i:s');

    }

    public function end_time()
    {
        return jdate($this->end)->format('H:i:s');

    }

    public function diff_in_second()
    {
        return $this->end - $this->start;
    }

    public function copyData(DailyShiftOperation $daily_shift_operation)
    {
        $list = [
            "split_shift_type_group_id",
            "shift_work_day_id",
            "min",
            "max",
            "allowed_earlier_time_for_entry",
            "allowed_delay_time_for_entry",
            "allowed_earlier_time_for_exit",
            "allowed_delay_time_for_exit",
            "leave_id",
            "overtime_id",
            'mission_id',
            "replacement_id",
            "present_in_organ",
            "present_in_organ_for_other",
            "legal_working_hours_in_minute",
            "is_for_leave",
            "is_legal_operation_hours"
        ];
        foreach ($list as $item) {
            if (!isset($this->$item)) {
                $this->$item = $daily_shift_operation->$item;
            }
        }
    }

    public function update_datetime()
    {
        $this->start_datetime = Carbon::createFromTimestamp($this->start)->toDateTimeString();
        $this->end_datetime = Carbon::createFromTimestamp($this->end)->toDateTimeString();
    }

    public static function BreakIntervals($intervals, DailyShiftOperation $new_time_interval)
    {

        $new_time_interval->start = round($new_time_interval->start / 60) * 60;
        $new_time_interval->end = round($new_time_interval->end / 60) * 60;

        $start_number = max(array_keys($intervals)) + 1;

        $start = $new_time_interval->start;
        $end = $new_time_interval->end;
        if ($start < $intervals[0]->min) {
            $start = $intervals[0]->min;
        }
        if ($start > $intervals[0]->max) {
            $start = $intervals[0]->max;
        }


        if ($end > $intervals[0]->max) {
            $end = $intervals[0]->max;

        }
        if ($end < $intervals[0]->min) {
            $end = $intervals[0]->min;
        }
        $new_time_interval->start = $start;
        $new_time_interval->end = $end;

        $within_interval = false;
        $new_intervals = $intervals;
        // بررسی اینکه داخل یک بازه است یا خیر
        foreach ($intervals as $number => $interval) {


            // بازه هایی که حذف می شوند را دیگر استفاده نمی کنیم.
            if (isset($interval->removed)) {
                continue;
            }

            if ($start >= $interval->start && $end <= $interval->end) {
                $interval->removed = true;
                $within_interval = true;

                $intervals[$start_number++] = new DailyShiftOperation(
                    $interval->start,
                    $start,
                    $start_number - 1,
                    null,
                    $number
                );
                $intervals[$start_number - 1]->copyData($interval);

                $intervals[$start_number++] = new DailyShiftOperation(
                    $start,
                    $end,
                    $start_number - 1,
                    $new_time_interval,
                    $number
                );
                $intervals[$start_number - 1]->copyData($interval);

                $intervals[$start_number++] = new DailyShiftOperation(
                    $end,
                    $interval->end,
                    $start_number - 1,
                    null,
                    $number,
                );
                $intervals[$start_number - 1]->copyData($interval);

            }

        }


        if (!$within_interval) {

            $start_within_interval_number = -1; // بازه شروع داخل کدام بازه است
            $end_within_interval_number = -1; // بازه پایانی داخل کدام بازه است.
            foreach ($intervals as $number => $interval) {

                // بازه هایی که حذف می شوند را دیگر استفاده نمی کنیم.
                if (isset($interval->removed)) {
                    continue;
                }

                if ($start >= $interval->start && $start < $interval->end) {
//                      echo "start: $start >= $interval->start && $start < $interval->end=>$number"."<br/>";
                    $start_within_interval_number = $number;
                }
                if ($end >= $interval->start && $end <= $interval->end) {
//                      echo "end:$end >= $interval->start && $end < $interval->end=>$number"."<br/>";
                    $end_within_interval_number = $number;
                }
            }
//            if($new_time_interval->shift_work_day_id==12345){
//                return $new_time_interval->start_time();
//            }
            if ($start_within_interval_number >= 0 && $end_within_interval_number >= 0) {


                //شکستن بازه شروع به دو بازه
                $intervals[$start_within_interval_number]->removed = 1;

                $intervals[$start_number++] = new DailyShiftOperation(
                    $intervals[$start_within_interval_number]->start,
                    $start,
                    $start_number - 1,
                    null,
                    $start_within_interval_number
                );
                $intervals[$start_number - 1]->copyData($intervals[$start_within_interval_number]);

                $intervals[$start_number++] = new DailyShiftOperation(
                    $start,
                    $intervals[$start_within_interval_number]->end,
                    $start_number - 1,
                    $new_time_interval,
                    $start_within_interval_number
                );
                $intervals[$start_number - 1]->copyData($intervals[$start_within_interval_number]);

                //شکستن بازه پایانی به دو بازه

                $intervals[$end_within_interval_number]->removed = 1;

                $intervals[$start_number++] = new DailyShiftOperation(
                    $intervals[$end_within_interval_number]->start,
                    $end,
                    $start_number - 1,
                    $new_time_interval,
                    $end_within_interval_number
                );
                $intervals[$start_number - 1]->copyData($intervals[$end_within_interval_number]);

                $intervals[$start_number++] = new DailyShiftOperation(
                    $end,
                    $intervals[$end_within_interval_number]->end,
                    $start_number - 1,
                    null,
                    $end_within_interval_number
                );
                $intervals[$start_number - 1]->copyData($intervals[$end_within_interval_number]);


                // بازه هایی که داخل بازه میانی هستند را پیدا می کنیم و خصوصیات بازه جدید را در آنها کپی می کنیم.
                $interval_must_be_removed = [];
                $start_middle = $intervals[$start_within_interval_number]->end;
                $end_middle = $intervals[$end_within_interval_number]->start;

                foreach ($intervals as $interval) {

                    if ($start_middle <= $interval->start && $end_middle >= $interval->end) {
                        $interval->copyData($new_time_interval);
                    }
                }


            }
        }


        // بازه ها را به ترتیب شماره بازه شروع مرتب می کنیم و بازه هایی که باید حذف شوند را حذف می کنیم.
        $intervals = DailyShiftOperation::ClearInterval($intervals);


        return $intervals;
    }

    public static function ClearInterval($intervals)
    {
        $new_intervals = [];
        $k = 1;
        foreach ($intervals as $item) {
            if (isset($item->removed) || $item->end <= $item->start) {
                continue;
            }
            $new_intervals[$item->start] = $item;
        }
        ksort($new_intervals);
        $sort_interval = [];
        foreach ($new_intervals as $interval) {
            $interval->update_datetime();
            $sort_interval[] = $interval;
        }

        return $sort_interval;

    }

    public static function CalculateForDay(Worker $worker, $date)
    {

        // اضافه کردن اولین بازه، کل روز $data
        $start = Carbon::parse($date . " 00:00:00")->timestamp;
        $end = Carbon::parse($date . " 24:00:00")->timestamp;
        $interval = new DailyShiftOperation($start, $end);
        $interval->min = $start;
        $interval->max = $end;
        $intervals[] = $interval;

        // اضافه کردن بازه های حق شیفت
        foreach (SplitShiftType::all() as $split_shift_type) {
            $start = Carbon::parse($date . " " . $split_shift_type->start_time)->timestamp;
            $end = Carbon::parse($date . " " . $split_shift_type->end_time)->timestamp;
            $interval = new DailyShiftOperation($start, $end);
            $interval->split_shift_type_group_id = $split_shift_type->split_shift_type_group_id;

            $intervals = self::BreakIntervals($intervals, $interval);
        }


        // اضافه کردن بازه هایی شیف( بازهایی که طبق شیفت کاری باید در سازمان حضور داشته باشد)
        // اضافه کردن بازه هایی شیف( بازهایی که طبق شیفت کاری باید در سازمان حضور داشته باشد)
        $year = jdate($start)->format('Y');
        $month = jdate($start)->format('m');
        $shift_work_query = ShiftWorkDayController::GetDateWorkQuery($worker, $year, $month);
        $shift_work_day_list = $shift_work_query->orderBy("datetime")->
        where("datetime", $date)->
        orderBy("datetime")->
        get();

        $legal_working_hours_in_minute = -1;


        $legal_working_hours_in_minute_remaining = null;

        foreach ($shift_work_day_list as $shift_work_day) {

            $start = Carbon::parse($shift_work_day->start_datetime)->timestamp;
            $end = Carbon::parse($shift_work_day->end_datetime)->timestamp;
            $interval = new DailyShiftOperation($start, $end);
            $interval->shift_work_day_id = $shift_work_day->id;
            $interval->legal_working_hours_in_minute = $shift_work_day->legal_working_hours_in_minute;

            $intervals = self::BreakIntervals($intervals, $interval);
            if ($legal_working_hours_in_minute == -1) {
                $legal_working_hours_in_minute = $shift_work_day->legal_working_hours_in_minute;
                $legal_working_hours_in_minute_remaining = $legal_working_hours_in_minute;
            }
            if ($legal_working_hours_in_minute != $shift_work_day->legal_working_hours_in_minute) {
                $date_str = jdate(Carbon::parse($date)->timestamp)->format("Y/m/d");
                $full_name = $worker->fullname();
                return [
                    "result" => false,
                    "error" => " ساعت کار قانونی در بازه های مختلف $date_str برای $full_name متفاوت است.(" .
                        $legal_working_hours_in_minute . "," . $shift_work_day->legal_working_hours_in_minute . " دقیقه"
                        . ") "
                ];
            }
//            echo $shift_work_day->id."=>". Carbon::parse( $shift_work_day->start_datetime )->timestamp." - ".Carbon::parse( $shift_work_day->end_datetime )->timestamp."<br/>";
//          echo  $intervals[0]->max."<br/>";

        }
        foreach ($shift_work_day_list as $shift_work_day) {


            // محاسبه بازه های ساعت کار قانونی
            $minute_interval = Carbon::parse($shift_work_day->end_datetime)->diffInMinutes(Carbon::parse($shift_work_day->start_datetime));

            $minute_interval=min($minute_interval,$legal_working_hours_in_minute_remaining);

            if ($minute_interval > 0) {
                // اولین بازه را تا n ساعت به عنوان بازه کار قانونی در نظر می گیریم.
                $start = Carbon::parse($shift_work_day->start_datetime)->timestamp;
                $end = Carbon::parse($shift_work_day->start_datetime)->addMinute($minute_interval)->timestamp;
                $interval = new DailyShiftOperation($start, $end);
                $interval->is_legal_operation_hours = 1;
                $intervals = self::BreakIntervals($intervals, $interval);

                $legal_working_hours_in_minute_remaining -= $minute_interval;

            }
            //   return $intervals;
        }

        // اضافه کردن بازهای تاخیر و تعجیل های مجاز در ورود و خروج از سازمان

        $posts_by_shift_id_list = PostUser::join("posts", "posts.id", "post_id")->
        where("user_id", $worker->id)->
        select("posts.*")->
        get()->keyBy("shift_id");


        $first_shift_work_day = null;
        foreach ($shift_work_day_list as $shift_work_day) {
            $first_shift_work_day = $shift_work_day;
            $intervals = self::EarlierDelay($intervals, $legal_working_hours_in_minute, $posts_by_shift_id_list, $shift_work_day, $shift_work_day->start_datetime, $shift_work_day->end_datetime);
        }

        $next_date = Carbon::parse($date)->addDay();
        // وورد خروج
        $user_entry_log_list = UserEntryLog::GetUserEntryForDay($worker, $date, $next_date);


        // افزود بازه های مرخصی های تایید شده
        $leave_overtime_list = LeaveOvertime::where([
            "user_id" => $worker->id
        ])->
        whereIn("status_id", [4630003, 4630006, 4630007])->
        where(function ($query) use ($date, $next_date) {
            return $query->where(function ($query) use ($date, $next_date) {
                return $query->where("start_datetime", ">=", $date)->
                where("start_datetime", "<=", $next_date);
            })->
            orWhere(function ($query) use ($date, $next_date) {
                return $query->where("end_datetime", ">=", $date)->
                where("end_datetime", "<=", $next_date);
            })->
            orWhere(function ($query) use ($date, $next_date) {
                return $query->where("start_datetime", "<=", $date)->
                where("end_datetime", ">=", $next_date);
            });
        })->
        get();

        foreach ($leave_overtime_list as $leave_overtime) {

            $start = Carbon::parse($leave_overtime->start_datetime)->timestamp;
            $end = Carbon::parse($leave_overtime->end_datetime)->timestamp;
            $interval = new DailyShiftOperation($start, $end);

            switch ($leave_overtime->leave_overtime_type->leave_overtime_group_id) {
//
                case 1: // مرخصی
                    if(count($user_entry_log_list) > 0 ) {
                        // اگر فرد حداقل یک ورود و خروج در آن روز داشته است، بازه های تعجیل / تاخیر برای مرخصی را اضافه می کنیم، در غیر این صورت نیازی نیست که این کار را انجام دهیم.
                        $interval->leave_id = $leave_overtime->leave_overtime_type_id;
                        $other_variable = "leave_id";
                        $intervals = self::EarlierDelay($intervals, $legal_working_hours_in_minute, $posts_by_shift_id_list, $first_shift_work_day, $start, $end, $other_variable, true, $leave_overtime->leave_overtime_type_id);
                    }
                    break;
                case 2: // اضافه کاری
                    $interval->overtime_id = $leave_overtime->id;
                    $other_variable = "overtime_id";
                    $intervals = self::EarlierDelay($intervals, $legal_working_hours_in_minute, $posts_by_shift_id_list, $first_shift_work_day, $start, $end, $other_variable);

                    break;
                case 3: //ماموریت
                    $interval->mission_id = $leave_overtime->id;
                    $other_variable = "mission_id";
                    $intervals = self::EarlierDelay($intervals, $legal_working_hours_in_minute, $posts_by_shift_id_list, $first_shift_work_day, $start, $end, $other_variable);

                    break;

                case 4:// جابجایی
                    $interval->replacement_id = $leave_overtime->id;
                    $other_variable = "replacement_id";
                    $intervals = self::EarlierDelay($intervals, $legal_working_hours_in_minute, $posts_by_shift_id_list, $first_shift_work_day, $start, $end, $other_variable);

                    break;
            }
//return $intervals;
            $intervals = self::BreakIntervals($intervals, $interval);

        }

        // اگر فردی جانشین مرخصی فرد
        $leave_overtime_list = LeaveOvertime::
        join("leave_overtime_confirmation", "leave_overtime_id", "leave_overtimes.id")->
        where([
            "replace_user_id" => $worker->id
        ])->
        whereIn("leave_overtimes.status_id", [4630003, 4630006, 4630007])->
        where(function ($query) use ($date, $next_date) {
            return $query->where(function ($query) use ($date, $next_date) {
                return $query->where("start_datetime", ">=", $date)->
                where("start_datetime", "<=", $next_date);
            })->
            orWhere(function ($query) use ($date, $next_date) {
                return $query->where("end_datetime", ">=", $date)->
                where("end_datetime", "<=", $next_date);
            })->
            orWhere(function ($query) use ($date, $next_date) {
                return $query->where("start_datetime", "<=", $date)->
                where("end_datetime", ">=", $next_date);
            });
        })->
        select("leave_overtimes.*")->
        get();
        foreach ($leave_overtime_list as $leave_overtime) {

            $start = Carbon::parse($leave_overtime->start_datetime)->timestamp;
            $end = Carbon::parse($leave_overtime->end_datetime)->timestamp;
            $interval = new DailyShiftOperation($start, $end);
            $interval->present_in_organ_for_other = $leave_overtime->id;

            $intervals = self::BreakIntervals($intervals, $interval);
        }



        foreach ($user_entry_log_list as $user_entry) {
            $start = Carbon::parse($user_entry->entry_datetime)->timestamp;
            $end = Carbon::parse($user_entry->exit_datetime)->timestamp;
            $interval = new DailyShiftOperation($start, $end);
            $interval->present_in_organ = $user_entry->id;
            $intervals = self::BreakIntervals($intervals, $interval);
        }


        return [
            "result" => true,
            "intervals" => $intervals
        ];
    }

    public static function EarlierDelay($intervals, $legal_working_hours_in_minute, $posts_by_shift_id_list, $shift_work_day, $start_datetime, $end_datetime, $other_variable = null, $is_for_leave = false, $other_variable_value = 1)
    {
        // $is_for_leave = > آیا برای مرخصی ثبت می شود؟
        // اگر شیفت ورک مشخص نشده است، لازم نیست باز ها را محاسبه کند.
        if (!isset($shift_work_day)) {
            return $intervals;
        }
        // تعجیل مجاز برای ورود به سازمان (دقیقه)
        $allowed_earlier_time_for_entry = $posts_by_shift_id_list[$shift_work_day->shift_id]->allowed_earlier_time_for_entry;
        if ($allowed_earlier_time_for_entry > 0) {
            $start = Carbon::parse($start_datetime)->addMinute(-$allowed_earlier_time_for_entry)->timestamp;
            $end = Carbon::parse($start_datetime)->timestamp;
            $interval = new DailyShiftOperation($start, $end);
            $interval->allowed_earlier_time_for_entry = $shift_work_day->id;
            $interval->legal_working_hours_in_minute = $legal_working_hours_in_minute;
            if ($other_variable) {
                $interval->$other_variable = $other_variable_value; // ممکن است همراه تاخیر ها یک چیز دیگر مثل مرخصی هم باید T شود
            }
            $interval->is_for_leave = $is_for_leave;
            $intervals = self::BreakIntervals($intervals, $interval);
        }

        // تاخیر مجاز برای ورود به سازمان (دقیقه)
        $allowed_delay_time_for_entry = $posts_by_shift_id_list[$shift_work_day->shift_id]->allowed_delay_time_for_entry;
        if ($allowed_delay_time_for_entry > 0) {
            $start = Carbon::parse($start_datetime)->timestamp;
            $end = Carbon::parse($start_datetime)->addMinute($allowed_delay_time_for_entry)->timestamp;
            $interval = new DailyShiftOperation($start, $end);
            $interval->allowed_delay_time_for_entry = $shift_work_day->id;
            if ($other_variable) {
                $interval->$other_variable = $other_variable_value; // ممکن است همراه تاخیر ها یک چیز دیگر مثل مرخصی هم باید T شود
            }
            $interval->is_for_leave = $is_for_leave;
            $intervals = self::BreakIntervals($intervals, $interval);
        }

        // تعجیل مجاز برای خروج به سازمان (دقیقه)
        $allowed_earlier_time_for_exit = $posts_by_shift_id_list[$shift_work_day->shift_id]->allowed_earlier_time_for_exit;
        if ($allowed_earlier_time_for_exit > 0) {
            $start = Carbon::parse($end_datetime)->addMinute(-$allowed_earlier_time_for_exit)->timestamp;
            $end = Carbon::parse($end_datetime)->timestamp;
            $interval = new DailyShiftOperation($start, $end);
            $interval->allowed_earlier_time_for_exit = $shift_work_day->id;
            if ($other_variable) {
                $interval->$other_variable = $other_variable_value; // ممکن است همراه تاخیر ها یک چیز دیگر مثل مرخصی هم باید T شود
            }
            $interval->is_for_leave = $is_for_leave;
            $intervals = self::BreakIntervals($intervals, $interval);
        }
        // تاخیر مجاز برای خروج به سازمان (دقیقه)
        $allowed_delay_time_for_exit = $posts_by_shift_id_list[$shift_work_day->shift_id]->allowed_delay_time_for_exit;
        if ($allowed_delay_time_for_exit > 0) {
            $start = Carbon::parse($end_datetime)->timestamp;
            $end = Carbon::parse($end_datetime)->addMinute($allowed_delay_time_for_exit)->timestamp;
            $interval = new DailyShiftOperation($start, $end);
            $interval->allowed_delay_time_for_exit = $shift_work_day->id;
            if ($other_variable) {
                $interval->$other_variable = $other_variable_value; // ممکن است همراه تاخیر ها یک چیز دیگر مثل مرخصی هم باید T شود
            }
            $interval->is_for_leave = $is_for_leave;
            $intervals = self::BreakIntervals($intervals, $interval);
        }


        return $intervals;
    }
}
