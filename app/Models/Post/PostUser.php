<?php

namespace App\Models\Post;

use App\Models\OilChange\Car;
use App\Models\User;
use App\Models\HR\LeaveOvertime\LeaveOvertime;
use App\Models\HR\Shift\ShiftWorkDay;
use App\Models\HR\Shift\ShiftWork;
use App\Models\Utility\Menu\ButtonPost;
use App\Models\Utility\Menu\MenuPost;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Morilog\Jalali\CalendarUtils;
use phpDocumentor\Reflection\Types\True_;

class PostUser extends Model
{
    use HasFactory;
    use Loggable;

    protected $table = "post_user";
    protected $fillable = ["post_id", "user_id", "shift_work_id"];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id");
    }

    public function shift_work()
    {
        return $this->belongsTo(ShiftWork::class);
    }

    public function checkButtonPermission($button_names, $multi_route = false, $breakShiftWorkDay = false)
    {

        if ($breakShiftWorkDay) {
            $post_ids = \Auth::user()->posts->pluck("post_id");
        } else {
            $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids", $this->worker);
        }

        // ممکن است که یک عملیات برای ماژول های مختلف مشترک باشد، برای همین ممکن از چند
        // button_names
        // ارسال شود و سپس در یک کنترل نوع ماژول شناسایی و سپس به ماژول نهایی پاس داده می شود.
        if ($multi_route) {
            return ButtonPost::join("buttons", "buttons.id", "button_id")->
            whereIn("post_id", $post_ids)->
            whereIn("name", $button_names)->
            exists();
        } else {
            return ButtonPost::join("buttons", "buttons.id", "button_id")->
            whereIn("post_id", $post_ids)->
            where("name", $button_names)->
            exists();
        }

    }

    public function getShiftWorkDay($type)
    {
        $year = jdate(Carbon::parse(Carbon::now())->timestamp)->format('Y');
        $first_day_in_year = CalendarUtils::toGregorian($year, 1, 1);

        $first_day_datetime = Carbon::create($first_day_in_year[0], $first_day_in_year[1], $first_day_in_year[2], 0, 0);
        $now = Carbon::now();
        $current_date = Carbon::create($now->year, $now->month, $now->day, 0, 0)->addDay();
        $day = $current_date->diffInDays($first_day_datetime);

        switch ($type) {
            case "now":
                return ShiftWorkDay::where([
                    "shift_id" => $this->post->shift_id ?? 0,
                    "shift_work_id" => $this->shift_work_id,
                    "day" => $day
                ])->
                where("start_datetime", "<=", $now)->
                where("end_datetime", ">=", $now)->
                first();
                break;
            case "current_day":
                return ShiftWorkDay::where([
                    "shift_id" => $this->post->shift_id ?? 0,
                    "shift_work_id" => $this->shift_work_id,
                    "day" => $day
                ])->
                first();
                break;
        }
    }

    public function getMenuList($worker)
    {
        // لیست منو های مجاز
        // return  $menuList = $this->post->hasMany( MenuPost::class )->get();
        $menuList = [];
        $post_entry_row = $this->post->post_entry_status()->where("status_id", $worker->status_id ?? 0)->first();
        // بررسی وضعیت دسترسی با توجه به وضعیت های مجاز شاغلین
        if ($post_entry_row && $post_entry_row->allow_show_personal_menu && $post_entry_row->allow_show_all_menu) {

            if (!$post_entry_row->allow_filter_menu) { // فیلتر دسترسی ها با توجه به زمان
                $menuList = $this->post->hasMany(MenuPost::class)->get();
            } else {

                if ($this->AllowAccessToAllMenu($worker)) {
                    $menuList = $this->post->hasMany(MenuPost::class)->get();
                } else {
                    $menuList = $this->post->hasMany(MenuPost::class)->where("menu_id", 515)->get();// پرسنلی
                }

            }
        } elseif ($post_entry_row && $post_entry_row->allow_show_personal_menu && !$post_entry_row->allow_show_all_menu) {
            $menuList = $this->post->hasMany(MenuPost::class)->where("menu_id", 515)->get();// پرسنلی

        } else {
            $menuList = $this->post->hasMany(MenuPost::class)->where("menu_id", 0)->get();// منو ها خالی باشد
        }

        return $menuList;

    }

    public function getMenuPermission($worker, $menu_id)
    {

        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        return   MenuPost::whereIn("post_id", $post_ids)->count()>0;

//        $menu_list = $this->getMenuList($worker);
//        foreach ($menu_list as $item) {
//            if ($item->menu_id == $menu_id) {
//                return true;
//            }
//        }
//
//        return false;
    }

    public function AllowAccessToAllMenu($worker, $carbon_start_datetime = null, $carbon_end_datetime = null)
    {
        if (!$carbon_start_datetime) {
            $carbon_start_datetime = Carbon::now();
        }
        if (!$carbon_end_datetime) {
            $carbon_end_datetime = Carbon::now();
        }
        // به ازای یک post_user بررسی می کند که الان این پست به همه منوها دسترسی دارد یا خیر
        $post_entry_row = $this->post->post_entry_status()->where("status_id", $worker->status_id)->first();
        // بررسی وضعیت دسترسی با توجه به وضعیت های مجاز شاغلین
        if ($post_entry_row && $post_entry_row->allow_show_personal_menu && $post_entry_row->allow_show_all_menu) {

            if (!$post_entry_row->allow_filter_menu) { // فیلتر دسترسی ها با توجه به زمان
                return true;
            } else {
                // فیلتر دسترسی ها با توجه به زمان گروه شیفت
                $shift_work_day_start_count = ShiftWorkDay::where([
                    "shift_id" => $this->post->shift_id,
                    "shift_work_id" => $this->shift_work_id
                ])->
                where("start_datetime", "<=", $carbon_end_datetime->addMinute($this->post->allowed_earlier_time_for_entry))->
                where("end_datetime", ">=", $carbon_start_datetime->addMinute(-$this->post->allowed_delay_time_for_exit))->
                exists();

                $over_time_count = LeaveOvertime::
                join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
                join("leave_overtime_confirmation", "leave_overtimes.id", "leave_overtime_id")->
                where("leave_overtime_group_id", 2)->//اضافه کاری
                whereIn("leave_overtimes.status_id", [4630003, 4630007, 4630006])->
                where("leave_overtimes.user_id", $worker->id)->
                where("start_datetime", "<", now())->
                where("end_datetime", ">", now())->count();

                if ($shift_work_day_start_count || $over_time_count) {
                    return true;
                }
            }

        }

        return false;
    }

    public static function getCurrentPostByShiftWorkAndLeaveOvertime($type, $worker = null, $carbon_start_datetime = null, $carbon_end_datetime = null)
    {
        // لیست همه post_user های که یک کاربر در زمان حال به آنها دسترسی دارد را به دست می آورد.
        if (!$worker) {
            $worker = Worker::find(Auth::id());
        }
        // تاریخی که می خواهیم در آن زمان پست های فعال طرف بررسی شود.
        if (!$carbon_start_datetime) {
            $carbon_start_datetime = Carbon::now();
        }
        if (!$carbon_end_datetime) {
            $carbon_end_datetime = Carbon::now();
        }

        // لیست همه پست های فرد را می گیریم - $worker->post نادرست است، چون وقتی یک چیزی به آن اضافه می کنیم، ذخیره می شود( خطی کد پایین که پست های مرخصی هم اضافه می شود)
        $post_users = $worker->posts()->get();

        if (count($post_users) > 0) {
            // از بین پست ها پستی که بیشترین مقدار را دارد انتخاب می کنیم.
            $allow_earlier_and_delay_for_post =
                PostUser::join("posts", "posts.id", "post_id")->
                where("user_id", $worker->id)->
                selectRaw("max(allowed_earlier_time_for_entry) as allowed_earlier_time_for_entry, max(allowed_delay_time_for_exit) as allowed_delay_time_for_exit")->
                first();
        }
        // بررسی پست های جانشین مرخصی
        $leaves_post_user_ids = LeaveOvertime::
        join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
        join("leave_overtime_confirmation", "leave_overtimes.id", "leave_overtime_id")->
        whereIn("leave_overtime_group_id", [1,3, 4, 5])->//مرخصی ,ماموریت, جایگزینی - غیبت
        whereIn("leave_overtimes.status_id", [4630003, 4630007, 4630006])->
        where("replace_user_id", $worker->id)->
        where("start_datetime", "<", $carbon_end_datetime->addMinute(($allow_earlier_and_delay_for_post->allowed_earlier_time_for_entry ?? 1)))->
        where("end_datetime", ">", $carbon_start_datetime->addMinute(-($allow_earlier_and_delay_for_post->allowed_delay_time_for_exit ?? 0)))->
        pluck("post_user_id")->
        toArray();

//        if($worker->id == 122){
//            $leaves_post_user_ids = LeaveOvertime::
//            join( "leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id" )->
//            join( "leave_overtime_confirmation", "leave_overtimes.id", "leave_overtime_id" )->
//            where( "leave_overtime_group_id", 1 )->//مرخصی
//            whereIn( "leave_overtimes.status_id", [ 4630003, 4630007, 4630006 ] )->
//            where( "replace_user_id", $worker->id )->
//            where( "start_datetime", "<", Carbon::now()->addMinute( - 100 ) )->
//            where( "end_datetime", ">", Carbon::now()->addMinute( $post->allowed_delay_time_for_entry ?? 0 ) )->
//            pluck( "post_user_id" )->
//            toArray();
//        }

        $leave_post_users = PostUser::whereIn("id", $leaves_post_user_ids)->get();
        foreach ($leave_post_users as $item) {
            // $item->user_id=$worker->id; // چون اگر کسی جانشین کسی باشد، باید بتواند، هرجا شناسه مرخصی گیرنده است، باید بشود، شناسه جانشین
            $post_users [] = $item;
        }


        switch ($type) {
            case "post_user_object":

                return $post_users;
                break;
            case "user_ids":
                $user_ids = [-1];
                foreach ($post_users as $post_user) {
                    $user_ids[$post_user->user_id] = $post_user->user_id;
                }
                return $user_ids;
                break;

            case "post_ids":
                $post_ids_allow_access = [];
                foreach ($post_users as $post_user) {
                    if ($post_user->AllowAccessToAllMenu($worker, $carbon_start_datetime, $carbon_end_datetime)) {
                        $post_ids_allow_access[] = $post_user->post_id;

                    }
                }

                return $post_ids_allow_access;
                break;

            case "post_ids_with_out_menu": // لیست منو هایی که فرد دسترسی دارد، بدون در نظر گرفتن وضعیت دسترسی
                $post_ids_allow_access = [];
                foreach ($post_users as $post_user) {
                        $post_ids_allow_access[] = $post_user->post_id;
                }

                return $post_ids_allow_access;
                break;
            case "leaves_user_ids":

                $user_ids_allow_access = [];
                foreach ($post_users as $post_user) {
                    if ($post_user->AllowAccessToAllMenu($worker, $carbon_start_datetime, $carbon_end_datetime)) {
                        $user_ids_allow_access[] = $post_user->user_id;

                    }
                }
                $user_ids_allow_access[] = $worker->id;
                return $user_ids_allow_access;

            case "leaves_post_ids":
                $user_ids_allow_access = [];
                foreach ($post_users as $post_user) {
                    if ($post_user->AllowAccessToAllMenu($worker, $carbon_start_datetime, $carbon_end_datetime)) {
                        $user_ids_allow_access[] = $post_user->post_id;

                    }
                }

                return $user_ids_allow_access;
                break;
            case "post_id_shift_work_ids":
                $post_id_shift_work_ids_allow_access = [];
                foreach ($post_users as $post_user) {
                    if ($post_user->AllowAccessToAllMenu($worker, $carbon_start_datetime, $carbon_end_datetime)) {
                        $post_id_shift_work_ids_allow_access[] = ["post_id" => $post_user->post_id,
                            "shift_work_id" => $post_user->shift_work_id
                        ];

                    }
                }

                return $post_id_shift_work_ids_allow_access;
                break;
        }

    }

    public static function getCurrentUserByShiftWorkAndLeaveOvertimeByPostId($type, $post_id)
    {
        // لیست همه کاربرانی  که در زمان حال به پست (post_id) دسترسی دارند را برمی گرداند.


        $post_users = PostUser::where("post_id", $post_id)->get();
        $workers = [];
        foreach ($post_users as $post_user) {

            if ($post_user->AllowAccessToAllMenu($post_user->worker)) {
                $leaves_post_user_ids = LeaveOvertime::
                join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
                join("leave_overtime_confirmation", "leave_overtimes.id", "leave_overtime_id")->
                whereIn("leave_overtime_group_id", [1, 2, 4, 5])->//مرخصی ,ماموریت جایگزینی غیبت
                whereIn("leave_overtimes.status_id", [4630003, 4630007, 4630006])->
                where("user_id", $post_user->user_id)->
                where("start_datetime", "<", now())->
                where("end_datetime", ">", now())->
                get("replace_user_id");

                foreach ($leaves_post_user_ids as $item) {
                    $worker = Worker::find($item->replace_user_id);
                    if ($worker)
                        $workers[] = $worker;
                }

                $workers[] = $post_user->worker;
            }
        }


        switch ($type) {
            case "worker":

                return $workers;
                break;


        }
    }

    public static function PostAddUser(Post $post, Worker $worker, $shift_work_id)
    {

        $post_user_count = PostUser::where([
            "user_id" => $worker->id,
            "post_id" => $post->id,
            "shift_work_id" => $shift_work_id
        ])->count();
        if ($post_user_count >= 1) {
            return [
                "result" => false,
                "error" => "این پست قبلا به شاغل اختصاص داده شده است."
            ];
        }

        $post->worker()->attach($worker->id, ["shift_work_id" => $shift_work_id]);

        $shift_work = ShiftWork::find($shift_work_id);

        Notification::send("00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
            new SMSNotification("addpost", $post->caption, $shift_work->caption ?? "", null, $worker->fullname()));

        //ارسال پیامک برای ناظر ها
        $supervisor = Post::where("is_system_supervisor", 1)->
        whereNotIn("id",Post::InvalidPost())->
        get();

        foreach ($supervisor as $item) {

            foreach ($item->worker as $super_worker) {

                Notification::send(
                    "00" . ($super_worker->mobile_country->area_code ?? "98") . $super_worker->mobile,
                    new SMSNotification("supervisoralertforaddpost", $post->caption, $shift_work->caption ?? "", null, $worker->fullname()));

            }

        }

        return [
            "result" => true
        ];
    }

    public static function PostTrafficNotification( Worker $worker,$register_user_id,$entry_caption,$datetime)
    {
        // اطلاع رسانی پست سازمانی
        if ($register_user_id == 2) { // دستیار دیجیتال پیامک ارسال نمی شود.
            return true;
        }
        // بررسی انیکه آیا مجوز خروج، پس از ورود دارد یا خیر
        $traffic_notification_for_post_ids = PostUser::join("posts", "posts.id", "post_id")->
        where("user_id", $worker->id)->
        whereNotNull("traffic_notification_for_post_id")->
        pluck("traffic_notification_for_post_id")->toArray();

        if (count($traffic_notification_for_post_ids) > 0) {
            $supervisor = Post::whereIn("id", $traffic_notification_for_post_ids)->get();
            foreach ($supervisor as $item) {

                foreach ($item->worker as $super_worker) {

                    Notification::send(
                        "00" . ($super_worker->mobile_country->area_code ?? "98") . $super_worker->mobile,
                        new SMSNotification("hrentrynotificationtopost", $entry_caption,$datetime,null, $worker->fullname(), $datetime ));

                }

            }
        }
    }

}
