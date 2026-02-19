<?php

namespace App\Http\Controllers\HR\Personal;

use App\Events\HR\LeaveLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\Script\Script1012Controller;
use App\Http\Controllers\Utility\Script\Script1023Controller;
use App\Models\Post\Post;
use App\Models\Post\PostReplace;
use App\Models\Post\PostUser;
use App\Models\User;
use App\Models\HR\LeaveOvertime\LeaveOvertime;
use App\Models\HR\LeaveOvertime\LeaveOvertimeConfirmation;
use App\Models\HR\LeaveOvertime\LeaveOvertimeGroup;
use App\Models\HR\LeaveOvertime\LeaveOvertimeLog;
use App\Models\HR\LeaveOvertime\LeaveOvertimeType;
use App\Models\HR\Shift\ShiftWorkDay;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use function Symfony\Component\Translation\t;

class LeaveController extends Controller
{
    public static $info = [
        "route" => "hr.personal.leave.",
        "enable_status" => ["001", "003", "005", "006", "008", "009", "010", "011"],
        "button" => [
            "caption" => "ثبت درخواست مرخصی",
            "class" => "btn btn-primary text-white",
            "icon" => "feather icon-log-in"
        ],
        "view_path" => "hr.personal.leave.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "hr.personal.index";

    public function __construct()
    {
        $this->route_path = LeaveController::$info["route"];
        $this->view_path = LeaveController::$info["view_path"];
    }

    public function index($worker_id = 0)
    {


        $worker = Worker::find(Auth::id());
        $leave_overtime_request = session("leave_overtime_request");
        if (!$leave_overtime_request) {
            $leave_overtime_request = [
                "leave_overtime_type_id" => 1,
                "start_datetime" => null,
                "end_datetime" => null,
                "text" => ""
            ];
        }
        $leave_type_option = Option::get("leave_overtime_type", $leave_overtime_request["leave_overtime_type_id"], 1);
        $post_user_all = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_user_object", $worker);
        $post_users = [];
        foreach ($post_user_all as $post_user) {
            $post_users[$post_user->id] = $post_user;
        }


        if (count($post_users) == 0) {
            return redirect()->route($this->dashboard_route, [$worker->id, $worker->random])->
            withErrors("هیچ پست سازمانی برای شما تعریف نشده است، لطفا با واحد منابع انسانی تماس بگیرید.");
        }

        $text = "";
        // چک کردن اینکه برای همه پست ها شیفت تعریف شده باشد.
        foreach ($post_users as $post_user) {
            if (!$post_user->post->shift) {
                $text .= $post_user->post->caption . ", ";
            }
        }

        if ($text != "") {
            return redirect()->route($this->dashboard_route, [$worker->id, $worker->random])->
            withErrors("با توجه به اینکه برای پست های زیر شیفت مشخص نشده است، امکان ثبت مرخصی وجود ندارد، لطفا با واحد منابع انسانی تماس بگیرید." . "<br/>" . $text);
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


        return view($this->view_path . "index", compact("leave_type_option", "worker", "leave_overtime_request"));
    }

    public function submit(Request $request)
    {


        $worker = Worker::find(Auth::id());

        $start_datetime = Carbon::parse($request->start_datetime);
        $end_datetime = Carbon::parse($request->end_datetime);


        // گرفتن پست های فعال در زمان شروع مرخصی
        $post_user_all = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_user_object", $worker, $start_datetime, $end_datetime);
        $post_users = [];
        foreach ($post_user_all as $post_user) {
            $post_users[$post_user->id] = $post_user;
        }
        $post_users = array_values($post_users);
        session(["leave_overtime_request" => $request->all()]);

        // از آنجایی که تابع getCurrentPostByShiftWorkAndLeaveOvertime مقدار شروع و پایان را تغییر می دهد، یگ بار دیگر محاسبه می کنیم.
        $start_datetime = Carbon::parse($request->start_datetime);
        $end_datetime = Carbon::parse($request->end_datetime);

        $register_after_tacking = 0;
        if (Carbon::now()->greaterThan($start_datetime)) {

            $number_register_after_tacking = Worker::GetRegisterAfterTackingCount(Auth::id(), 1);
            if ($number_register_after_tacking + 1 < $post_users[0]->post->for_leave_number_of_register_after_tacking) {
                $register_after_tacking = 1;
            } else {
                return back()->withErrors("زمان شروع  نمی تواند قبل از زمان حال باشد.",);
            }
        }
        if ($start_datetime->greaterThan($end_datetime)) {
            return back()->withErrors("زمان پایان  نمی تواند از شروع مرخصی کوچکتر باشد.");
        }

        $replace_confirm_post_user = LeaveController::get_replace_confirm_post_user($post_users, $start_datetime, $end_datetime);

        if (count($replace_confirm_post_user["confirm_posts"]) == 0) {
            return back()->withErrors("در بازه زمانی انتخاب شده، شما در سازمان حضور ندارید، لذا نیاز به ثبت مرخصی (جایگزینی) نمی باشد.");
        }

        $message = LeaveController::check_has_replace_work($worker, $start_datetime, $end_datetime, 1);
        if ($message) {
            return back()->withErrors($message);
        }

        $message = LeaveController::check_has_any_leave_overtime($worker, $start_datetime, $end_datetime, 1);
        if ($message) {
            return back()->withErrors($message);
        }

        $message = LeaveController::check_start_and_end_date($post_users, $replace_confirm_post_user["confirm_posts"], $start_datetime, $end_datetime);
        if ($message != "") {

            return back()->withErrors($message);
        }

        session(["register_after_tacking" => $register_after_tacking]);
        if (count($replace_confirm_post_user["need_replace_work"]) == 0) {
            return $this->submit_replace_work($request);
        } else {

            return redirect()->route($this->route_path . "replace_work");
        }


    }

    public function replace_work()
    {
        $worker = Worker::find(Auth::id());
        $leave_overtime_request = session("leave_overtime_request");

        if (!$leave_overtime_request) {
            return redirect()->route($this->dashboard_route, [
                $worker->id,
                $worker->random
            ])->with(["success" => "لطفا یک بار دیگر جهت مرخصی اقدام نمایید."]);
        }


        $start_datetime = Carbon::parse($leave_overtime_request["start_datetime"]);
        $end_datetime = Carbon::parse($leave_overtime_request["end_datetime"]);


        // بررسی اینکه برای مرخصی برای چه پست هایی باید جانشین انتخاب کند
        $post_user_all = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_user_object", $worker, $start_datetime, $end_datetime);
        $post_users = [];
        foreach ($post_user_all as $post_user) {
            $post_users[$post_user->id] = $post_user;
        }

        if (count($post_users) == 0) {
            return redirect()->route($this->dashboard_route, [
                $worker->id,
                $worker->random
            ])->withErrors("هیچ پست سازمانی برای شما تعریف نشده است، لطفا با پشیتبانی تماس بگیرید.");
        }
        $start_datetime = Carbon::parse($leave_overtime_request["start_datetime"]);
        $end_datetime = Carbon::parse($leave_overtime_request["end_datetime"]);
        $replace_confirm_post_user = LeaveController::get_replace_confirm_post_user($post_users, $start_datetime, $end_datetime);

        $option_workers = [];

        foreach ($post_users as $post_user) {
            $option_worker = null;
            $option_worker[] = ["id" => "0", "text" => "لطفا یک نفر را انتخاب کنید", "value" => 0];
            if (in_array($post_user->id, $replace_confirm_post_user["need_replace_work"])) {
                $post_replace_ids = PostReplace::where("post_id", $post_user->post->id)->pluck("replace_post_id")->toArray();
                $replace_worker = Worker::join("post_user", "users.id", "user_id")->
                whereIn("post_id", $post_replace_ids)->
                where("user_id", "!=", $worker->id)->
                groupBy("user_id")->
                get();

                foreach ($replace_worker as $item) {
                    $option = [
                        "value" => $item->user_id,
                        "text" => $item->firstname . " " . $item->lastname
                    ];
                    $option_worker[] = $option;
                }
                $option_workers[$post_user->post_id] = ["option" => $option_worker, "post" => $post_user->post];
            }

        }

        return view($this->view_path . "replace_work", compact("leave_overtime_request", "worker", "option_workers"));

    }

    public function submit_replace_work(Request $request)
    {

        $worker = Worker::find(Auth::id());
        $start_datetime = Carbon::parse($request->start_datetime);
        $end_datetime = Carbon::parse($request->end_datetime);

        // بررسی اینکه برای مرخصی برای چه پست هایی باید جانشین انتخاب کند
        $post_user_all = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_user_object", $worker, $start_datetime, $end_datetime);
        $post_users = [];
        foreach ($post_user_all as $post_user) {
            $post_users[$post_user->id] = $post_user;
        }
        $post_users = array_values($post_users);

        // سال مالی
        $financial_year = Setting::FinancialYear();
        $financial_year_start = $financial_year["start_date_time"];
        $financial_year_end = $financial_year["last_date_time"];


        $leave_before_count = LeaveOvertime::where([
            "user_id" => $worker,
            "leave_overtime_type_id" => 2, //اضظراری
        ])->
        where("start_datetime", ">=", $financial_year_start)->
        where("end_datetime", "<=", $financial_year_end)->
        count();
        foreach ($post_users as $post_user) {

            if ($request->leave_overtime_type_id == 2 && $leave_before_count - 1 > $post_user->post->emergency_leave_number_in_year) {
                return back()->withErrors(
                    "با توجه به اینکه تعداد مرخضی های اضطراری " .
                    $post_users->post->emergency_leave_number_in_year .
                    " بار در سال می باشد، امکان ثبت مرخصی وجود ندارد. "
                );
            }

            if (!$post_user->post->parent_id) {
                return redirect()->route($this->dashboard_route)->withErrors("با توجه به اینکه پست مافوق برای " . $post_user->post->caption . " مشخص نشده است، امکان ثبت مرخصی برای شما امکان پذیر نیست.");
            }
        }

        // با توجه به اینکه مقدار ساعت تغییر می کند، دوباره آن را بروز می کنیم.
        $start_datetime = Carbon::parse($request->start_datetime);
        $end_datetime = Carbon::parse($request->end_datetime);

        $replace_confirm_post_user = LeaveController::get_replace_confirm_post_user($post_users, $start_datetime, $end_datetime);
        if (count($replace_confirm_post_user["confirm_posts"]) == 0) {
            return back()->withErrors("با توجه به زمان انتخاب شده، شما در این ساعت نبایستی در محل کار حاضر شوید، بنابراین نیاز به ثبت مرخصی ندارد.");
        }

        $leave_overtime_type = LeaveOvertimeType::where("id", $request->leave_overtime_type_id)->first();
        if (!$leave_overtime_type) {
            return redirect()->route($this->route_path . "index")->withErrors("نوع مرخصی مشخص نشده است، لطفا دوباره تلاش کنید.");
        }

        //چک کردن اینکه برای همه پست ها جانشین انتخاب کرده باشد
        foreach ($post_users as $post_user) {
            if (in_array($post_user->id, $replace_confirm_post_user["confirm_posts"])) {

                if ($post_user->post->for_leave_required_to_replace_person) {
                    $replace_user_id = "replace_user_id_" . $post_user->post_id;
                    if (!$request->$replace_user_id) {
                        return back()->withErrors("لطفا برای همه پست های خود حداقل یک جانشین انتخاب کنید.");
                    }
                }


            }
        }

        $start_datetime = Carbon::parse($request->start_datetime);
        $end_datetime = Carbon::parse($request->end_datetime);

        $register_after_tacking = session("register_after_tacking");
        $leave = LeaveOvertime::create([
            "user_id" => $worker->id,
            "leave_overtime_type_id" => $leave_overtime_type->id,
            "start_datetime" => $start_datetime,
            "end_datetime" => $end_datetime,
            "register_after_tacking" => $register_after_tacking ? 1 : 0,
        ]);

        $status_id = 4630002; // در انتظار تایید مافوق

        foreach ($post_users as $post_user) {
            if (in_array($post_user->id, $replace_confirm_post_user["confirm_posts"])) {

                if ($post_user->post->for_leave_required_to_replace_person) {
                    $status_id = 4630001; // در انتظار تایید جانشین
                }
                //تعداد سطح بالایی
                $time = $start_datetime->diffInHours($end_datetime);
                $for_leave_a_few_top_levels_must_confirm = $post_user->post->for_leave_a_few_top_levels_must_confirm;
                if ($time > $post_user->post->for_leave_a_few_top_levels_must_confirm_time) {
                    $for_leave_a_few_top_levels_must_confirm = $post_user->post->for_leave_a_few_top_levels_must_confirm_time_level;
                }

                $replace_user_id = "replace_user_id_" . $post_user->post_id;

                $leave_overtime_confirmation = LeaveOvertimeConfirmation::create([
                    "leave_overtime_id" => $leave->id,
                    "post_user_id" => $post_user->id,
                    "replace_user_id" => $post_user->post->for_leave_required_to_replace_person ? $request->$replace_user_id : null,
                    "number_top_levels_must_confirm" => $for_leave_a_few_top_levels_must_confirm,
                    "number_top_levels_confirmed" => 0,
                    "current_confirm_post_id" => $post_user->post->parent_id,
                    "status_id" => $post_user->post->for_leave_required_to_replace_person ? 4630001 : 4630002
                ]);

            }

        }

        $leave->status_id = $status_id;
        $leave->save();

        self::sendSmsReplace($leave, "مرخصی");
//
        $this->sendSmsParent($leave);


        event(new LeaveLogEvent($leave, 4630001, $request->text));

        session(["leave_overtime_request" => null]);

        $message = "یک درخواست مرخصی با موفقیت ثبت گردید.";
        if ($register_after_tacking) {
            $number_register_after_tacking_done = Worker::GetRegisterAfterTackingCount(Auth::id(), 1);
            $number_register_after_tacking = $post_users[0]->post->for_leave_number_of_register_after_tacking - $number_register_after_tacking_done - 1;
            $message = "یک درخواست مرخصی با موفقیت ثبت گردید." . "<br/>" . "توجه: شما می توانید حداکثر $number_register_after_tacking مرخصی دیگر پس از انجام مرخصی در سامانه ثبت نمایید. توجه فرمایید قبل از خروج از سازمان در زمان مرخصی، باید مرخصی شما ثبت و تایید شده باشد.";
        }
        return redirect()->route($this->dashboard_route, [
            $worker,
            $worker->random
        ])->with(["success" => $message]);
    }

    public function log(LeaveOvertime $leave_overtime)
    {


        return view($this->view_path . "log", compact("leave_overtime"));
    }

    public function confirm_replace_user(LeaveOvertime $leave_overtime)
    {
        $worker = Worker::find(Auth::id());

        $leave_overtime_confirmation = LeaveOvertimeConfirmation::where([
            "leave_overtime_id" => $leave_overtime->id,
            "status_id" => 4630001,
            "replace_user_id" => $worker->id
        ])->
        first();

        if (!$leave_overtime_confirmation) {
            return back()->withErrors("اطلاعات مرخصی یافت نشد، لطفا یکبار دیگر تلاس کنید.");
        }
        if ($leave_overtime_confirmation->replace_user_id != $worker->id) {
            return back()->withErrors("با توجه به اینکه شما جانشین مرخصی نیستید، امکان تایید برای شما وجود ندارد.");
        }
        if ($leave_overtime->status_id != 4630001) {
            return back()->withErrors("وضعیت مرخصی جهت تایید جانشین معتبر نمی باشد، لطفا با واحد منابع انسانی تماس بگیرید.");
        }

        $this->confirm_users($leave_overtime, "");
        $this->sendSmsParent($leave_overtime);

        return redirect()->back()->with(["success" => "تایید جانشین مرخصی با موفقیت ثبت گردید"]);
    }

    public function reject_replace_user(LeaveOvertime $leave_overtime)
    {
        $worker = Worker::find(Auth::id());

        $list = LeaveOvertimeConfirmation::where(["leave_overtime_id" => $leave_overtime->id])->pluck("replace_user_id")->toArray();
        $list[] = -1;
        if (!in_array($worker->id, $list)) {
            return back()->withErrors("با توجه به اینکه شما جانشین مرخصی نیستید، امکان تایید برای شما وجود ندارد.");
        }
        if ($leave_overtime->status_id != 4630001) {
            return back()->withErrors("وضعیت مرخصی جهت تایید جانشین معتبر نمی باشد، لطفا با واحد منابع انسانی تماس بگیرید.");
        }


        $leave_overtime->status_id = 4630008;// عدم تایید جانشین
        $leave_overtime->save();

        $post_user = PostUser::where("user_id", $worker->id)->first();

        event(new LeaveLogEvent($leave_overtime, 4630005, ""));

        return redirect()->back()->with(["success" => "عدم تایید جانشین مرخصی با موفقیت ثبت گردید"]);
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
            return back()->withErrors("شناسه مرخصی نامعتبر است، لطفا یکبار دیگر تلاش کنید.");
        }
        if ($leave_overtime->status_id != 4630002) {
            return back()->withErrors("وضعیت مرخصی جهت تایید مافوق معتبر نمی باشد، لطفا با واحد منابع انسانی تماس بگیرید.");
        }

//return $request->all();
        switch ($request->type) {
            case "confirm":

                $this->confirm_users($leave_overtime, $request->comment);
                $this->sendSmsParent($leave_overtime);

                return redirect()->back()->with(["success" => "تایید  مرخصی با موفقیت ثبت گردید"]);
                break;
            case "reject":

                $leave_overtime->status_id = 4630004;//  عدم تایید مافوق
                $leave_overtime->save();

                event(new LeaveLogEvent($leave_overtime, 4630004, $request->comment));

                return redirect()->back()->with(["success" => "عدم تایید  مرخصی با موفقیت ثبت گردید"]);

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

                return redirect()->back()->with(["success" => "اخذ توضیحات  مرخصی با موفقیت ثبت گردید"]);
                break;
        }


        return redirect()->back()->withErrors("درخواست نامعتبر است.");

    }

    public function set_user_comment(Request $request)
    {

        $worker = Worker::find(Auth::id());

        $leave = LeaveOvertime::find($request->leave_overtime_id);
        if (!$leave) {
            return back()->withErrors("شناسه مرخصی نامعتبر است، لطفا یکبار دیگر تلاش کنید.");
        }

        if ($leave->status_id != 4630009) {
            return back()->withErrors("وضعیت مرخصی جهت ثبت توضیحات معتبر نمی باشد، لطفا با واحد منابع انسانی تماس بگیرید.");
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
            return back()->withErrors("این مرخصی توسط شما ثبت نشده است لذا امکان کنسل کردن آن وجود ندارد.");
        }

        if (in_array($leave_overtime->status_id, [4630003, 4630004, 4630005, 4630007, 4630006, 4630008])) {
            return back()->withErrors("با توجه به اینکه مرخصی انجام شده(تایید شده) است، امکان کنسل کردن آن وجود ندارد.");
        }


        $leave_overtime->status_id = 4630005;// انصراف
        $leave_overtime->save();


        event(new LeaveLogEvent($leave_overtime, 4630011, ""));

        return redirect()->back()->with(["success" => "کنسل کردن مرخصی با موفقیت ثبت گردید"]);
    }

    public function sendSmsParent(LeaveOvertime $leave_overtime, $template = null)
    {

        $company_name = Setting::getStringValue("company_name");
        $hr_leave_conform_parent_sms = Setting::getIntegerValue("hr_leave_conform_parent_sms");
        if (!$hr_leave_conform_parent_sms) {
            return false;
        }

        $list = LeaveOvertimeConfirmation::
        where([
            "leave_overtime_id" => $leave_overtime->id,
            "status_id" => 4630002 // در انتظار تایید مافوق
        ])->
        get();

        $replace_count = LeaveOvertimeConfirmation::where([
            "leave_overtime_id" => $leave_overtime->id,
            "status_id" => 4630001 // در انتظار تایید جانشین
        ])->
        count();
        // اگر حداقل یک جانشین وجود دارد که تایید نشده است، پیامک برای مدیر ارسال نشود.
        if ($replace_count > 0) {
            return;
        }
        $worker_ids = [];

        foreach ($list as $leave_overtime_confirmation) {

            // اگر تعداد تایید ها به حد نصاب نرسیده برای آخرین پست پیامک ارسال می شود.
            if ($leave_overtime_confirmation->number_top_levels_confirmed < $leave_overtime_confirmation->number_top_levels_must_confirm) {


                $post_users = PostUser::where("post_id", $leave_overtime_confirmation->current_confirm_post_id)->get();
                $token = $company_name;

                foreach ($post_users as $post_user) {
                    // ممکن است یک نفر دو پست داشته باشد و مافوق هر دو پست یک نفر باشد، بنابراین نیاز نیست 2 پیامک ارسال شود.
                    if (!isset($worker_ids[$post_user->user_id])) {
                        $token3 = "_APP_NAME_" . "/Personal/" . ($post_user->worker->id ?? "") . "/" . ($post_user->worker->random ?? "");

                        Notification::send("00" . ($post_user->worker->mobile_country->area_code ?? "98") . $post_user->worker->mobile,
                            new SMSNotification($template ?? "hrleaveconfirmparentpost",
                                $token,
                                "",
                                $token3,
                                $leave_overtime_confirmation->leave_overtime->leave_overtime_type->caption,
                                $leave_overtime_confirmation->leave_overtime->worker->fullname())
                        );
                    }

                    $worker_ids[$post_user->user_id] = 1;
                }
            }

        }


    }

    public static function sendSmsReplace(LeaveOvertime $leave_overtime, $token2 = "***")
    {

        $hr_leave_conform_parent_sms = Setting::getIntegerValue("hr_leave_confirm_replace_sms");
        if (!$hr_leave_conform_parent_sms) {
            return false;
        }
        $company_name = Setting::getStringValue("company_name");

        $list = LeaveOvertimeConfirmation::where([
            "leave_overtime_id" => $leave_overtime->id,
            "status_id" => 4630001
        ])->get();

        foreach ($list as $leave_overtime_confirmation) {
            $replace_worker = $leave_overtime_confirmation->replace_worker;
            if ($replace_worker) {
                $token = $company_name;
                $token3 = "_APP_NAME_" . "/Personal/" . ($replace_worker->id ?? "") . "/" . ($replace_worker->random ?? "");
                Notification::send("00" . ($replace_worker->mobile_country->area_code ?? "98") . $replace_worker->mobile,
                    new SMSNotification("hrleaveconfirmreplace",
                        $token,
                        $token2,
                        $token3,
                        $replace_worker->fullname(),
                        $leave_overtime_confirmation->leave_overtime->worker->fullname()));

            }
        }
    }

    public function confirm_users(LeaveOvertime $leave_overtime, $text)
    {

        $worker = Worker::find(Auth::id());
        $add_log_replace = false;
        $add_log_parent = false;

        //اگر جانشینی منتظر تایید کاربر هست، آن را تایید و در انتظار تایید مافوق قرار بده
        $list = LeaveOvertimeConfirmation::where([
            "leave_overtime_id" => $leave_overtime->id,
            "status_id" => 4630001,
            "replace_user_id" => $worker->id
        ])->
        get();

        foreach ($list as $leave_overtime_confirmation) {
            $leave_overtime_confirmation->status_id = 4630002;
            $leave_overtime_confirmation->save();
            $add_log_replace = true;

        }

        // در صورتی که همه جانشین ها تایید کرده اند، وضعیت را در انتظار تایید مافوق قرار بده در غیر اینصورت ادامه نده
        $count_replace_confirm = LeaveOvertimeConfirmation::where([
            "leave_overtime_id" => $leave_overtime->id,
            "status_id" => 4630001
        ])->
        count();

        if ($count_replace_confirm == 0 && $leave_overtime->status_id == 4630001) {

            $leave_overtime->status_id = 4630002; // در انتظار تایید مافوق
            $leave_overtime->save();

        }


        // از بین آنهایی که تایید کرده اند، پست آنها را برگردان
        $user_ids = LeaveOvertimeLog::where("leave_overtime_id", $leave_overtime->id)->pluck("user_id")->toArray();
        // همه پست هایی که کاربر الان دارد.
        $worker_post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids_with_out_menu", $worker);

        $post_ids = PostUser::whereIn("user_id", $user_ids)->pluck("post_id")->toArray();
        $post_ids = array_merge($post_ids, $worker_post_ids);

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

                $add_log_parent = true;
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

            Script1023Controller::calculateForDays($leave_overtime->worker, $leave_overtime->start_datetime, $leave_overtime->end_datetime);
        }

        if ($add_log_replace) {
            event(new LeaveLogEvent($leave_overtime, 4630002, $text));
        }
        if ($add_log_parent) {
            event(new LeaveLogEvent($leave_overtime, 4630003, $text));
        }


    }

    public static function get_replace_confirm_post_user($post_users, $start_datetime, $end_datetime)
    {
        // بررسی پست هایی که با توجه به زمان مرخصی نیاز به جانشین دارند
        $replace_confirm_post_users = [
            "confirm_posts" => [],
            "need_replace_work" => []
        ];
//        return $start_datetime ->format("Y-m-d H:i:s");
        // بررسی اینکه با توجه به زمان مرخصی
        foreach ($post_users as $post_user) {

            $count = 0;
            // بررسی نقطه شروع
            // if ( $post_user->id == 112 ) {
            $count += ShiftWorkDay::where([
                "shift_id" => $post_user->post->shift_id,
                "shift_work_id" => $post_user->shift_work_id
            ])->
            where("start_datetime", "<=", $start_datetime)->
            where("end_datetime", ">", $start_datetime)->count();
            //  }

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
            // echo $post_user->id . "=>" . $count;
            if ($count > 0) {
                $replace_confirm_post_users["confirm_posts"][] = $post_user->id;
                if ($post_user->post->for_leave_required_to_replace_person) {
                    $replace_confirm_post_users["need_replace_work"][] = $post_user->id;
                }
            }

        }

        return $replace_confirm_post_users;
    }

    public static function check_start_and_end_date($post_users, $post_user_confirm, $start_datetime, $end_datetime, $type_caption = "مرخصی")
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
            $message .= "اولین ساعت شروع کار شما  " . $time . " می باشد، لذا قبل از آن نیاز به ثبت " . $type_caption .
                " نمی باشد، " . "<br/>" . "لطفا زمان شروع " . $type_caption
                . " را ویرایش کنید." . "<br/>";
        }
        if ($end_pas_count == 0) {
            $time = jdate(Carbon::parse($end_datetime_for_work)->timestamp)->format(" ساعت H:i:s  %A  Y/m/d  ");
            $message .= "آخرین ساعت حضور شما در سازمان " . $time . " می باشد، لذا برای بعد از آن نیاز به ثبت " . $type_caption .
                " نمی باشد. " . "<br/>" . "لطفا زمان پایان " . $type_caption .
                " را ویرایش کنید.";
        }

        return $message;
    }

    public static function check_has_replace_work($worker, $start_datetime, $end_datetime, $leave_overtime_group_id)
    {
        // با توجه به اینکه ممکن است فردی در جانشینی بخواهد مرخصی ثبت کند، بنابراین این شرط کلا برای افراد حذف گردید.
        return null;

//        $leave = LeaveOvertime::
//        join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
//        join("leave_overtime_confirmation", "leave_overtimes.id", "leave_overtime_id")->
//        whereIn("leave_overtime_group_id", [1, 4])->// مرخصی و جایگزنینی
//        whereIn("leave_overtimes.status_id", [4630003, 4630007, 4630006])->
//        where("replace_user_id", $worker->id)->
//        where("start_datetime", "<", $start_datetime)->
//        where("end_datetime", ">", $start_datetime)->first();
//
//        $leave_overtime_group_caption = LeaveOvertimeGroup::find($leave_overtime_group_id)->caption;
//        if ($leave) {
//            return "شما از  " .
//                jdate(Carbon::parse($leave->start_datetime)->timestamp)->format(" ساعت H:i:s  %A  Y/m/d  ")
//                . " تا  " .
//                jdate(Carbon::parse($leave->end_datetime)->timestamp)->format(" ساعت H:i:s  %A  Y/m/d  ")
//                . " جانشین آقا/خانم " .
//                $leave->worker->fullname()
//                . " می باشید، لذا نمی توانید در این ساعت " .
//                $leave_overtime_group_caption
//                . " ثبت نمایید، لطفا تاریخ شروع را اصلاح نمایید.";
//
//        }
//
//        $leave = LeaveOvertime::
//        join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
//        join("leave_overtime_confirmation", "leave_overtimes.id", "leave_overtime_id")->
//        whereIn("leave_overtime_group_id", [1, 4])->// مرخصی و جایگزنینی
//        whereIn("leave_overtimes.status_id", [4630003, 4630007, 4630006])->
//        where("replace_user_id", $worker->id)->
//        where("start_datetime", "<", $end_datetime)->
//        where("end_datetime", ">", $end_datetime)->first();
//
//        if ($leave) {
//            return "شما از  " .
//                jdate(Carbon::parse($leave->start_datetime)->timestamp)->format(" ساعت H:i:s  %A  Y/m/d  ")
//                . " تا  " .
//                jdate(Carbon::parse($leave->end_datetime)->timestamp)->format(" ساعت H:i:s  %A  Y/m/d  ")
//                . " جانشین آقا/خانم " .
//                $leave->worker->fullname()
//                . " می باشید، لذا نمی توانید در این ساعت " . $leave_overtime_group_caption . " ثبت نمایید، لطفا تاریخ پایان را اصلاح نمایید.";
//
//        }
//
//        return null;
    }

    public static function check_has_any_leave_overtime($worker, $start_datetime, $end_datetime, $leave_overtime_group_id)
    {

        $leave = LeaveOvertime::
        whereIn("leave_overtimes.status_id", [4630003, 4630007, 4630006])->
        where("leave_overtime_type_id", "!=", 501)-> // اگر جانشینی  غیبت بود نباید چک کند
        where("user_id", $worker->id)->
        where("start_datetime", "<", $start_datetime)->
        where("end_datetime", ">", $start_datetime)->
        first();

        $leave_overtime_group_caption = LeaveOvertimeGroup::find($leave_overtime_group_id)->caption;
        if ($leave) {
            return "شما از  " .
                jdate(Carbon::parse($leave->start_datetime)->timestamp)->format(" ساعت H:i:s  %A  Y/m/d  ")
                . " تا  " .
                jdate(Carbon::parse($leave->end_datetime)->timestamp)->format(" ساعت H:i:s  %A  Y/m/d  ") .
                " یک " .
                $leave->leave_overtime_type->caption
                . " ثبت نموده اید ، لذا نمی توانید در این ساعت " .
                $leave_overtime_group_caption
                . " ثبت نمایید، لطفا تاریخ شروع را اصلاح نمایید.";

        }

        $leave = LeaveOvertime::
        whereIn("leave_overtimes.status_id", [4630003, 4630007, 4630006])->
        where("leave_overtime_type_id", "!=", 501)-> // اگر جانشینی  غیبت بود نباید چک کند
        where("user_id", $worker->id)->
        where("start_datetime", "<", $end_datetime)->
        where("end_datetime", ">", $end_datetime)->first();

        if ($leave) {
            return "شما از  " .
                jdate(Carbon::parse($leave->start_datetime)->timestamp)->format(" ساعت H:i:s  %A  Y/m/d  ")
                . " تا  " .
                jdate(Carbon::parse($leave->end_datetime)->timestamp)->format(" ساعت H:i:s  %A  Y/m/d  ") .
                " یک " .
                $leave->leave_overtime_type->caption
                . " ثبت نموده اید ، لذا نمی توانید در این ساعت " .
                $leave_overtime_group_caption
                . " ثبت نمایید، لطفا تاریخ پایان را اصلاح نمایید.";

        }

        return null;
    }
}
