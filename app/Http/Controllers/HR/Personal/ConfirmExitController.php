<?php

namespace App\Http\Controllers\HR\Personal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\PersonalController;
use App\Models\Post\PostUser;
use App\Models\Utility\Car\Car;
use App\Models\Utility\HR\Leave\Leave;
use App\Models\HR\LeaveOvertime\LeaveOvertime;
use App\Models\HR\User\UserEntryLog;
use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ConfirmExitController extends Controller
{
    public static $info = [
        "route" => "hr.personal.confirm_exit.",
        "enable_status" => ["001", "002"],
        "button" => [
            "caption" => "تایید خروج",
            "class" => "btn btn-primary text-white",
            "icon" => "feather icon-log-out"
        ],
        "view_path" => "hr.personal.confirm_exit.",
        "message" => ["confirm" => "آیا از  خروج شاغل اطمینان دارید؟"]
    ];

    public function submit(Worker $worker)
    {

        $result = $this->checkPermission($worker);
        if ($result != "") {
            return $result;
        }

        $result = self::PassGateExit($worker, Auth::id());
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        return back()->with(["success" => $result["success"]]);
    }

    public static function PassGateExit(
        Worker $worker,
               $user_id,
               $has_remote_work = false,
               $sms_template = "hrentrysignout",
               $max_time_allowed_for_percent_in_company = "",
               $check_earlier_time_for_exist = true,
               $exit_datetime = null)
    {


        // اگر قالب پیش فرض ورود خروج بود،
        // نام قالب را از تنظیمات بر می داریم
        // ولی اگر قالب خاصی بود تغییر نمی دهیم و همان را انتخاب می کنیم.

        $template_entry_sign_out=$sms_template;
       if($sms_template == "hrentrysignout" ){
           $template_entry_sign_out=Setting::getStringValue("template_entry_sign_out");
       }

        $user_entry_log = UserEntryLog::where("user_id", $worker->id)->whereNull("exit_datetime")->first();

        if (!$user_entry_log) {
            return [
                "result" => false,
                "error" => "شاغل آخرین بار از سازمان خارج شده است و لازم است تا ورود برای او ثبت شود."
            ];
        }

        if ($worker->exit_permit_status_id == 461000100) {
            return [
                "result" => false,
                "error" => $worker->fullname() . "  مجوز خروج از سازمان ندارد، لطفا با واحد منابع انسانی تماس بگیرد."
            ];
        }

        $status_id = 4620008;// خارج شده از سازمان
        /*************************/
        if (!$has_remote_work) {
            // بررسی تعجیل مجاز برای خروج از سازمان
            $post_users = PostUser::where("user_id", $worker->id)->get();
            $count_earlier_time_for_exit = 0;
            foreach ($post_users as $post_user) {
                $shift_work_day = $post_user->getShiftWorkDay("now");

                if ($shift_work_day && Carbon::parse($shift_work_day->end_datetime)->greaterThan(Carbon::now())) {

                    // تعجیل مجاز برای خروج از سازمان برای شیفت ها
                    if ($post_user->post->allowed_earlier_time_for_exit < Carbon::parse($shift_work_day->end_datetime)->diffInMinutes(Carbon::now())) {
                        $count_earlier_time_for_exit--;
                    } else {
                        $count_earlier_time_for_exit += 1000;
                    }

                }

            }
            $post_info = PostUser::
            join("posts", "post_id", "posts.id")->
            where("user_id", $worker->id)->
            selectRaw("max(allowed_earlier_time_for_entry) as allowed_earlier_time_for_entry,max(allowed_delay_time_for_exit) as allowed_delay_time_for_exit")->
            first();

            //    // تعجیل مجاز برای خروج از سازمان برای مرخصی ها
            $leave_list_count = LeaveOvertime::where("user_id", $worker->id)->
            join("leave_overtime_confirmation", "leave_overtime_id", "leave_overtimes.id")->
            join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
            whereIn("leave_overtimes.status_id", [4630003, 4630006, 4630007])->
            where("start_datetime", "<", Carbon::now()->addMinute(($post_info->allowed_earlier_time_for_entry ?? 1)))->
            where("end_datetime", ">", Carbon::now()->addMinute(-$post_info->allowed_delay_time_for_exit ?? 1))->
            where("leave_overtime_group_id", 1)->
            count();

            if ($leave_list_count > 0) {
                $count_earlier_time_for_exit = +1000;
            }


            if ($count_earlier_time_for_exit < 0 && $check_earlier_time_for_exist) {
                return [
                    "result" => false,
                    "error" => " با توجه به اینکه تعجیل مجاز جهت خروج از سازمان برای " .
                        $post_user->post->caption . " " .
                        $post_user->post->allowed_earlier_time_for_exit .
                        " دقیقه می باشد،  خروج از سازمان امکان پذیر نیست."
                ];

            }

        }
        $user_entry_log->update([
            "exit_register_user_id" => $user_id,
            "exit_datetime" => $exit_datetime ? $exit_datetime : now()
        ]);

        $worker->status_id = $status_id;
        $worker->save();

        $hr_sign_out_sms = Setting::getIntegerValue("hr_sign_out_sms");
        $date = jdate(Carbon::parse($user_entry_log->exit_datetime))->format('Y/m/d');
        $time = jdate(Carbon::parse($user_entry_log->exit_datetime))->format('H:i:s');

        if (($hr_sign_out_sms && !$has_remote_work) || $sms_template != "hrentrysignout") {
            Notification::send("00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
                new SMSNotification($template_entry_sign_out, $date, $time, $max_time_allowed_for_percent_in_company, $worker->fullname()));
        }

        PostUser::PostTrafficNotification($worker,$user_id,"خروج",$time." ".$date);

        return [
            "result" => true,
            "success" => "خروج با موفقیت ثبت گردید."
        ];

    }

    public function checkPermission(Worker $worker)
    {

        $result = PersonalController::checkPermissionConditions($worker, ConfirmExitController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
