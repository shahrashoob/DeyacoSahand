<?php

namespace App\Imports;

use App\Models\HR\LeaveOvertime\ImportLeaveOvertime;
use App\Models\HR\LeaveOvertime\ImportLeaveReminder;
use App\Models\HR\LeaveOvertime\LeaveOvertimeType;
use App\Models\HR\Shift\NewShiftWorkDay;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;

class LeaveReminderImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    var $shift;
    var $year;


    public function collection(Collection $rows)
    {
        //
        $cols = [
            "year" => 0,
            "national_code" => 1,
            "leave_type_id" => 2,
            "start_date_jalali" => 3,
            "end_date_jalali" => 4,
            "leave_remainder" => 5,
        ];

        ImportLeaveReminder::where("id", ">", 0)->delete();
        $i = 0;
        $error_format = "";
        foreach ($rows as $row) {
            $i++;
            if ($i == 1) {
                foreach ($cols as $k => $v) {
                    if (!isset($row[$cols[$k]]) || $row[$cols[$k]] != $k) {
                        $error_format = "فرمت فایل به درستی انتخاب نشده است." . "<br/>";
                    }
                }
            }
            if ($error_format) {
                $shift_work_day = new ImportLeaveReminder();
                $shift_work_day->year = 1;
                $shift_work_day->national_code = 0;
                $shift_work_day->leave_type_id = 1;
                $shift_work_day->start_date_jalali = "";
                $shift_work_day->end_date_jalali = "";
//                $shift_work_day->leave_in_start = "";
//                $shift_work_day->leave_in_end = "";
                $shift_work_day->leave_remainder = "";
                $shift_work_day->error = $error_format;

                $shift_work_day->save();

                return;
            }
            if ($i <= 2) {
                continue;
            }
            foreach ($cols as $k => $v) {
                if (!isset($row[$v])) {
                    $row[$v] = 0;
                }
            }
            $error_text = "";


            $national_code = $row[$cols["national_code"]];
            $worker=Worker::where("national_code", $national_code)->first();
            if (!$worker) {
                $error_text .= "کد ملی به درستی وارد نشده است." . "<br/>";
            }

            $leave_type_id = $row[$cols["leave_type_id"]];
            $leave_type=LeaveOvertimeType::where("id", $leave_type_id)->first();
            if (!$leave_type) {
                $error_text .= "نوع مرخصی به درستی وارد نشده است." . "<br/>";
            }


            $start_date = Jalalian::fromFormat('Y/m/d', $row[$cols["start_date_jalali"]]);
            $start_date = $start_date->toCarbon();

            $end_date = Jalalian::fromFormat('Y/m/d', $row[$cols["end_date_jalali"]]);
            $end_date = $end_date->toCarbon();


            $leave_reminder = new ImportLeaveReminder();
            $leave_reminder->year =  $row[$cols["year"]];
            $leave_reminder->user_id = $worker->id??0;
            $leave_reminder->national_code =  $row[$cols["national_code"]];

            $leave_reminder->leave_type_id =  $row[$cols["leave_type_id"]];
            $leave_reminder->start_date_jalali =  $row[$cols["start_date_jalali"]];
            $leave_reminder->start_date = $start_date;

            $leave_reminder->end_date_jalali =  $row[$cols["end_date_jalali"]];
            $leave_reminder->end_date =  $end_date;


            $leave_reminder->leave_in_start =   $row[$cols["leave_remainder"]];
//            $leave_reminder->leave_in_end =  $row[$cols["leave_in_end"]];
//            $leave_reminder->leave_remainder =  $row[$cols["leave_remainder"]];
            $leave_reminder->error = $error_text==""?null:$error_text;

            $leave_reminder->save();





        }
    }

    public function getMonth($day)
    {
        if ($day <= 31) {
            return 1;
        } elseif ($day <= 62) {
            return 2;
        } elseif ($day <= 93) {
            return 3;
        } elseif ($day <= 124) {
            return 4;
        } elseif ($day <= 155) {
            return 5;
        } elseif ($day <= 186) {
            return 6;
        } elseif ($day <= 216) {
            return 7;
        } elseif ($day <= 246) {
            return 8;
        } elseif ($day <= 276) {
            return 9;
        } elseif ($day <= 306) {
            return 10;
        } elseif ($day <= 336) {
            return 11;
        } else {
            return 12;
        }

    }
}
