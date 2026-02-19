<?php

namespace App\Http\Controllers\HR\Personal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\PersonalController;
use App\Models\HR\User\UserEntryLog;
use App\Models\Post\PostUser;
use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class StartRemoteWorkController extends Controller
{
    public static $info = [
        "route" => "hr.personal.start_remote_work.",
        "enable_status" => ["003", "004", "005", "008", "012"],
        "button" => [
            "caption" => "شروع دورکاری",
            "class" => "btn btn-primary text-white",
            "icon" => "feather icon-log-in"
        ],
        "view_path" => "hr.personal.start_remote_work.",
        "message" => ["confirm" => "آیا از  شروع دورکاری اطمینان دارید؟"]
    ];

    public function submit(Worker $worker)
    {

        $result = $this->checkPermission($worker);
        if ($result != "") {
            return $result;
        }

        if ($worker->is_possible_to_work_remotely == 0) {
            return back()->withErrors('امکان ثبت   دورکاری برای شما وجود ندارد. لطفا با واحد منابع انسانی تماس بگیرید.');
        }
        $result = ConfirmEntryController::PassGateEnter($worker, Auth::id(), true);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        return back()->with('شروع دورکاری با موفقیت ثبت گردید.');

    }


    public function checkPermission(Worker $worker)
    {

        $result = PersonalController::checkPermissionConditions($worker, StartRemoteWorkController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
