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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class AbsenceController extends Controller
{
    // غیبت
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
            return back()->withErrors("اطلاعات درخواست یافت نشد، لطفا یکبار دیگر تلاس کنید.");
        }
        if ($leave_overtime_confirmation->replace_user_id != $worker->id) {
            return back()->withErrors("با توجه به اینکه شما جانشین  نیستید، امکان تایید برای شما وجود ندارد.");
        }
        if ($leave_overtime->status_id != 4630001) {
            return back()->withErrors("وضعیت جانشینی غیبت جهت تایید جانشین معتبر نمی باشد، لطفا با واحد منابع انسانی تماس بگیرید.");
        }

        $this->confirm_users($leave_overtime, "");

        self::sendSmsParent($leave_overtime);

        return redirect()->back()->with(["success" => "تایید جانشین غیبت با موفقیت ثبت گردید"]);
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
            return back()->withErrors("شناسه درخواست نامعتبر است، لطفا یکبار دیگر تلاش کنید . ");
        }
        if ($leave_overtime->status_id != 4630002) {
            return back()->withErrors("وضعیت درخواست جهت تایید مافوق معتبر نمی باشد، لطفا با واحد منابع انسانی تماس بگیرید . ");
        }

//return $request->all();
        switch ($request->type) {
            case "confirm":

                $result = $this->confirm_users($leave_overtime, $request->comment);
                if (!$result["result"]) {
                    return back()->withErrors($result["error"]);
                }
                $this->sendSmsParent($leave_overtime);

                return redirect()->back()->with(["success" => "تایید  درخواست با موفقیت ثبت گردید"]);
                break;
            case "reject":

                $leave_overtime->status_id = 4630004;//  عدم تایید مافوق
                $leave_overtime->save();

                event(new LeaveLogEvent($leave_overtime, 4630004, $request->comment));

                if($request)
                return redirect()->back()->with(["success" => "عدم تایید درخواست با موفقیت ثبت گردید"]);

                break;
//            case "comment":
//                $leave_overtime->status_id = 4630009;//  در انتظار اخذ توضیح
//                $leave_overtime->save();
//
//
//                $hr_leave_set_comment_replace_sms = Setting::getIntegerValue( "hr_leave_set_comment_replace_sms" );
//                $company_name                     = Setting::getStringValue( "company_name" );
//
//                if ( $hr_leave_set_comment_replace_sms ) {
//                    Notification::send( "00" . ( $leave_overtime->worker->country->area_code ?? "98" ) . $leave_overtime->worker->mobile,
//                        new SMSNotification( "hrleavesetcomment", $leave_overtime->worker->id ?? "", $leave_overtime->worker->random ?? "", $company_name, $leave_overtime->leave_overtime_type->caption, $worker->fullname() ) );
//
//                }
//                event( new LeaveLogEvent( $leave_overtime, 4630009, $request->comment ) );
//
//                return redirect()->back()->with( [ "success" => "اخذ توضیحات  جابجایی شیفت با موفقیت ثبت گردید" ] );
//                break;
        }


        return redirect()->back()->withErrors("درخواست نامعتبر است . ");

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

        $post_user_list = [];
        foreach ($list as $leave_overtime_confirmation) {
            $leave_overtime_confirmation->status_id = 4630002;
            $leave_overtime_confirmation->save();
            $add_log_replace = true;

            // اگر یک جانشین تایید کرد، همه جانشین های post_user مشابه حذف می شوند.
            LeaveOvertimeConfirmation::where([
                "leave_overtime_id" => $leave_overtime->id,
                "status_id" => 4630001,
                "post_user_id" => $leave_overtime_confirmation->post_user_id
            ])->
            where("replace_user_id", "!=", $worker->id)->
            delete();

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
        $worker_post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids", $worker);


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

        }

        if ($add_log_replace) {
            event(new LeaveLogEvent($leave_overtime, 4630002, $text));
        }
        if ($add_log_parent) {
            event(new LeaveLogEvent($leave_overtime, 4630003, $text));
        }

        return ["result" => true];
    }

    public static function CreateNewAbsence(Worker $worker, $start_datetime, $create_user_id = null, $check_cancel_item = false)
    {

        $start_datetime = Carbon::parse($start_datetime);
        $start_datetime1 = Carbon::parse($start_datetime);
        //تا پایان روز
        $end_datetime = (Carbon::parse($start_datetime)->addHour(24));


        $post_user_list = [];
        $post_user_all = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_user_object", $worker, $start_datetime1, $end_datetime);
        $post_users = [];
        foreach ($post_user_all as $post_user) {
            $post_users[$post_user->id] = $post_user;
        }
        $post_users = array_values($post_users);

        foreach ($post_users as $post_user) {
            if ($post_user->post->for_leave_required_to_replace_person) {
                $post_replace_ids = PostReplace::where("post_id", $post_user->post->id)->pluck("replace_post_id")->toArray();
                $replace_worker = Worker::join("post_user", "users.id", "user_id")->
                whereIn("post_id", $post_replace_ids)->
                where("user_id", "!=", $worker->id)->
                groupBy("user_id")->
                get();

                foreach ($replace_worker as $item) {
                    $post_user_list[] = ["user_id" => $item->user_id, "post_user" => $post_user];
                }

            }

        }

        if (count($post_user_list) == 0) {
            return [
                "result" => true,
                "message" => "نیاز به ثبت درخواست جانشینی غیبت نمی باشد."
            ];
        }

        // اگر قبلا یک درخواست باز برای امروز وجود داشت، دیگر لازم نیست درخواست جدید ثبت کند.
        $before_list_count = LeaveOvertime::where("user_id", $worker->id)->
        join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
        where("leave_overtime_group_id", 5)->//غیبت
        when($check_cancel_item,function ($query){
            return $query->whereNotIn("status_id", [ 4630004,4630008]);
        })->
        where("start_datetime", "<=", Carbon::now())->
        where("end_datetime", ">=", Carbon::now())->
        count();
        if ($before_list_count > 0) {
            return [
                "result" => true,
                "message" => "قبلا یک درخواست ثبت شده است."
            ];
        }

        // شیفت کاری امروز را به دست می آوریم و پاره وقتی که زمان پایان آن از همه بزرگتر است را انتخاب می کنیم.
        $shift_work_query = ShiftWorkDayController::GetDateWorkQuery($worker, 0, 0)->
        where("start_datetime", ">=", Carbon::parse($start_datetime->format("Y-m-d")))->
        where("end_datetime", "<=", $end_datetime)->
        orderByDesc("end_datetime")->
        first();
        if (!$shift_work_query) {
            return [
                "result" => true,
                "message" => "برای امروز زمان کاری وجود ندارد."
            ];
        }
        $end_datetime = $shift_work_query->end_datetime;

        $absence = LeaveOvertime::create([
            "user_id" => $worker->id,
            "leave_overtime_type_id" => 501,
            "start_datetime" => $start_datetime,
            "end_datetime" => $end_datetime,
            "status_id" => 4630001 // در انتظار تایید جانشین
        ]);

        foreach ($post_user_list as $post_user_item) {
            $post_user = $post_user_item["post_user"];
            $user_id = $post_user_item["user_id"];


            //تعداد سطح بالایی
            $time = $start_datetime->diffInHours($end_datetime);
            $for_leave_a_few_top_levels_must_confirm = $post_user->post->for_leave_a_few_top_levels_must_confirm;
            if ($time > $post_user->post->for_leave_a_few_top_levels_must_confirm_time) {
                $for_leave_a_few_top_levels_must_confirm = $post_user->post->for_leave_a_few_top_levels_must_confirm_time_level;
            }

            $leave_overtime_confirmation = LeaveOvertimeConfirmation::create([
                "leave_overtime_id" => $absence->id,
                "post_user_id" => $post_user->id,
                "replace_user_id" => $user_id, // جانشین غیبت
                "number_top_levels_must_confirm" => $for_leave_a_few_top_levels_must_confirm,
                "number_top_levels_confirmed" => 0,
                "current_confirm_post_id" => $post_user->post->parent_id,
                "status_id" => 4630001 // در انتظار تایید جانشین
            ]);


        }


        self::sendSmsReplace($absence);
//
        self::sendSmsParent($absence);


        event(new LeaveLogEvent($absence, 4630012, "", $create_user_id)); //جانشینی غیبت

        return [
            "result" => true,
            "absence" => $absence
        ];
    }

    public static function CancelRequest(Worker $worker, $user_id)
    {
        $list = LeaveOvertime::
        join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
        where("user_id", $worker->id)->
        whereIn("leave_overtimes.status_id", [4630001, 4630002, 4630009])->
        where("leave_overtime_group_id", 5)->
        select("leave_overtimes.*")->
        get();
        foreach ($list as $item) {
            $item->status_id = 4630004; // کنسل شده
            $item->save();
            event(new LeaveLogEvent($item, 4630012, "", $user_id)); //جانشینی غیبت
        }
    }

    public static function sendSmsParent(LeaveOvertime $leave_overtime, $template = null)
    {
        return true;
//        $company_name = Setting::getStringValue("company_name");
//        $hr_leave_conform_parent_sms = Setting::getIntegerValue("hr_leave_conform_parent_sms");
//        if (!$hr_leave_conform_parent_sms) {
//            return false;
//        }
//
//        $list = LeaveOvertimeConfirmation::
//        where([
//            "leave_overtime_id" => $leave_overtime->id,
//            "status_id" => 4630002 // در انتظار تایید مافوق
//        ])->
//        get();
//
//        $replace_count = LeaveOvertimeConfirmation::where([
//            "leave_overtime_id" => $leave_overtime->id,
//            "status_id" => 4630001 // در انتظار تایید جانشین
//        ])->
//        count();
//        // اگر حداقل یک جانشین وجود دارد که تایید نشده است، پیامک برای مدیر ارسال نشود.
//        if ($replace_count > 0) {
//            return;
//        }
//
//        foreach ($list as $leave_overtime_confirmation) {
//
//            // اگر تعداد تایید ها به حد نصاب نرسیده برای آخرین پست پیامک ارسال می شود.
//            if ($leave_overtime_confirmation->number_top_levels_confirmed < $leave_overtime_confirmation->number_top_levels_must_confirm) {
//
//
//                $post_users = PostUser::where("post_id", $leave_overtime_confirmation->current_confirm_post_id)->get();
//                $token = $company_name;
//
//                foreach ($post_users as $post_user) {
//                    $token3 = "_APP_NAME_" . "/Personal/" . ($post_user->worker->id ?? "") . "/" . ($post_user->worker->random ?? "");
//
//                    Notification::send("00" . ($post_user->worker->country->area_code ?? "98") . $post_user->worker->mobile,
//                        new SMSNotification($template ?? "hrleaveconfirmparentpost",
//                            $token,
//                            "",
//                            $token3,
//                            $leave_overtime_confirmation->leave_overtime->leave_overtime_type->caption,
//                            $leave_overtime_confirmation->leave_overtime->worker->fullname())
//                    );
//                }
//            }
//
//        }


    }

    public static function sendSmsReplace(LeaveOvertime $leave_overtime)
    {

        return true;
//        $hr_leave_conform_parent_sms = Setting::getIntegerValue("hr_leave_confirm_replace_sms");
//        if (!$hr_leave_conform_parent_sms) {
//            return false;
//        }
//        $company_name = Setting::getStringValue("company_name");
//
//        $list = LeaveOvertimeConfirmation::where([
//            "leave_overtime_id" => $leave_overtime->id,
//            "status_id" => 4630001
//        ])->get();
//
//        foreach ($list as $leave_overtime_confirmation) {
//            $replace_worker = $leave_overtime_confirmation->replace_worker;
//
//            $token = $company_name;
//            $token3 = "_APP_NAME_" . "/Personal/" . ($replace_worker->id ?? "") . "/" . ($replace_worker->random ?? "");
//            Notification::send("00" . ($replace_worker->country->area_code ?? "98") . $replace_worker->mobile,
//                new SMSNotification("hrleaveconfirmreplaceabsence",
//                    $token,
//                    "",
//                    $token3,
//                    $replace_worker->fullname(),
//                    $leave_overtime_confirmation->leave_overtime->worker->fullname()));
//
//        }
    }

}
