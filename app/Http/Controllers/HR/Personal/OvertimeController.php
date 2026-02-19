<?php

namespace App\Http\Controllers\HR\Personal;

use App\Events\HR\LeaveLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\Script\Script1023Controller;
use App\Models\Post\PostUser;
use App\Models\HR\LeaveOvertime\LeaveOvertime;
use App\Models\HR\LeaveOvertime\LeaveOvertimeConfirmation;
use App\Models\HR\LeaveOvertime\LeaveOvertimeLog;
use App\Models\HR\Shift\ShiftWorkDay;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class OvertimeController extends Controller
{
    public static $info = [
        "route" => "hr.personal.overtime.",
        "enable_status" => ["001", "003", "005", "006", "008","009","010","011"],
        "button" => [
            "caption" => "ثبت درخواست اضافه کاری",
            "class" => "btn btn-primary text-white",
            "icon" => "feather icon-log-in"
        ],
        "view_path" => "hr.personal.overtime.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "hr.personal.index";

    public function __construct()
    {
        $this->route_path = OvertimeController::$info["route"];
        $this->view_path = OvertimeController::$info["view_path"];
    }

    public function index()
    {


        $worker = Worker::find(Auth::id());
        $overtime_request = session("overtime_request");
        if (!$overtime_request) {
            $overtime_request = [
                "leave_overtime_type_id" => 0,
                "start_time_m" => -1,
                "start_time_h" => -1,
                "start_datetime" => null,
                "end_time_m" => -1,
                "end_time_h" => -1,
                "end_datetime" => null,
                "text" => ""
            ];
        }
        $post_users = PostUser::where("user_id", $worker->id)->groupBy("post_id")->get();

        if (count($post_users) == 0) {
            return redirect()->route($this->dashboard_route, [$worker->id, $worker->random])->
            withErrors("هیچ پست سازمانی برای شما تعریف نشده است، لطفا با واحد منابع انسانی تماس بگیرید.");
        }

        $text = "";
        foreach ($post_users as $post_user) {
            if (!$post_user->post->shift) {
                $text .= $post_user->post->caption . ", ";
            }
        }
        if ($text != "") {
            return redirect()->route($this->dashboard_route, [$worker->id, $worker->random])->
            withErrors("با توجه به اینکه برای پست های زیر شیفت مشخص نشده است، امکان ثبت اضافه کاری وجود ندارد، لطفا با واحد منابع انسانی تماس بگیرید." . "<br/>" . $text);
        }

        $post_users_not_shift_work = PostUser::where("user_id", $worker->id)->whereNull("shift_work_id")->get();
        $text = "";
        if (count($post_users_not_shift_work) != 0) {

            foreach ($post_users_not_shift_work as $item) {
                $text .= $item->post->caption . ", ";
            }

            return redirect()->route($this->dashboard_route, [$worker->id, $worker->random])->
            withErrors("گروه شیفت برای پست های زیر مشخص نشده است، لطفا با واحد منابع انسانی تماس بگیرید." . "<br/>" . $text);
        }


        return view($this->view_path . "index", compact("worker", "overtime_request"));
    }


    public function submit(Request $request)
    {


        $worker = Worker::find(Auth::id());
        $post_users = PostUser::where("user_id", $worker->id)->groupBy("post_id")->get();

        $start_datetime = Carbon::parse($request->start_datetime);
        $end_datetime = Carbon::parse($request->end_datetime);

        session(["overtime_request" => $request->all()]);

        $register_after_tacking=0;
        if (Carbon::now()->greaterThan($start_datetime)) {
            $number_register_after_tacking = Worker::GetRegisterAfterTackingCount(Auth::id(), 2);
            if ($number_register_after_tacking + 1 < $post_users[0]->post->for_worker_overtime_number_of_register_after_tacking) {
                $register_after_tacking = 1;
            } else {
                return back()->withErrors("زمان شروع اضافه کاری نمی تواند قبل از زمان حال باشد.",);
            }

        }
        if ($start_datetime->greaterThan($end_datetime)) {
            return back()->withErrors("زمان پایان اضافه کاری نمی تواند از شروع شروع اضافه کاری کوچکتر باشد.");
        }

        $check_time_is_free = $this->check_time_is_free($post_users, $start_datetime, $end_datetime);

        if (count($check_time_is_free["confirm_posts"]) != 0) {

            $message = $this->check_start_and_end_date($post_users, $check_time_is_free["confirm_posts"], $start_datetime, $end_datetime);
            if ($message != "") {
                return back()->withErrors($message);
            }

        }

        // بررسی اینکه در این زمان مرخصی قبلا نگرفته باشد.
        $message = LeaveController::check_has_replace_work($worker, $start_datetime, $end_datetime, 2);
        if ($message) {
            return back()->withErrors($message);
        }

        $message = LeaveController::check_has_any_leave_overtime($worker, $start_datetime, $end_datetime, 2);
        if ($message) {
            return back()->withErrors($message);
        }

        $start_datetime = Carbon::parse($request->start_datetime);
        $end_datetime = Carbon::parse($request->end_datetime);
        $leave = LeaveOvertime::create([
            "user_id" => $worker->id,
            "leave_overtime_type_id" => 200,// اضافه کاری
            "start_datetime" => $start_datetime,
            "end_datetime" => $end_datetime,
            "status_id" => 4630002, //  در انتظار تایید مافوق
            "register_after_tacking" => $register_after_tacking ,
        ]);

        foreach ($post_users as $post_user) {

            //تعداد سطح بالایی
            $time = $start_datetime->diffInHours($end_datetime);
            $for_work_overtime_a_few_top_levels_must_confirm = $post_user->post->for_work_overtime_a_few_top_levels_must_confirm;
            if ($time > $post_user->post->for_work_overtime_a_few_top_levels_must_confirm_time) {
                $for_work_overtime_a_few_top_levels_must_confirm = $post_user->post->for_work_overtime_a_few_top_levels_must_confirm_time_level;
            }


            $leave_overtime_confirmation = LeaveOvertimeConfirmation::create([
                "leave_overtime_id" => $leave->id,
                "post_user_id" => $post_user->id,
                "number_top_levels_must_confirm" => $for_work_overtime_a_few_top_levels_must_confirm,
                "number_top_levels_confirmed" => 0,
                "current_confirm_post_id" => $post_user->post->parent_id,
                "status_id" => 4630002
            ]);

        }

        $this->sendSmsParent($leave);


        event(new LeaveLogEvent($leave, 4630001, $request->text));

        session(["leave_overtime_request" => null]);

        return redirect()->route($this->dashboard_route, [
            $worker,
            $worker->random
        ])->with(["success" => "یک درخواست اضافه کاری با موفقیت ثبت گردید."]);


    }

    public function confirm_parent_post(Request $request)
    {

        $worker = Worker::find(Auth::id());

        $post_ids = PostUser::where("user_id", $worker->id)->pluck("post_id")->toArray();

        $post_user = PostUser::where("user_id", $worker->id)->whereIn("post_id", $post_ids)->first();

        if (!$post_user) {
            return back()->withErrors("با توجه به پست های سازمانی شما، امکان تایید برای شما وجود ندارد.");
        }

        $leave_overtime = LeaveOvertime::find($request->leave_overtime_id);

        if (!$leave_overtime) {
            return back()->withErrors("شناسه اضافه کاری نامعتبر است، لطفا یکبار دیگر تلاش کنید.");
        }
        if ($leave_overtime->status_id != 4630002) {
            return back()->withErrors("وضعیت اضافه کاری جهت تایید مافوق معتبر نمی باشد، لطفا با واحد منابع انسانی تماس بگیرید.");
        }

//return $request->all();
        switch ($request->type) {
            case "confirm":

                $this->confirm_users($leave_overtime, $request->comment);
                $this->sendSmsParent($leave_overtime);

                return redirect()->back()->with(["success" => "تایید  اضافه کاری با موفقیت ثبت گردید"]);
                break;
            case "reject":

                $leave_overtime->status_id = 4630004;//  عدم تایید مافوق
                $leave_overtime->save();

                event(new LeaveLogEvent($leave_overtime, 4630004, $request->comment));

                return redirect()->back()->with(["success" => "عدم تایید  اضافه کاری با موفقیت ثبت گردید"]);

                break;
            case "comment":
                $leave_overtime->status_id = 4630009;//  در انتظار اخذ توضیح
                $leave_overtime->save();


                $hr_leave_set_comment_replace_sms = Setting::getIntegerValue("hr_leave_set_comment_replace_sms");
                $company_name = Setting::getStringValue("company_name");

                if ($hr_leave_set_comment_replace_sms) {
                    Notification::send("00" . ($leave_overtime->worker->mobile_country->area_code ?? "98") . $leave_overtime->worker->mobile,
                        new SMSNotification("hrleavesetcomment", $leave_overtime->worker->id ?? "", $leave_overtime->worker->random ?? "", $company_name, $leave_overtime->leave_overtime_type->caption, $worker->fullname()));

                }
                event(new LeaveLogEvent($leave_overtime, 4630009, $request->comment));

                return redirect()->back()->with(["success" => "اخذ توضیحات  اضافه کاری با موفقیت ثبت گردید"]);
                break;
        }


        return redirect()->back()->withErrors("درخواست نامعتبر است.");

    }

    public function confirm_users(LeaveOvertime $leave_overtime, $text)
    {

        $worker = Worker::find(Auth::id());


        // از بین آنهایی که تایید کرده اند، پست آنها را برگردان
        $user_ids = LeaveOvertimeLog::where("leave_overtime_id", $leave_overtime->id)->pluck("user_id")->toArray();

        // همه پست هایی که کاربر الان دارد.
        $worker_post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids_with_out_menu", $worker);

        $post_ids = PostUser::whereIn("user_id", $user_ids)->pluck("post_id")->toArray();
        $post_ids=array_merge($post_ids,$worker_post_ids);


        // از بین مافوق هایی که باید تایید کنند، اگر شناسه آنها داخل post_ids هست، آن را تایید کن و یک سطح بالاتر ببر، و دوباره امتحان کن
        while (1) {
            $leave_overtime_confirmation = LeaveOvertimeConfirmation::where([
                "leave_overtime_id" => $leave_overtime->id,
                "status_id" => 4630002
            ])->
            whereIn("current_confirm_post_id", $post_ids)->
            first();
            if (!$leave_overtime_confirmation) {
                break;
            }

            if ($leave_overtime_confirmation->number_top_levels_confirmed < $leave_overtime_confirmation->number_top_levels_must_confirm) {

                $number_confirmed = $leave_overtime_confirmation->number_top_levels_confirmed + 1 >= $leave_overtime_confirmation->number_top_levels_must_confirm;
                $leave_overtime_confirmation->number_top_levels_confirmed = $leave_overtime_confirmation->number_top_levels_confirmed + 1;

                if ($number_confirmed) {
                    $leave_overtime_confirmation->current_confirm_post_id = null;
                    $leave_overtime_confirmation->status_id = 4630003; // تایید شده
                    $leave_overtime_confirmation->save();
                } else {
                    $leave_overtime_confirmation->current_confirm_post_id = $leave_overtime_confirmation->current_confirm_post->parent_id ?? null;
                    $leave_overtime_confirmation->status_id = $leave_overtime_confirmation->current_confirm_post->parent ?
                        4630002 : // مافوق
                        4630003; // تایید شده
                    $leave_overtime_confirmation->save();
                }


            } else {
                // اگر همه مافوق ها تایید کردند، بشود تایید شده
                $leave_overtime_confirmation->status_id = 4630003;// تایید شده
                $leave_overtime_confirmation->current_confirm_post_id = null;
                $leave_overtime_confirmation->save();
            }


        }

        $count_not_confirmed = LeaveOvertimeConfirmation::where([
            "leave_overtime_id" => $leave_overtime->id,
        ])->
        where("status_id", "!=", 4630003)->
        count();

        if ($count_not_confirmed == 0) {

            $leave_overtime->status_id = 4630003;
            $leave_overtime->save();

            $company_name = Setting::getStringValue("company_name");
            Notification::send("00" . ($leave_overtime->worker->mobile_country->area_code ?? "98") . $leave_overtime->worker->mobile,
                new SMSNotification("hrleaveend", $company_name, null, null, $leave_overtime->leave_overtime_type->caption));

            Script1023Controller::calculateForDays($leave_overtime->worker,$leave_overtime->start_datetime,$leave_overtime->end_datetime);

        }

        event(new LeaveLogEvent($leave_overtime, 4630003, $text));
    }

    public function set_user_comment(Request $request)
    {

        $worker = Worker::find(Auth::id());

        $leave = LeaveOvertime::find($request->leave_overtime_id);
        if (!$leave) {
            return back()->withErrors("شناسه اضافه کاری نامعتبر است، لطفا یکبار دیگر تلاش کنید.");
        }

        if ($leave->status_id != 4630009) {
            return back()->withErrors("وضعیت اضافه کاری جهت ثبت توضیحات معتبر نمی باشد، لطفا با واحد منابع انسانی تماس بگیرید.");
        }


        $leave->status_id = 4630002;// در انتظار تایید مافوق
        $leave->save();

        $post_user = PostUser::where("user_id", $worker->id)->first();

        event(new LeaveLogEvent($leave, 4630010, $request->comment));


        $this->sendSmsParent($leave, "hrleavesetusercommentalert");


        return redirect()->back()->with(["success" => "توضیحات با موفقیت ثبت گردید"]);
    }

    public function cancel(LeaveOvertime $leave_overtime)
    {

        $worker = Worker::find(Auth::id());

        if ($leave_overtime->user_id != $worker->id) {
            return back()->withErrors("این درخواست توسط شما ثبت نشده است لذا امکان کنسل کردن آن وجود ندارد.");
        }

        if (in_array($leave_overtime->status_id, [4630003, 4630004, 4630005, 4630007, 4630006, 4630008])) {
            return back()->withErrors("با توجه به اینکه اضافه کاری انجام شده(تایید شده) است، امکان کنسل کردن آن وجود ندارد.");
        }


        $leave_overtime->status_id = 4630005;// انصراف
        $leave_overtime->save();


        event(new LeaveLogEvent($leave_overtime, 4630011, ""));

        return redirect()->back()->with(["success" => "کنسل کردن اضافه کاری با موفقیت ثبت گردید"]);
    }

    public function sendSmsParent(LeaveOvertime $leave_overtime, $template = null)
    {

        $company_name = Setting::getStringValue("company_name");
        $hr_overtime_conform_parent_sms = Setting::getIntegerValue("hr_overtime_conform_parent_sms");
        if (!$hr_overtime_conform_parent_sms) {
            return false;
        }
        $worker_ids = [];

        $list = LeaveOvertimeConfirmation::where([
            "leave_overtime_id" => $leave_overtime->id,
            "status_id" => 4630002
        ])->get();

        foreach ($list as $leave_overtime_confirmation) {

            // اگر تعداد تایید ها به حد نصاب نرسیده برای آخرین پست پیامک ارسال می شود.
            if ($leave_overtime_confirmation->number_top_levels_confirmed < $leave_overtime_confirmation->number_top_levels_must_confirm) {


                $post_users = PostUser::where("post_id", $leave_overtime_confirmation->current_confirm_post_id)->get();
                foreach ($post_users as $post_user) {
                    // ممکن است یک نفر دو پست داشته باشد و مافوق هر دو پست یک نفر باشد، بنابراین نیاز نیست 2 پیامک ارسال شود.
                    if (isset($worker_ids[$post_user->user_id])) {
                        Notification::send("00" . ($post_user->worker->mobile_country->area_code ?? "98") . $post_user->worker->mobile,
                            new SMSNotification($template ?? "hrleaveconfirmparentpost",
                                $post_user->worker->id ?? "",
                                $post_user->worker->random ?? "",
                                $company_name,
                                $leave_overtime_confirmation->leave_overtime->leave_overtime_type->caption,
                                $leave_overtime_confirmation->leave_overtime->worker->fullname())
                        );
                    }
                    $worker_ids[$post_user->user_id] = 1;

                }
            }

        }


    }

    public function check_time_is_free($post_users, $start_datetime, $end_datetime)
    {
        // بررسی اینکه زمان انتخاب شده برای اضافه کاری زمانی باشد که فرد در آن زمان هیچ شیفتی ندارد.

        $replace_confirm_post_users = [
            "confirm_posts" => []
        ];
        // بررسی اینکه با توجه به زمان مرخصی
        foreach ($post_users as $post_user) {

            $count = 0;
            // بررسی نقطه شروع
            $count += ShiftWorkDay::where([
                "shift_id" => $post_user->post->shift_id,
                "shift_work_id" => $post_user->shift_work_id
            ])->
            where("start_datetime", "<=", $start_datetime)->
            where("end_datetime", ">", $start_datetime)->count();

            // بررسی نقطه پایان
            $count += ShiftWorkDay::where([
                "shift_id" => $post_user->post->shift_id,
                "shift_work_id" => $post_user->shift_work_id
            ])->
            where("start_datetime", "<", $end_datetime)->
            where("end_datetime", ">=", $end_datetime)->count();

            // بررسی اینکه داخل باز است یا خیر
            $count += ShiftWorkDay::where([
                "shift_id" => $post_user->post->shift_id,
                "shift_work_id" => $post_user->shift_work_id
            ])->
            where("start_datetime", ">=", $start_datetime)->
            where("end_datetime", "<=", $end_datetime)->count();

            if ($count > 0) {
                $replace_confirm_post_users["confirm_posts"][] = $post_user->id;
            }

        }

        return $replace_confirm_post_users;
    }

    public function check_start_and_end_date($post_users, $post_user_confirm, $start_datetime, $end_datetime)
    {

        $start_pas_count = 0;
        $end_pas_count = 0;
        $start_datetime_for_work = null;
        $end_datetime_for_work = null;
        // بررسی اینکه با توجه به زمان مرخصی
        foreach ($post_users as $post_user) {

            if (in_array($post_user->id, $post_user_confirm)) {
                // بررسی نقطه شروع
                $start_pas_count += ShiftWorkDay::where([
                    "shift_id" => $post_user->post->shift_id,
                    "shift_work_id" => $post_user->shift_work_id
                ])->
                where("start_datetime", "<=", $start_datetime)->
                where("end_datetime", ">", $start_datetime)->count();

                $shift_work_day_start = ShiftWorkDay::where([
                    "shift_id" => $post_user->post->shift_id,
                    "shift_work_id" => $post_user->shift_work_id
                ])->
                where("start_datetime", ">", $start_datetime)->
                orderBy("start_datetime")->first();

                if ($shift_work_day_start) {
                    if ($start_datetime_for_work) {
                        // گرفتن min ساعت های شروع
                        if (!$start_datetime_for_work->greaterThan(Carbon::parse($shift_work_day_start->start_datetime))) {
                            $start_datetime_for_work = Carbon::parse($shift_work_day_start->start_datetime);
                        }
                    } else {
                        $start_datetime_for_work = Carbon::parse($shift_work_day_start->start_datetime);
                    }
                }

                // بررسی نقطه پایان
                $end_pas_count += ShiftWorkDay::where([
                    "shift_id" => $post_user->post->shift_id,
                    "shift_work_id" => $post_user->shift_work_id
                ])->
                where("start_datetime", "<", $end_datetime)->
                where("end_datetime", ">=", $end_datetime)->count();

                $shift_work_day_end = ShiftWorkDay::where([
                    "shift_id" => $post_user->post->shift_id,
                    "shift_work_id" => $post_user->shift_work_id
                ])->
                where("end_datetime", "<", $end_datetime)->
                orderByDesc("end_datetime")->first();

                if ($shift_work_day_end) {
                    if ($end_datetime_for_work) {
                        //گرفتن max ساعت های پایان
                        if ($end_datetime_for_work->greaterThan(Carbon::parse($shift_work_day_end->end_datetime))) {
                            $end_datetime_for_work = Carbon::parse($shift_work_day_end->end_datetime);
                        }
                    } else {
                        $end_datetime_for_work = Carbon::parse($shift_work_day_end->end_datetime);
                    }
                }

            }

        }
        $message = "";
        if ($start_pas_count == 0) {
            $time = jdate(Carbon::parse($start_datetime_for_work)->timestamp)->format(" ساعت H:i:s  %A  Y/m/d  ");
            $message .= "اولین ساعت شروع کار شما  " . $time . " می باشد، لذا بعد از آن نیاز به ثبت اضافه کاری نمی باشد، " . "<br/>" . "لطفا زمان پایان اضافه کاری را ویرایش کنید." . "<br/>";
        }
        if ($end_pas_count == 0) {
            $time = jdate(Carbon::parse($end_datetime_for_work)->timestamp)->format(" ساعت H:i:s  %A  Y/m/d  ");
            $message .= "آخرین ساعت حضور شما در سازمان " . $time . " می باشد، لذا برای قبل از آن نیاز به ثبت اضافه کاری نمی باشد. " . "<br/>" . "لطفا زمان شروع اضافه کاری را ویرایش کنید.";
        }

        return $message;
    }


}
