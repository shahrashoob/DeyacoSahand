<?php

namespace App\Models\LineProduct\Machine;

use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Station\Operation\StationSubOperation;
use App\Models\Production\Production;
use App\Models\HR\Shift\ShiftWork;
use App\Models\Utility\Message;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MachineLog extends Model
{
    protected $table = "machine_logs";
    protected $fillable = [
        "current_production_id",
        "machine_id",
        "production_status_id",
        "on_status_id",
        "active_status_id",
        "message_id",
        "user_id",
        "machine_event_type_id",
        "contour_1_value",
        "contour_2_value",
        "contour_3_value",
        "contour_4_value",
        "contour_5_value",
        "user_id",
        "shift_work_id",
        "operator_id",
        "allocation_id",
        "station_sub_operation_id"
    ];

    public function get_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }

    public function station_sub_operation()
    {
        return $this->belongsTo(StationSubOperation::class, 'station_sub_operation_id');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id", "id");
    }

    public function operator()
    {
        return $this->belongsTo(Worker::class, "operator_id", "id");
    }

    public function event_type()
    {
        return $this->belongsTo(MachineEventType::class, "machine_event_type_id");
    }

    public function shift_work()
    {
        return $this->belongsTo(ShiftWork::class);
    }

    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }

    public function on_status()
    {
        return $this->belongsTo(Status::class, "on_status_id", "id");
    }

    public static function getLastLogWithContour(Machine $machine)
    {
        return MachineLog::where([
            "machine_id" => $machine->id,
        ])->
        whereNotNull("contour_1_value")->
        orderByDesc("id")->first();
    }

    public static function getLastLog(Machine $machine)
    {
        return MachineLog::where([
            "machine_id" => $machine->id,
        ])->
        orderByDesc("id")->first();
    }

    public function machine_off_reason()
    {
        return $this->belongsTo(MachineOffReason::class);
    }

    public function production_status()
    {
        return $this->belongsTo(Status::class, "production_status_id", "id");
    }

    public function current_production()
    {
        return $this->belongsTo(Production::class, "current_production_id", 'id');
    }

    public function active_status()
    {
        return $this->belongsTo(Status::class, "active_status_id", "id");
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }


    public function message()
    {
        return $this->belongsTo(Message::class, "message_id", "id");
    }


    public function getStatus()
    {
        return $this->status->caption;
    }


    public function checkMinContour($value1, $value2, $value3, $value4, $value5, $goods_kind = "FabricRaw")
    {

        if ($goods_kind != "FabricRaw") {
            1 / 0;
        }

        $result["result"] = false;

        $contour_number = $this->machine->machine_type->get_property_value(6, 0); // تعداد کنتور
        $contour_caption = $this->machine->machine_type->get_property_value(8, 0); // واحد فرعی کنتور
        $contour_ratio = $this->machine->machine_type->getContourRatio();

        $result["ratio"] = $contour_ratio;
        if (!$contour_ratio || $contour_ratio == 0) {
            $result["error"] = " ضریب تبدیل کنتور برای گروه ماشین " . $this->machine->machine_type->caption . " به درستی ثبت نشده است، لطفا با پشتیبانی تماس بگیرد.";

            return $result;
        }

        // به ازای تعداد کنتور دستگاه باید عداد وارد شده باشد.
        for ($k = 1; $k <= $contour_number; $k++) {
            if (!isset($value1)) {
                $result["error"] = " حداقل تعداد کنتور دستگاه وارد نشده است.";

                return $result;
            }
            $key = "contour_" . $k . "_value";
            $key_value = "value" . $k;
            if (isset($this->$key) && $$key_value * $contour_ratio < $this->$key) {

                $result["error"] = " مقدار " . $contour_caption . " " . $k . " به درستی وارد نشده است.";

                return $result;
            }
        }


        // بررسی اینکه قطب وارد شده با توجه به زمان معتبر است یا خیر
        $check_contour_with_time = Setting::getIntegerValue("check_contour_with_time");
        if ($check_contour_with_time) {
            $new_sum_contour = ((int)$value1 + (int)$value2 + (int)$value3 + (int)$value4 + (int)$value5) * $contour_ratio;

            $contour_in_minute = $this->machine->machine_type->number_of_contour_in_minute; //  کارکرد کنتور اصلی در دقیقه
            $start = Carbon::parse($this->created_at);
            $end = Carbon::now();
            $hours = $start->diffInMinutes($end) + 1;

            $diff_contour = $new_sum_contour - $this->sumCounter("calculate");

            if (($diff_contour / $hours/$contour_ratio) > $contour_in_minute) {
                $result["error"] = "تعداد " . $contour_caption . " وارد شده با توجه به زمان سپری شده از ورود آخرین " . $contour_caption . " دستگاه قابل قبول نیست."
//                ."<br/>new_sum_contour:$new_sum_contour".
//                    "<br/> old contour:".$this->sumCounter("calculate").
//                    "<br/> diff_counter:$diff_contour".
//                    "<br/> diff_counter/hours:".($diff_contour/$hours).
//                    "<br/> value1:$value1".
//                    "<br/> value2:$value2".
//                    "<br/> value3:$value3".
//                    "<br/> value4:$value4".
//                    "<br/> value5:$value5"
                ;
//


                return $result;
            }
        }


        $result["result"] = true;

        return $result;
    }

    public static function getInitResult(Machine $machine, $goods_kind = "FabricRaw")
    {


        $contour_ratio = $machine->machine_type->get_property_value(9, 0); // ضریب تبدیل

        if (!$contour_ratio || $contour_ratio == 0) {
            $result["error"] = " ضریب تبدیل به درستی ثبت نشده است، لطفا با پشتیبانی تماس بگیرد.";
            $result["result"] = false;

            return $result;
        }
        $result["result"] = true;
        $result["ratio"] = $contour_ratio;

        return $result;

    }

    public function sumCounter($type = "")
    {
        if ($type == "") {
            return $this->contour_sum_value;
        } else if ($type == "calculate") {
            return ($this->contour_1_value ?? 0) +
                ($this->contour_2_value ?? 0) +
                ($this->contour_3_value ?? 0) +
                ($this->contour_4_value ?? 0) +
                ($this->contour_5_value ?? 0);
        }
    }

    public function getCounterTextList()
    {
//        if(!isset($this->machine->machine_type)){
//            return "***";
//        }
        $contour_number = $this->machine->machine_type->get_property_value(6, 0); // تعداد کنتور
        $text = "";
        for ($k = 1; $k <= $contour_number; $k++) {
            $key = "contour_" . $k . "_value";
            $text .= $this->$key." ,";
        }

        return $text;
    }

    public static function InDelivering(Machine $machine)
    {
        $last_log = MachineLog::where("machine_id", $machine->id)->orderByDesc("id")->first();
        if ($last_log && $last_log->machine_event_type_id == 650) {
            // در حال تحویل شیفت
            return [
                "result" => false,
                "error" => "با توجه به اینکه ماشین " . $machine->caption . " در حال تحویل شیفت می باشید، امکان تخصیص وجود ندارد، لطفا به اپراتور مسئول (" . $last_log->operator->fullname() . ") جهت تایید تحویل شیفت اطلاع دهید."
            ];

        }

        return [
            "result" => true
        ];
    }
}
