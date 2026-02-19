<?php

namespace App\Http\Controllers\HR\Worker;

use App\Events\HR\LeaveLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DashboardController;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\LeaveOvertime\LeaveOvertime;
use App\Models\Post\PostUser;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Sodium\compare;

class OvertimeTogetherController extends Controller
{
    var $view_path = "hr.worker.overtime_together.";
    var $route_path = "hr.worker.overtime_together.";


    public function index()
    {


        $post_users = PostUser::join("users", "users.id", "user_id")->

        whereIn("cooperation_type_id", [1, 11])->groupBy("post_id")->get();

        if (count($post_users) == 0) {
            return back()->
            withErrors("هیچ پست سازمانی در سامانه تعریف نشده است، لطفا با واحد منابع انسانی تماس بگیرید.");
        }

        $text = "";
        foreach ($post_users as $post_user) {
            if (!$post_user->post->shift) {
                $text .= $post_user->post->caption . ", ";
            }
        }
        if ($text != "") {
            return back()->
            withErrors("با توجه به اینکه برای پست های زیر شیفت مشخص نشده است، امکان ثبت اضافه کاری وجود ندارد، لطفا با واحد منابع انسانی تماس بگیرید." . "<br/>" . $text);
        }

        $post_users_not_shift_work = PostUser::join("users", "users.id", "user_id")->whereIn("cooperation_type_id", [1, 11])->
        whereNull("shift_work_id")->
        get();
        $text = "";
        if (count($post_users_not_shift_work) != 0) {

            foreach ($post_users_not_shift_work as $item) {
                $text .= $item->post->caption . ", ";
            }

            return back()->
            withErrors("گروه شیفت برای پست های زیر مشخص نشده است، لطفا با واحد منابع انسانی تماس بگیرید." . "<br/>" . $text);
        }


        $post_users = PostUser::join("users", "users.id", "user_id")->

        whereIn("cooperation_type_id", [1, 11])->groupBy("user_id")->get();
        $option_users = [];
        foreach ($post_users as $post_user) {
            $option_users[] = ["value" => $post_user->user_id, "text" => $post_user->worker->fullname()];
        }

        return view($this->view_path . "index", compact("option_users"));

    }

    public function submit(Request $request)
    {

        if (!isset($request->user_ids) || count($request->user_ids) == 0) {
            return back()->withErrors("لطفا حداقل یک فرد را انتخاب نمایید.");
        }

        $start_datetime = Carbon::parse($request->start_datetime);
        $end_datetime = Carbon::parse($request->end_datetime);

        // session(["overtime_request" => $request->all()]);

        if ($start_datetime->greaterThan($end_datetime)) {
            return back()->withErrors("زمان پایان اضافه کاری نمی تواند از شروع شروع اضافه کاری کوچکتر باشد.");
        }
        $workers = Worker::whereIn("id", $request->user_ids)->get();
        foreach ($workers as $worker) {
            $leave = LeaveOvertime::create([
                "user_id" => $worker->id,
                "leave_overtime_type_id" => 200,// اضافه کاری
                "start_datetime" => $start_datetime,
                "end_datetime" => $end_datetime,
                "status_id" => 4630003  // تایید شده
            ]);
            event(new LeaveLogEvent($leave, 4630001, $request->text));
            event(new LeaveLogEvent($leave, 4630003,));
        }
        return redirect()->route($this->route_path . "index")->with(["success" => "یک درخواست مرخصی گروهی برای همه افراد انتخاب شده ثبت گردید"]);
    }

}
