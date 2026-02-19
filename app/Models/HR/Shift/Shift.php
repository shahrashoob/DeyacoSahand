<?php

namespace App\Models\HR\Shift;

use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\Translation\t;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = ["caption", "active_status_id", "number_of_shift_work","legal_working_hours_in_minute","number_of_shift_work_group"];

    public function getCode()
    {
        if ($this->code != "") {
            return $this->code;
        }
        $code = $this->id + 1000;
        $this->save();

        return $code;
    }


    public function create_datetime()
    {
        return jdate(Carbon::parse($this->updated_at)->timestamp)->format('H:i Y/m/d ');

    }
    public function active_status()
    {
        return $this->belongsTo(Status::class, "active_status_id");
    }

    public function shift_work_group()
    {
        return $this->hasMany(ShiftWorkGroup::class);
    }

    /**
     * تعداد دسته بندی های شیفت
     * @return int
     */
    public function getShiftWorkGroupCount()
    {
        $list = $this->shift_work_group()->groupBy("shift_work_group_type_id")->get();
        return count($list);
    }

    public static function GetEndTimeOfWork($shift, $time_request, $now_date = null, $emergency_time = false, $details_result = "public")
    {

        $we_are_in_shift_working_hours = true;
        if ($now_date == null) {
            $now_date = Carbon::now();
            $start_datetime = $now_date;
        }
        // مدت زمان t را دریافات می کند و با توجه به زمان حالا مشخص می کند که زمان کار در T ساعت آیند در گروه شیفت 1 چقدر است.
        $first_shift_work_day = ShiftWorkDay::
        where(["shift_id" => $shift->id])->
        where("start_datetime", "<=", $now_date)->
        where("end_datetime", ">=", $now_date)->
        first();

        if ($emergency_time) {
            if (!$first_shift_work_day) {
                $first_shift_work_day = ShiftWorkDay::
                where(["shift_id" => $shift->id])->
                where("start_datetime", ">=", $now_date)->
                first();
                $start_datetime = Carbon::parse($first_shift_work_day->start_datetime);
                $we_are_in_shift_working_hours = false;
            } else {
                $start_datetime = $now_date;
            }
        }
        if (!$first_shift_work_day) {
            return ["result" => false, "message" => " در ساعت کاری جدول  " . $shift->caption . " قرار نداریم."];
        }

//return  jdate(Carbon::parse( $first_shift_work_day->start_datetime )->timestamp);

        $time_end_shift = $start_datetime->diffInHours(
            Carbon::parse($first_shift_work_day->end_datetime)
        );

        while ($time_request - $time_end_shift > 0) {
            $time_request -= $time_end_shift;

            $first_shift_work_day = ShiftWorkDay::
            where(["shift_id" => $shift->id])->
            where("start_datetime", ">=", $first_shift_work_day->end_datetime)->
            orderBy("start_datetime")->
            first();


            if (!$first_shift_work_day) {
                return ["result" => false, "error" => "تقویم کاری " . $shift->caption . " نامعتبر است."];
            }


            $time_end_shift = Carbon::parse($first_shift_work_day->start_datetime)->diffInHours(
                Carbon::parse($first_shift_work_day->end_datetime)
            );
            $start_datetime = Carbon::parse($first_shift_work_day->start_datetime);
        }


        if (!$first_shift_work_day) {
            return ["result" => false, "error" => "تقویم کاری " . $shift->caption . " برای ".$time_request."ساعت آینده نامعتبر است."];
        }

        $end_data = $start_datetime->addHour($time_request)->format("H:i Y/m/d");

        switch ($details_result) {
            case "public":
                return [
                    "result" => true,
                    "datetime" => $end_data,
                    "datetime_persian" => jdate($start_datetime->timestamp)->format("H:i Y/m/d"),
                    "time_remaining_in_minute" => $now_date->diffInMinutes($end_data),
                ];
                break;
            case "details":
                return [
                    "result" => true,
                    "datetime" => $end_data,
                    "datetime_persian" => jdate($start_datetime->timestamp)->format("H:i Y/m/d"),
                    "time_remaining_in_minute" => $now_date->diffInMinutes($end_data),
                    "we_are_in_shift_working_hours" => $we_are_in_shift_working_hours,
                    "shift_work_day" => $first_shift_work_day,
                ];
                break;
            default:
                return [
                    "result" => true,
                    "datetime" => $end_data,
                    "datetime_persian" => jdate($start_datetime->timestamp)->format("H:i Y/m/d"),
                    "time_remaining_in_minute" => $now_date->diffInMinutes($end_data),
                    "we_are_in_shift_working_hours" => $we_are_in_shift_working_hours,
                    "shift_work_day" => $first_shift_work_day,
                ];
        }


    }

}
