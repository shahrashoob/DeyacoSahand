<?php

namespace App\Http\Controllers\HR\Personal;

use App\Events\HR\LeaveLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Post\PostReplace;
use App\Models\Post\PostUser;
use App\Models\HR\LeaveOvertime\LeaveOvertime;
use App\Models\HR\LeaveOvertime\LeaveOvertimeConfirmation;
use App\Models\HR\LeaveOvertime\LeaveOvertimeLog;
use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ReplacementController extends Controller
{
    public static $info = [
        "route" => "hr.personal.replacement.",
        "enable_status" => ["001", "003", "005", "006", "008","009","010","011"],
        "button" => [
            "caption" => "ثبت درخواست جابجایی شیفت",
            "class" => "btn btn-primary text-white",
            "icon" => "feather icon-log-in"
        ],
        "view_path" => "hr.personal.replacement.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "hr.personal.index";

    public function __construct()
    {
        $this->route_path = ReplacementController::$info["route"];
        $this->view_path = ReplacementController::$info["view_path"];
    }

    public function index()
    {


        $worker = Worker::find(Auth::id());
        $replacement_request = session("replacement_session");
        if (!$replacement_request) {
            $replacement_request = [
                "leave_overtime_type_id" => 400,
                "start_datetime" => null,
                "end_datetime" => null,
                "text" => "",

                // اطلاعات ساعت برگشت جایگزینی
                "start_datetime_return" => null,
                "end_datetime_return" => null,

            ];

        }
        $post_users = PostUser::where("user_id", $worker->id)->groupBy("post_id")->get();

        if (count($post_users) == 0) {
            return redirect()->route($this->dashboard_route, [$worker->id, $worker->random])->
            withErrors("هیچ پست سازمانی برای شما تعریف نشده است، لطفا با واحد منابع انسانی تماس بگیرید . ");
        }

        $text = "";
        foreach ($post_users as $post_user) {
            if (!$post_user->post->shift) {
                $text .= $post_user->post->caption . ", ";
            }
        }
        if ($text != "") {
            return redirect()->route($this->dashboard_route, [$worker->id, $worker->random])->
            withErrors("با توجه به اینکه برای پست های زیر شیفت مشخص نشده است، امکان ثبت اضافه کاری وجود ندارد، لطفا با واحد منابع انسانی تماس بگیرید . " . " < br />" . $text);
        }

        $post_users_not_shift_work = PostUser::where("user_id", $worker->id)->whereNull("shift_work_id")->get();
        $text = "";
        if (count($post_users_not_shift_work) != 0) {

            foreach ($post_users_not_shift_work as $item) {
                $text .= $item->post->caption . ", ";
            }

            return redirect()->route($this->dashboard_route, [$worker->id, $worker->random])->
            withErrors("گروه شیفت برای پست های زیر مشخص نشده است، لطفا با واحد منابع انسانی تماس بگیرید . " . " < br />" . $text);
        }


        return view($this->view_path . "index", compact("worker", "replacement_request"));
    }

    public function submit(Request $request)
    {


        $worker = Worker::find(Auth::id());
        $post_users = PostUser::where("user_id", $worker->id)->groupBy("post_id")->get();

        $start_datetime = Carbon::parse($request->start_datetime);
        $end_datetime = Carbon::parse($request->end_datetime );

        session(["replacement_session" => $request->all()]);


        if (Carbon::now()->greaterThan($start_datetime)) {
            return back()->withErrors("زمان شروع جانشینی نمی تواند قبل از زمان حال باشد . ",);
        }
        if ($start_datetime->greaterThan($end_datetime)) {
            return back()->withErrors("زمان پایان جانشینی نمی تواند از  شروع جانشینی کوچکتر باشد . ");
        }

        // زمان برگشت
        $start_datetime_return = Carbon::parse($request->start_datetime_return);
        $end_datetime_return = Carbon::parse($request->end_datetime_return);

        if (Carbon::now()->greaterThan($start_datetime_return)) {
            return back()->withErrors("زمان شروع جانشینی نمی تواند قبل از زمان حال باشد . ",);
        }
        if ($start_datetime_return->greaterThan($end_datetime_return)) {
            return back()->withErrors("زمان پایان جانشینی نمی تواند از  شروع جانشینی کوچکتر باشد . ");
        }

        $replace_confirm_post_user = LeaveController::get_replace_confirm_post_user($post_users, $start_datetime, $end_datetime);

//        if ( count( $replace_confirm_post_user["confirm_posts"] ) == 0 ) {
//            return back()->withErrors( "در بازه زمانی انتخاب شده، شما در سازمان حضور ندارد، لذا نیاز به ثبت جایگزین نمی باشد . " );
//        }

        $message = LeaveController::check_has_replace_work($worker, $start_datetime, $end_datetime, 1);
        if ($message) {
            return back()->withErrors($message);
        }

        $message = LeaveController::check_has_any_leave_overtime($worker, $start_datetime, $end_datetime, 1);
        if ($message) {
            return back()->withErrors($message);
        }

        $message = LeaveController::check_start_and_end_date($post_users, $replace_confirm_post_user["confirm_posts"], $start_datetime, $end_datetime, "جایگزینی");
        if ($message != "") {
            return back()->withErrors($message);
        }
        if (count($replace_confirm_post_user["need_replace_work"]) == 0) {
            return back()->withErrors("با توجه به اینکه پست شما نیاز به جانشین ندارد، بنابراین  ثبت جایگزینی امکان پذیر نیست . ");
        } else {

            return redirect()->route($this->route_path . "replace_work");
        }


    }

    public function replace_work()
    {
        $worker = Worker::find(Auth::id());

        $replacement_request = session("replacement_session");

        if (!$replacement_request) {
            return redirect()->route($this->dashboard_route, [
                $worker->id,
                $worker->random
            ])->withErrors("لطفا یک بار دیگر جهت جایگزینی اقدام نمایید . ");
        }


        $start_datetime = Carbon::parse($replacement_request["start_datetime"] );
        $end_datetime = Carbon::parse($replacement_request["end_datetime"]);


        $start_datetime_return = Carbon::parse($replacement_request["start_datetime_return"]);
        $end_datetime_return = Carbon::parse($replacement_request["end_datetime_return"]);


        // لیست افراد جابجایی شیفت نهایی
        $final_user_ids = [];

        // لیست افرادی که می توانند جانشین worker شوند
        $user_ids = $this->getUserReplacement($worker->id, $start_datetime, $end_datetime);
        $listx = [];
// به ازای هر فرد بررسی می کند worker می تواند برای آنها در برگشت، جابجایی شیفت شود یا خیر
        foreach ($user_ids as $user_id) {
            $return_user_ids = $this->getUserReplacement($user_id, $start_datetime_return, $end_datetime_return);
            $listx[$user_id] = $return_user_ids;
            if (in_array($worker->id, $return_user_ids)) {
                $final_user_ids[] = $user_id;
            }
        }

        $option_worker[] = ["id" => "0", "text" => "لطفا یک نفر را انتخاب کنید", "value" => 0];
        $userList = User::whereIn("id", $final_user_ids)->get();

        foreach ($userList as $item) {
            $option = [
                "value" => $item->id,
                "text" => $item->firstname . " " . $item->lastname
            ];
            $option_worker[] = $option;
        }

        if (count($userList) == 0) {
            return back()->withErrors("در ساعت انتخاب شده برای همکار، هیچ فردی نمی تواند جانشین شما باشد.");
        }

        return view($this->view_path . "replace_work", compact("replacement_request", "worker", "option_worker"));

    }

    public function submit_replace_work(Request $request)
    {

        $worker = Worker::find(Auth::id());
        $post_users = PostUser::where("user_id", $worker->id)->get();

        $financial_year_start = Carbon::now()->format("Y/") . Setting::getStringValue("financial_year_start");
        $financial_year_start = Carbon::parse($financial_year_start);
        $financial_year_end = Carbon::now()->format("Y/") . Setting::getStringValue("financial_year_end");
        $financial_year_end = Carbon::parse($financial_year_end);


        $start_datetime = Carbon::parse($request->start_datetime );
        $end_datetime = Carbon::parse($request->end_datetime );

        $start_datetime_return = Carbon::parse($request->start_datetime_return);
        $end_datetime_return = Carbon::parse($request->end_datetime_return );


        foreach ($post_users as $post_user) {

            if (!$post_user->post->parent_id) {
                return redirect()->route($this->dashboard_route)->withErrors("با توجه به اینکه پست مافوق برای " . $post_user->post->caption . " مشخص نشده است، امکان ثبت جابجایی شیفت برای شما امکان پذیر نیست . ");
            }

        }

        $replace_confirm_post_user = LeaveController::get_replace_confirm_post_user($post_users, $start_datetime, $end_datetime);
        if (count($replace_confirm_post_user["confirm_posts"]) == 0) {
            return back()->withErrors("با توجه به زمان انتخاب شده، شما در این ساعت نبایستی در محل کار حاضر شوید، بنابراین نیاز به ثبت جابجایی شیفت ندارد . ");
        }

        foreach ($post_users as $post_user) {

            if (!$post_user->post->parent_id) {
                return redirect()->route($this->dashboard_route)->withErrors("با توجه به اینکه پست مافوق برای " . $post_user->post->caption . " مشخص نشده است، امکان ثبت جابجایی شیفت برای شما امکان پذیر نیست . ");
            }


        }


        $leave = LeaveOvertime::create([
            "user_id" => $worker->id,
            "leave_overtime_type_id" => 400,
            "start_datetime" => $start_datetime,
            "end_datetime" => $end_datetime,
            "start_datetime_return" => $start_datetime_return,
            "end_datetime_return" => $end_datetime_return,
        ]);

        $status_id = 4630002; // در انتظار تایید مافوق

        foreach ($post_users as $post_user) {
            if (in_array($post_user->id, $replace_confirm_post_user["confirm_posts"])) {
                $status_id = 4630001; // در انتظار تایید جانشین
                //تعداد سطح بالایی
                $time = $start_datetime->diffInHours($end_datetime);
                $for_replacement_a_few_top_levels_must_confirm = $post_user->post->for_replacement_a_few_top_levels_must_confirm;
                if ($time > $post_user->post->for_replacement_a_few_top_levels_must_confirm_time) {
                    $for_replacement_a_few_top_levels_must_confirm = $post_user->post->for_replacement_a_few_top_levels_must_confirm_time_level;
                }


                $leave_overtime_confirmation = LeaveOvertimeConfirmation::create([
                    "leave_overtime_id" => $leave->id,
                    "post_user_id" => $post_user->id,
                    "replace_user_id" => $request->replace_user_id,
                    "number_top_levels_must_confirm" => $for_replacement_a_few_top_levels_must_confirm,
                    "number_top_levels_confirmed" => 0,
                    "current_confirm_post_id" => $post_user->post->parent_id,
                    "status_id" => 4630001
                ]);

            }

        }

        $leave->status_id = $status_id;
        $leave->save();

        $this->sendSmsReplace($leave);
//
        $this->sendSmsParent($leave);


        event(new LeaveLogEvent($leave, 4630001, $request->text));

        session(["replacement_session" => null]);

        return redirect()->route($this->dashboard_route, [
            $worker,
            $worker->random
        ])->with(["success" => "یک درخواست جابجایی شیفت با موفقیت ثبت گردید . "]);
    }

    public function sendSmsParent(LeaveOvertime $leave_overtime, $template = null)
    {

        $company_name = Setting::getStringValue("company_name");
        $hr_leave_conform_parent_sms = Setting::getIntegerValue("hr_replacement_confirm_parent_sms");
        if (!$hr_leave_conform_parent_sms) {
            return false;
        }
        $worker_ids = [];
        $leave_overtime_confirmation = LeaveOvertimeConfirmation::where([
            "leave_overtime_id" => $leave_overtime->id,
            "status_id" => 4630002
        ])->first();

        // اگر تعداد تایید ها به حد نصاب نرسیده برای آخرین پست پیامک ارسال می شود.
        if ($leave_overtime_confirmation && $leave_overtime_confirmation->number_top_levels_confirmed < $leave_overtime_confirmation->number_top_levels_must_confirm) {


            $post_users = PostUser::where("post_id", $leave_overtime_confirmation->current_confirm_post_id)->get();
            $token = $company_name;

            foreach ($post_users as $post_user) {
                // ممکن است یک نفر دو پست داشته باشد و مافوق هر دو پست یک نفر باشد، بنابراین نیاز نیست 2 پیامک ارسال شود.
                if (isset($worker_ids[$post_user->user_id])) {

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

    public function sendSmsReplace(LeaveOvertime $leave_overtime)
    {

        $hr_leave_conform_parent_sms = Setting::getIntegerValue("hr_replacement_confirm_replace_sms");
        if (!$hr_leave_conform_parent_sms) {
            return false;
        }
        $company_name = Setting::getStringValue("company_name");

        $leave_overtime_confirmation = LeaveOvertimeConfirmation::where([
            "leave_overtime_id" => $leave_overtime->id,
            "status_id" => 4630001
        ])->first();


        $replace_worker = $leave_overtime_confirmation->replace_worker;

        $token = $company_name;
        $token3 = "_APP_NAME_" . "/Personal/" . ($replace_worker->id ?? "") . "/" . ($replace_worker->random ?? "");
        Notification::send("00" . ($replace_worker->mobile_country->area_code ?? "98") . $replace_worker->mobile,
            new SMSNotification("hrreplacementconfirmreplace",
                $token,
                "",
                $token3,
                $replace_worker->fullname(),
                $leave_overtime_confirmation->leave_overtime->worker->fullname()));


    }

    public function cancel(LeaveOvertime $leave_overtime)
    {

        $worker = Worker::find(Auth::id());

        if ($leave_overtime->user_id != $worker->id) {
            return back()->withErrors("این جابجایی شیفت توسط شما ثبت نشده است لذا امکان کنسل کردن آن وجود ندارد . ");
        }

        if (in_array($leave_overtime->status_id, [4630003, 4630004, 4630005, 4630007, 4630006, 4630008])) {
            return back()->withErrors("با توجه به اینکه جابجایی شیفت انجام شده( تایید شده) است، امکان کنسل کردن آن وجود ندارد . ");
        }


        $leave_overtime->status_id = 4630005;// انصراف
        $leave_overtime->save();


        event(new LeaveLogEvent($leave_overtime, 4630011, ""));

        return redirect()->back()->with(["success" => "کنسل کردن جابجایی شیفت با موفقیت ثبت گردید"]);
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
            return back()->withErrors("اطلاعات جابجایی شیفت یافت نشد، لطفا یکبار دیگر تلاس کنید . ");
        }
        if ($leave_overtime_confirmation->replace_user_id != $worker->id) {
            return back()->withErrors("با توجه به اینکه شما جانشین جابجایی شیفت نیستید، امکان تایید برای شما وجود ندارد . ");
        }
        if ($leave_overtime->status_id != 4630001) {
            return back()->withErrors("وضعیت جابجایی شیفت جهت تایید جانشین معتبر نمی باشد، لطفا با واحد منابع انسانی تماس بگیرید . ");
        }

        $result = $this->confirm_users($leave_overtime, "");
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $this->sendSmsParent($leave_overtime);

        return redirect()->back()->with(["success" => "تایید جانشین جابجایی شیفت با موفقیت ثبت گردید"]);
    }

    public function confirm_parent_post(Request $request)
    {

        $worker = Worker::find(Auth::id());

        $post_ids = PostUser::where("user_id", $worker->id)->pluck("post_id")->toArray();

        $post_user = PostUser::where("user_id", $worker->id)->whereIn("post_id", $post_ids)->first();

        if (!$post_user) {
            return back()->withErrors("با توجه به پست های سازمانی شما، امکان تایید برای شما وجود ندارد . ");
        }

        $leave_overtime = LeaveOvertime::find($request->leave_overtime_id);

        if (!$leave_overtime) {
            return back()->withErrors("شناسه جابجایی شیفت نامعتبر است، لطفا یکبار دیگر تلاش کنید . ");
        }
        if ($leave_overtime->status_id != 4630002) {
            return back()->withErrors("وضعیت جابجایی شیفت جهت تایید مافوق معتبر نمی باشد، لطفا با واحد منابع انسانی تماس بگیرید . ");
        }

//return $request->all();
        switch ($request->type) {
            case "confirm":

                $result = $this->confirm_users($leave_overtime, $request->comment);
                if (!$result["result"]) {
                    return back()->withErrors($result["error"]);
                }
                $this->sendSmsParent($leave_overtime);

                return redirect()->back()->with(["success" => "تایید  جابجایی شیفت با موفقیت ثبت گردید"]);
                break;
            case "reject":

                $leave_overtime->status_id = 4630004;//  عدم تایید مافوق
                $leave_overtime->save();

                event(new LeaveLogEvent($leave_overtime, 4630004, $request->comment));

                return redirect()->back()->with(["success" => "عدم تایید  جابجایی شیفت با موفقیت ثبت گردید"]);

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

                return redirect()->back()->with(["success" => "اخذ توضیحات  جابجایی شیفت با موفقیت ثبت گردید"]);
                break;
        }


        return redirect()->back()->withErrors("درخواست نامعتبر است . ");

    }

    public function reject_replace_user(LeaveOvertime $leave_overtime)
    {
        $worker = Worker::find(Auth::id());

        $list = LeaveOvertimeConfirmation::where(["leave_overtime_id" => $leave_overtime->id])->pluck("replace_user_id")->toArray();
        $list[] = -1;
        if (!in_array($worker->id, $list)) {
            return back()->withErrors("با توجه به اینکه شما جانشین جابجایی شیفت نیستید، امکان تایید برای شما وجود ندارد . ");
        }
        if ($leave_overtime->status_id != 4630001) {
            return back()->withErrors("وضعیت جابجایی شیفت جهت تایید جانشین معتبر نمی باشد، لطفا با واحد منابع انسانی تماس بگیرید . ");
        }


        $leave_overtime->status_id = 4630008;// عدم تایید جانشین
        $leave_overtime->save();

        $post_user = PostUser::where("user_id", $worker->id)->first();

        event(new LeaveLogEvent($leave_overtime, 4630005, ""));

        return redirect()->back()->with(["success" => "عدم تایید جانشین جابجایی شیفت با موفقیت ثبت گردید"]);
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

            $leave_overtime_confirmation_return = LeaveOvertimeConfirmation::
            where("leave_overtime_id", $leave_overtime->id)->
            whereNotNull("replace_user_id")->
            first();
            if (!$leave_overtime_confirmation_return) {
                return [
                    "result" => false,
                    "error" => "جانشین شما در سامانه یافت نشد، لطفا با پشتیبانی تماس بگیرید . "
                ];
            }

            $leave_overtime->status_id = 4630003;
            $leave_overtime->save();

            $this->create_replacement_return($leave_overtime, $leave_overtime_confirmation_return);

            $company_name = Setting::getStringValue("company_name");
            Notification::send("00" . ($leave_overtime->worker->mobile_country->area_code ?? "98") . $leave_overtime->worker->mobile,
                new SMSNotification("hrleaveend", $company_name, null, null, $leave_overtime->leave_overtime_type->caption));

        }

        if ($add_log_replace) {
            event(new LeaveLogEvent($leave_overtime, 4630002, $text));
        }
        if ($add_log_parent) {
            event(new LeaveLogEvent($leave_overtime, 4630003, $text));
        }

        return ["result" => true];
    }

    public function set_user_comment(Request $request)
    {

        $worker = Worker::find(Auth::id());

        $leave = LeaveOvertime::find($request->leave_overtime_id);
        if (!$leave) {
            return back()->withErrors("شناسه جابجایی شیفت نامعتبر است، لطفا یکبار دیگر تلاش کنید . ");
        }

        if ($leave->status_id != 4630009) {
            return back()->withErrors("وضعیت جابجایی شیفت جهت ثبت توضیحات معتبر نمی باشد، لطفا با واحد منابع انسانی تماس بگیرید . ");
        }


        $leave->status_id = 4630002;// در انتظار تایید مافوق
        $leave->save();

        event(new LeaveLogEvent($leave, 4630010, $request->comment));


        $this->sendSmsParent($leave, "hrleavesetusercommentalert");


        return redirect()->back()->with(["success" => "توضیحات با موفقیت ثبت گردید"]);
    }

    public function create_replacement_return(LeaveOvertime $leave_overtime, $leave_overtime_confirmation_return)
    {


        $leave = LeaveOvertime::create([
            "user_id" => $leave_overtime_confirmation_return->replace_user_id,
            "leave_overtime_type_id" => 401, // برگشت جابجایی شیفت
            "start_datetime" => $leave_overtime->start_datetime_return,
            "end_datetime" => $leave_overtime->end_datetime_return,
            "replacement_leave_overtime_id" => $leave_overtime->id
        ]);

        $status_id = 4630003; // تایید شده

        $post_users = PostUser::where("user_id", $leave_overtime_confirmation_return->replace_user_id)->get();

        foreach ($post_users as $post_user) {

            $leave_overtime_confirmation = LeaveOvertimeConfirmation::create([
                "leave_overtime_id" => $leave->id,
                "post_user_id" => $post_user->id,
                "replace_user_id" => $leave_overtime->user_id,
                "number_top_levels_must_confirm" => 0,
                "number_top_levels_confirmed" => 0,
                "current_confirm_post_id" => null,
                "status_id" => $status_id
            ]);

        }

        $leave->status_id = $status_id;
        $leave->save();
    }

    public function getUserReplacement($worker_id, $start_datetime, $end_datetime)
    {

        $post_users = PostUser::where("user_id", $worker_id)->get();

        if (count($post_users) == 0) {
            return [
                "result" => false,
                "error" => "هیچ پست سازمانی برای شما تعریف نشده است، لطفا با پشیتبانی تماس بگیرید . "
            ];
        }

        // لیست پست هایی که با توجه به زمان مرخصی نیاز به جانشین دارند.
        $replace_confirm_post_user = LeaveController::get_replace_confirm_post_user($post_users, $start_datetime, $end_datetime);


        $post_user_workers = [];
        $user_ids = [];
        $k = 0;
        foreach ($post_users as $post_user) {
            if (in_array($post_user->id, $replace_confirm_post_user["need_replace_work"])) {
                $post_replace_ids = PostReplace::where("post_id", $post_user->post->id)->pluck("replace_post_id")->toArray();
                $replace_worker = Worker::join("post_user", "users.id", "user_id")->
                whereIn("post_id", $post_replace_ids)->
                where("user_id", "!=", $worker_id)->
                groupBy("user_id")->
                pluck("user_id")->
                toArray();

                $post_user_workers[$k] = $replace_worker;
                $k++;
            }

        }

        // از بین همه پست های کسانی که می توانند جانشین شوند را اشتراک می گیریم
        // یک نفر که می خواهد جایگزین فرد دیگری شود، باید بتواند برای همه پست های او جایگزین باشد.
        return $user_ids = ReplacementController::get_array_intersect($post_user_workers);

    }

    public static function get_array_intersect($list)
    {
        switch (count($list)) {
            case 0:
                return [];
            case 1:
                return $list[0];
            case 2:
                return array_intersect($list[0], $list[1]);
            case 3:
                return array_intersect($list[0], $list[1], $list[2]);
            case 4:
                return array_intersect($list[0], $list[1], $list[2], $list[3]);
            case 5:
                return array_intersect($list[0], $list[1], $list[2], $list[3], $list[4]);
            case 6:
                return array_intersect($list[0], $list[1], $list[2], $list[3], $list[4], $list[5]);
            case 7:
                return array_intersect($list[0], $list[1], $list[2], $list[3], $list[4], $list[5], $list[6]);
            case 8:
                return array_intersect($list[0], $list[1], $list[2], $list[3], $list[4], $list[5], $list[6], $list[7]);
            case 9:
                return array_intersect($list[0], $list[1], $list[2], $list[3], $list[4], $list[5], $list[6], $list[7], $list[8]);
            case 10:
                return array_intersect($list[0], $list[1], $list[2], $list[3], $list[4], $list[5], $list[6], $list[7], $list[8], $list[9]);
            default:
                return 1 / 0;
        }
    }
}
