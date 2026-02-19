<?php

namespace App\Http\Controllers\HR\Personal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\PersonalController;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\HR\User\UserEntryLog;
use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ConfirmEntryController extends Controller
{
    public static $info = [
        "route" => "hr.personal.confirm_entry.",
        "enable_status" => ["003", "004", "005", "008", "012"],
        "button" => [
            "caption" => "تایید ورود",
            "class" => "btn btn-primary text-white",
            "icon" => "feather icon-log-in"
        ],
        "view_path" => "hr.personal.confirm_entry.",
        "message" => ["confirm" => "آیا از  ورود شاغل اطمینان دارید؟"]
    ];

    public function submit(Worker $worker)
    {

        $result = $this->checkPermission($worker);
        if ($result != "") {
            return $result;
        }

        $result = self::PassGateEnter($worker, Auth::id());
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        return back()->with(["success" => $result["success"]]);

    }

    public static function PassGateEnter(Worker $worker, $user_id, $has_remote_work = false)
    {

        $template_entry_sign_in = Setting::getStringValue("template_entry_sign_in");
        $user_entry_log = UserEntryLog::where("user_id", $worker->id)->whereNull("exit_datetime")->first();

        if ($user_entry_log) {
            return [
                "result" => false,
                "error" => "شاغل در تاریخ " . $user_entry_log->entry_datetime() . " به سازمان وارد شده است و لازم است تا تایید خروج برای او ثبت شود."
            ];
        }
        if ($worker->entry_permit_status_id == 461000100) {
            return [
                "result" => false,
                "error" => $worker->fullname() . "  مجوز ورود به سازمان ندارد، لطفا با واحد منابع انسانی تماس بگیرد."
            ];
        }

        $user_entry_log = UserEntryLog::create([
            "user_id" => $worker->id,
            "user_status_id" => $worker->status_id,
            "entry_register_user_id" => $user_id,
            "entry_datetime" => now()
        ]);

        // بررسی انیکه آیا مجوز خروج، پس از ورود دارد یا خیر
        $the_worker_has_permission_to_leave_after_entering_count = PostUser::join("posts", "posts.id", "post_id")->
        where("user_id", $worker->id)->
        where("the_worker_has_permission_to_leave_after_entering", 0)->
        count();

        if ($the_worker_has_permission_to_leave_after_entering_count > 0) {
            $worker->exit_permit_status_id = 461000100; // اجازه خروج ندارد
        }

        $worker->status_id = 4620001;// حاضر در محل کار
        $worker->save();
        $hr_sing_in_sms = Setting::getIntegerValue("hr_sing_in_sms");
        $date = jdate(Carbon::parse($user_entry_log->entry_datetime))->format('Y/m/d');
        $time = jdate(Carbon::parse($user_entry_log->entry_datetime))->format('H:i:s');

        if ($hr_sing_in_sms && !$has_remote_work) {
            Notification::send("00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
                new SMSNotification($template_entry_sign_in, $date, $time, null, $worker->fullname()));
        }

        PostUser::PostTrafficNotification($worker,$user_id,"ورود",$time." ".$date);


        return [
            "result" => true,
            "success" => "ورود با موفقیت ثبت گردید."
        ];
    }

    public function checkPermission(Worker $worker)
    {

        $result = PersonalController::checkPermissionConditions($worker, ConfirmEntryController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

}
