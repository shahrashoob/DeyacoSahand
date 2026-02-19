<?php

namespace App\Http\Controllers\HR\Employment\Admin;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Models\HR\Agent\Agent;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentSelection;
use App\Models\HR\Employment\EmploymentSelectionSelector;
use App\Models\HR\Selection\SelectionPostSetting;
use App\Models\HR\Selection\SelectionSelector;
use App\Models\Post\PostDocumentType;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/*
   می باشد این کنترلر مربوط به تایید اطلاعات کاربر
 * */

class DirectRegisterController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.direct_register.",
        "enable_status" => [],
        "button" => ["caption" => "ثبت نام مستقیم (مشتری، تامین کننده و پیمانکار)", "class" => "btn-primary"],
        "view_path" => "",
        "hidden_button" => true

    ];

    public function index()
    {
        $result = $this->checkPermission();
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $jsn_data_list = JsonDataList::firstOrCreate(
            [
                "other_id" =>Auth::id(),
                "message_type_id" => 380,
            ]);

        session(["logout_data" => $jsn_data_list->id]);
        session(["error_login"=>"شما می توانید یک درخواست همکاری از طرف مشتری، تامین کننده یا پیمانکار ثبت نمایید به طوری که پیامک تایید شماره همراه برای شما ارسال می گردد."

        ."<br/>توجه داشته باشید که هر بار کلیک بر روی ثبت نام مستقیم فقط یک پیامک  تایید شماره همراه برای شما ارسال می شود،"
        ]);
        return redirect()->route("logout");


    }

    public function checkPermission()
    {

        $info = self::$info;
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission($info["route"] . "index")) {
            return [
                "result" => false,
                "error" => "دسترسی  عملیات برای شما تعریف نشده است",
            ];
        }
        return [
            "result" => true,
        ];
    }
}
