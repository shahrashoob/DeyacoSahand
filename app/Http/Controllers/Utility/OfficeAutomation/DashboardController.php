<?php

namespace App\Http\Controllers\Utility\OfficeAutomation;

use App\Events\HR\OfficeAutomationEvent;
use App\Events\HR\OfficeAutomationLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\Out\ExitFormController;
use App\Models\File\File;
use App\Models\Post\PostUser;
use App\Models\Utility\OfficeAutomation\OfficeAutomationAction;
use App\Models\Utility\OfficeAutomation\OfficeAutomationFile;
use App\Models\Utility\OfficeAutomation\OfficeAutomationToDoList;
use App\Models\Utility\OfficeAutomation\OfficeAutomationUser;
use App\Models\Utility\OfficeAutomation\OfficeAutomationWork;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Priority;
use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DashboardController extends Controller
{
    //
    public $route_path = "utility.office_automation.dashboard.";
    public $view_path = "utility.office_automation.dashboard.";

    public function index(Request $request)
    {

        $user_id = Auth::id();
        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
            $priority_id = $request->priority_id;
            $status_id = $request->status_id;
            $start_datetime = $request->start_datetime;
            $end_datetime = $request->end_datetime;
        } else {
            $search = session("search_office_automation");
            $order_by = session("order_by_office_automation") ?? "id__desc";
            $priority_id = session("priority_id_office_automation");
            $status_id = session("status_id_office_automation") ?? 5250002;
            $start_datetime = session("start_datetime_office_automation");
            $end_datetime = session("end_datetime_office_automation");
        }
        session([
            "search_office_automation" => $search,
            "order_by_office_automation" => $order_by,
            "priority_id_office_automation" => $priority_id,
            "status_id_office_automation" => $status_id,
            "start_datetime_office_automation" => $start_datetime,
            "end_datetime_office_automation" => $end_datetime,
        ]);

        // لیست کارهایی که خودش ایجاد کرده
        $list = OfficeAutomationWork::
        join("office_automation_users", "office_automation_works.id", "office_automation_work_id")->

        where("office_automation_users.user_id", Auth::id())->
        select("office_automation_works.*",)->
        when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                return $query->where("code", "like", "%" . $search . "%")->
                orWhere("caption", "like", "%" . $search . "%");
            });

        })->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);
        })->
        when($priority_id, function ($query) use ($priority_id) {
            return $query->where("office_automation_works.priority_id", $priority_id);
        })->
        when($status_id, function ($query) use ($status_id) {
            if($status_id ==5250002){
                return $query->whereIn("office_automation_works.status_id", [$status_id,5250005]);
            }
            else {
                return $query->where("office_automation_works.status_id", $status_id);
            }
        })->
        when($start_datetime, function ($query) use ($start_datetime) {
            return $query->where("office_automation_works.created_at", ">=", $start_datetime);
        })->
        when($end_datetime, function ($query) use ($end_datetime) {
            $end_datetime = Carbon::parse($end_datetime)->addDay();

            return $query->where("office_automation_works.created_at", "<=", $end_datetime);
        })->
        paginate();

        $order_by_Option = Option::OrderBy("office_automation", $order_by);
        $status_Option = Option::get("status_in_ids", $status_id, 0, [5250002, 5250004, 5250005]);
        $priority_option = Option::get("priority", $priority_id, 2, [], 0);

        return view($this->view_path . "index", compact("list", "user_id", "search", "order_by_Option", "status_Option", "priority_option", "start_datetime", "end_datetime"));
    }

    public function view(OfficeAutomationWork $office_automation_work, $current_user_id, $parent_user_id = 0)
    {

        $current_user = Worker::find($current_user_id);

        $user = Worker::find(Auth::id());

        $result = $this->checkPermission($office_automation_work, $user->id);
        if ($result != "") {
            return $result;
        }


        // ثبت لاگ مشاهده کارها

        $office_automation_views = OfficeAutomationAction::where([
            "office_automation_work_id" => $office_automation_work->id,
            "user_id" => $current_user->id,
            "office_automation_to_do_type_id" => 1, //جهت مشاهده
            "status_id" => "5250011"
        ])->
        get();
        foreach ($office_automation_views as $office_automation_view) {
            $office_automation_view->status_id = 5250012; //مشاهده شده
            $office_automation_view->save();
            event(new OfficeAutomationLogEvent($office_automation_view->office_automation_work, $office_automation_view->office_automation_to_do_list, $office_automation_view, 5250006));
        }

        // ثبت وضعیت مشاهده شده توسط کاربر
        $action_not_view = OfficeAutomationAction::where([
            "office_automation_work_id" => $office_automation_work->id,
            "user_id" => $user->id,
            "view_status_id" => 5250011 // در انتظار مشاهده
        ])->
        get();
        foreach ($action_not_view as $office_automation_action) {
            $office_automation_action->view_status_id = 5250012; // مشاهده شده
            $office_automation_action->save();
            event(new OfficeAutomationLogEvent($office_automation_action->office_automation_work, $office_automation_action->office_automation_to_do_list, $office_automation_action, 5250007));

        }


        $office_automation_action_created_by_user_ids = OfficeAutomationAction::where([
            "office_automation_work_id" => $office_automation_work->id,
            "user_id" => $current_user->id
        ])->pluck("id")->toArray();
        $office_automation_action_created_by_user_ids[] = -1;

        $siblingToDoList = OfficeAutomationToDoList::
        where([
            "office_automation_work_id" => $office_automation_work->id,
            "user_id" => $current_user->id
        ])->
        get();

        $to_do_list_action = OfficeAutomationToDoList::
        join("office_automation_actions", "office_automation_to_do_list.id", "office_automation_to_do_list_id")->
        where([
            "office_automation_actions.office_automation_work_id" => $office_automation_work->id,
            "office_automation_actions.user_id" => $current_user->id
        ])->
        groupBy("office_automation_to_do_list_id")->
        select("office_automation_to_do_list.*")->
        get();


//        return OfficeAutomationToDoList::where("user_id",$current_user->id)->whereNotNull("office_automation_action_id");
//      return  OfficeAutomationToDoList::whereIn("office_automation_action_id",$office_automation_action_created_by_user_ids)->get();
////return $office_automation_action;
        //  return $office_automation_to_do_list->office_automation_work;
        $max_file_size = Setting::getIntegerValue("office_automation_max_file_size_in_mb");

        return view($this->view_path . "view", compact("max_file_size", "current_user", "office_automation_work", "siblingToDoList", "to_do_list_action", "user"));


    }

    public function DCWT_ShortLink(OfficeAutomationWork $office_automation_work, $key)
    {

        if ($office_automation_work->random != $key) {
            return back()->withErrors("کار مورد نظر در کارتابل شما وجود ندارد.");
        }

        return $this->view($office_automation_work, Auth::id());
    }

    public function create($to_do_list_parent_id = null, $work_parent_id = null)
    {
        $list = [];

        $office_automation_to_do_list_parent = OfficeAutomationToDoList::find($to_do_list_parent_id);
        if ($to_do_list_parent_id && !$office_automation_to_do_list_parent) {
            return back()->withErrors("لینک صفحه مورد نظر یافت نشد.");
        }
        $office_automation_work = $office_automation_to_do_list_parent->office_automation_work ?? "";


        if ($work_parent_id) {
            $office_automation_work = OfficeAutomationWork::find($work_parent_id);
            if (!$office_automation_work) {
                return back()->withErrors("لینک صفحه مورد نظر یافت نشد.");
            }
        }


        $post_option = Option::get("post_user");
        $office_automation_to_do_type_option = Option::get("office_automation_to_do_type", session("to_do_type_office_automation") ?? 0, 0, [1]);
        $priority_option = Option::get("priority", $office_automation_to_do_list_parent->priority_id ?? session("priority_office_automation") ?? 0, 2);
        $max_file_size = Setting::getIntegerValue("office_automation_max_file_size_in_mb");

        $caption = session("caption_office_automation") ?? "";
        $description = session("description_office_automation") ?? "";
        $end_datetime = session("end_datetime_office_automation") ?? "";

        return view($this->view_path . "create", compact("list", "caption", "description", "end_datetime", "max_file_size", "post_option", "priority_option", "office_automation_to_do_type_option", "to_do_list_parent_id", "work_parent_id", "office_automation_work"));
    }

    public function store(Request $request)
    {

//        return $request->all();
        //   return $request->work_file->extension();
        session([
            "caption_office_automation" => $request->caption ?? "",
            "description_office_automation" => $request->description ?? "",
            "end_datetime_office_automation" => $request->end_datetime ?? "",
            "to_do_type_office_automation" => $request->office_automation_to_do_type_id ?? "",
            "priority_office_automation" => $request->priority_id ?? "",
        ]);

        $post_user_operation = $request->post_user_operation;
        $post_user_view = $request->post_user_view;

        if (!$post_user_operation || count($post_user_operation) <= 0) {
            return back()->withErrors("لطفا حداقل یک نفر جهت اقدام انتخاب نمایید.");
        }
        if (Str::length($request->caption) > 50) {
            return back()->withErrors("عنوان کار نباید بیش از 50 کاراکتر باشد");
        }
        $substr = substr_count($request->caption, ' ');
        if ($substr > 4) {
            return back()->withErrors("عنوان کار حداکثر می تواند دارای 4 کاراکتر  Space باشد");
        }
        $result = DashboardController::checkFiles($request);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }
        if (!$request->end_datetime) {
            session([
                "end_datetime_office_automation" => $request->end_datetime ?? "",
            ]);

            return back()->withErrors("لطفا تاریخ پایان را انتخاب نمایید.");
        }

        // لیست کارها جهت اقدام
        $post_user_operation_list = PostUser::
        whereIn("id", $post_user_operation)->
        get();

        // بررسی اینکه شرکت با دیاکو قرارداد فعال دارد یا خیر
        $has_active_contract = Setting::getIntegerValue("has_active_contract");

        if (!$has_active_contract) {

            foreach ($post_user_operation_list as $post_user) {
                if($post_user->post_id == 2000){
                    return back()->withErrors("با توجه به اینکه هیچ قرارداد پشتیبانی فعالی برای شرکت وجود ندارد، امکان ثبت تیکت برای نماینده دیاکو امکان پذیر نمی باشد.");
                }
            }

            if ($post_user_view) {
                $post_user_operation_view = PostUser::whereIn("id", $post_user_view)->get();
                foreach ($post_user_operation_view as $post_user) {
                    if($post_user->post_id == 2000){
                        return back()->withErrors("با توجه به اینکه هیچ قرارداد پشتیبانی فعالی برای شرکت وجود ندارد، امکان ثبت میزکار برای نماینده دیاکو امکان پذیر نمی باشد.");
                    }
                }
            }
        }

        // تعریف کار
        $office_automation_work = OfficeAutomationWork::create([
            "caption" => $request->caption,
            "user_id" => Auth::id(),
            "status_id" => 5250002,
            // در جریان
            "end_datetime" => $request->end_datetime,
            "priority_id" => $request->priority_id,
        ]);

        event(new OfficeAutomationLogEvent($office_automation_work, null, null, 5250001, ""));

        // تعریف ارجاع
        $office_automation_to_do_list = OfficeAutomationToDoList::create([
            "office_automation_work_id" => $office_automation_work->id,
            "user_id" => Auth::id(),
            "status_id" => 5250002, // در جریان
            "priority_id" => $request->priority_id,
            "end_datetime" => $request->end_datetime,
            "caption" => $request->caption,
            "description" => nl2br($request->description),
        ]);

        event(new OfficeAutomationLogEvent($office_automation_work, $office_automation_to_do_list, null, 5250002, "", $request));

        // اضافه کردن کار به لیست کاربران
        OfficeAutomationUser::create([
            "office_automation_work_id" => $office_automation_work->id,
            "user_id" => Auth::id(),
            "status_id" => 5250002, // در جریان
            "priority_id" => $request->priority_id,
            "input_or_create" => 1
        ]);



        foreach ($post_user_operation_list as $post_user) {

            $office_automation_action = OfficeAutomationAction::create([
                "office_automation_work_id" => $office_automation_work->id,
                "office_automation_to_do_list_id" => $office_automation_to_do_list->id,
                "office_automation_to_do_type_id" => $request->office_automation_to_do_type_id, // جهت
                "user_id" => $post_user->user_id,
                "status_id" => 5250002, // در جریان
            ]);

            DashboardController::sendSms($office_automation_action, "creatework");

            // اضافه کردن کار به لیست کاربران

            $office_automation_user = OfficeAutomationUser::where([
                "office_automation_work_id" => $office_automation_work->id,
                "user_id" => $post_user->user_id,
            ])->first();

            if ($office_automation_user) {
                $office_automation_user->status_id = 5250002; // در جریان
                $office_automation_user->save();
            } else {
                OfficeAutomationUser::create([
                    "office_automation_work_id" => $office_automation_work->id,
                    "user_id" => $post_user->user_id,
                    "status_id" => 5250002, // در جریان
                    "priority_id" => $request->priority_id,
                    "input_or_create" => 2
                ]);
            }


        }

        // لیست کارها جهت مشاهده
        if ($post_user_view) {
            $post_user_operation_view = PostUser::whereIn("id", $post_user_view)->where("user_id", "!=", Auth::id())->get();
            foreach ($post_user_operation_view as $post_user) {
                $office_automation_action = OfficeAutomationAction::create([
                    "office_automation_work_id" => $office_automation_work->id,
                    "office_automation_to_do_list_id" => $office_automation_to_do_list->id,
                    "office_automation_to_do_type_id" => 1, // جهت مشاهده
                    "user_id" => $post_user->user_id,
                    "status_id" => 5250011 // در انتظار مشاهده
                ]);

                DashboardController::sendSms($office_automation_action, "creatework");
                // اضافه کردن کار به لیست کاربران
                $office_automation_user = OfficeAutomationUser::where([
                    "office_automation_work_id" => $office_automation_work->id,
                    "user_id" => $post_user->user_id,
                ])->first();

                if ($office_automation_user) {
                    $office_automation_user->status_id = 5250002; // در جریان
                    $office_automation_user->save();
                } else {
                    OfficeAutomationUser::create([
                        "office_automation_work_id" => $office_automation_work->id,
                        "user_id" => $post_user->user_id,
                        "status_id" => 5250002, // در جریان
                        "priority_id" => $request->priority_id,
                        "input_or_create" => 2
                    ]);
                }
            }
        }

        session([
            "caption_office_automation" => null,
            "description_office_automation" => null,
        ]);

        return redirect()->route($this->route_path . "index")->with(["success" => "یک کار با موفقیت تعریف گردید."]);

    }

    public function create_to_do(OfficeAutomationToDoList $office_automation_to_do_list)
    {

        $post_option = Option::get("post_user");
        $office_automation_to_do_type_option = Option::get("office_automation_to_do_type", session("to_do_type_office_automation") ?? 0, 0, [1]);
        $priority_option = Option::get("priority", $office_automation_to_do_list->priority_id ?? session("priority_office_automation") ?? 0, 2);
        $current_user_id = Auth::id();
        $max_file_size = Setting::getIntegerValue("office_automation_max_file_size_in_mb");
        $caption = session("caption_office_automation") ?? $office_automation_to_do_list->office_automation_work->caption;
        $description = session("description_office_automation") ?? null;
        $end_datetime = session("end_datetime_office_automation") ?? null;

        return view($this->view_path . "create_to_do", compact("caption", "description", "end_datetime", "max_file_size", "post_option", "priority_option", "office_automation_to_do_type_option", "office_automation_to_do_list", "current_user_id"));

    }

    public function store_to_do(Request $request)
    {
        // return nl2br($request->description);
        // return $request->all();
        //   return $request->work_file->extension();
        session([
            "caption_office_automation" => $request->caption ?? "",
            "description_office_automation" => $request->description ?? "",
            "end_datetime_office_automation" => $request->end_datetime ?? "",
            "to_do_type_office_automation" => $request->office_automation_to_do_type_id ?? null,
            "priority_office_automation" => $request->priority_id ?? null,
        ]);
        $current_user_id = Auth::id();
        $office_automation_to_do_list = OfficeAutomationToDoList::find($request->office_automation_to_do_list_id);
        if (!$office_automation_to_do_list) {
            return back()->withErrors("اطلاعات به درستی وارد نشده است.");
        }

        $office_automation_action = OfficeAutomationAction::where([
            "office_automation_to_do_list_id" => $office_automation_to_do_list->id,
            "user_id" => $current_user_id
        ])->
        where("user_id", "!=", $office_automation_to_do_list->office_automation_work->user_id)->
        first();


        $post_user_operation = $request->post_user_operation;
        $post_user_view = $request->post_user_view;

        if (!$post_user_operation || count($post_user_operation) <= 0) {
            return back()->withErrors("لطفا حداقل یک نفر جهت اقدام انتخاب نمایید.");
        }

        if (Str::length($request->caption) > 50) {
            return back()->withErrors("عنوان کار نباید بیش از 50 کاراکتر باشد");
        }
        $substr = substr_count($request->caption, ' ');
        if ($substr > 4) {
            return back()->withErrors("عنوان کار حداکثر می تواند دارای 4 کاراکتر  Space باشد");
        }
        $result = DashboardController::checkFiles($request);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }
        if (!$request->end_datetime) {
            session([
                "end_datetime_office_automation" => $request->end_datetime ?? "",
            ]);

            return back()->withErrors("لطفا تاریخ پایان را انتخاب نمایید.");
        }

        $to_do_list = OfficeAutomationToDoList::find($request->office_automation_to_do_list_id);
        if (!$to_do_list) {
            return back()->withErrors("اطلاعات به درستی وارد نشده است.");
        }

        // لیست کارها جهت اقدام
        $post_user_operation_list = PostUser::
        whereIn("id", $post_user_operation)->
        get();

        // لیست کارها جهت مشاهده
        if (!$post_user_view) {

            $post_user_view [] = -1;
        }

        $post_user_operation_view = PostUser::whereIn("id", $post_user_view)->where("user_id", "!=", Auth::id())->get();

        if (count($post_user_operation_list) == 0 && count($post_user_operation_view) == 0) {
            return back()->withErrors("لیست اقدام کننده ها و رونوشت به صورت هم زمان نمی توانند خالی باشند.");
        }

        $office_automation_work = $to_do_list->office_automation_work;
        // تعریف ارجاع
        $office_automation_to_do_list = OfficeAutomationToDoList::create([
            "office_automation_work_id" => $office_automation_work->id,
            "user_id" => $current_user_id,
            "status_id" => 5250002, // در جریان
            "priority_id" => $request->priority_id,
            "end_datetime" => $request->end_datetime,
            "caption" => $request->caption,
            "description" => nl2br($request->description),
            "office_automation_action_id" => $office_automation_action->id ?? null
        ]);

        event(new OfficeAutomationLogEvent($office_automation_work, $office_automation_to_do_list, null, 5250002, "", $request));


        // لیست کارها جهت اقدام
        $post_user_operation_list = PostUser::
        whereIn("id", $post_user_operation)->
        where("user_id", "!=", $office_automation_to_do_list->office_automation_work->user_id)->
        get();

        foreach ($post_user_operation_list as $post_user) {

            $office_automation_action = OfficeAutomationAction::create([
                "office_automation_work_id" => $office_automation_work->id,
                "office_automation_to_do_list_id" => $office_automation_to_do_list->id,
                "office_automation_to_do_type_id" => $request->office_automation_to_do_type_id, // جهت
                "user_id" => $post_user->user_id,
                "status_id" => 5250002, // در جریان
            ]);
            DashboardController::sendSms($office_automation_action, "creatework");
            $office_automation_user = OfficeAutomationUser::where([
                "office_automation_work_id" => $office_automation_work->id,
                "user_id" => $post_user->user_id,
            ])->first();

            if ($office_automation_user) {
                $office_automation_user->status_id = 5250002; // در جریان
                $office_automation_user->save();
            } else {
                OfficeAutomationUser::create([
                    "office_automation_work_id" => $office_automation_work->id,
                    "user_id" => $post_user->user_id,
                    "status_id" => 5250002, // در جریان
                    "priority_id" => $request->priority_id,
                    "input_or_create" => 2
                ]);
            }

        }


        foreach ($post_user_operation_view as $post_user) {

            $office_automation_action = OfficeAutomationAction::create([
                "office_automation_work_id" => $office_automation_work->id,
                "office_automation_to_do_list_id" => $office_automation_to_do_list->id,
                "office_automation_to_do_type_id" => 1, // جهت مشاهده
                "user_id" => $post_user->user_id,
                "status_id" => 5250011 // در انتظار مشاهده
            ]);
            DashboardController::sendSms($office_automation_action, "creatework");

            // اضافه کردن کار به لیست کاربران
            $office_automation_user = OfficeAutomationUser::where([
                "office_automation_work_id" => $office_automation_work->id,
                "user_id" => $post_user->user_id,
            ])->first();

            if ($office_automation_user) {
                $office_automation_user->status_id = 5250002; // در جریان
                $office_automation_user->save();
            } else {
                OfficeAutomationUser::create([
                    "office_automation_work_id" => $office_automation_work->id,
                    "user_id" => $post_user->user_id,
                    "status_id" => 5250002, // در جریان
                    "priority_id" => $request->priority_id,
                    "input_or_create" => 2
                ]);
            }
        }
        session([
            "caption_office_automation" => null,
            "description_office_automation" => null,
        ]);

        return redirect()->route($this->route_path . "view", [
            $to_do_list->office_automation_work_id,
            $current_user_id
        ])->with(["success" => "یک کار با موفقیت تعریف گردید."]);

    }

    public function download(OfficeAutomationWork $office_automation_work, OfficeAutomationFile $office_automation_file)
    {

        if ($office_automation_file->office_automation_work_id != $office_automation_work->id) {
            return back()->withErrors("لینک فایل معتبر نمی باشد.");
        }

        $path = public_path($office_automation_file->file->path);
        $fileName = $office_automation_file->file->caption;

        return Response::download($path, $fileName, ['Content-Type: application']);


    }

    public function print_office(OfficeAutomationWork $office_automation_work, $key)
    {
        if ($office_automation_work->random != $key) {
            return back()->withErrors("کار مورد نظر در کارتابل شما وجود ندارد.");
        }

        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        $result = $this->create_pdf_file($office_automation_work, $worker, "print");

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        Pdf::labelPrinter($result["html"], "L", $office_automation_work->id . ".pdf", "A4",
            $result["print_file"]);
        return back()->with(["success" => "جهت دریافت فایل پرینت شده، به محل پرینتر " . $worker->default_printer->caption . " مراجعه فرمایید."]);

    }

    public function create_pdf_file(OfficeAutomationWork $office_automation_work, Worker $worker, $type)
    {
        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = route("DCWT_ShortLink", [$office_automation_work, $office_automation_work->random]);

        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);
        $view_path = "utility.office_automation..print.";
        $software_name = Setting::getStringValue("software_name");
        $qr = QrCode::size(100)->generate($url);

        $html[0] = view($view_path . "._print_info",
            compact("office_automation_work", "software_name", "qr"))->render();
        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $office_automation_work->code . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => 0,
                "printer_id" => $worker->default_printer_id,
                "number_of_prints" => 1
            ]);

        }

        return ["result" => true, "html" => $html, "print_file" => $print_file];
    }

    public static function checkFiles(Request $request)
    {
        $size_byte = 0;
        $max_size_mb = Setting::getIntegerValue("office_automation_max_file_size_in_mb");
        if (isset($request->work_file)) {
            foreach ($request->file('work_file') as $work_file_item) {
                $size_byte += $work_file_item->getSize();
                if (!in_array(File::get_file_extension($work_file_item->getClientOriginalName()), [
                    "pdf",
                    "xls",
                    "xlsx",
                    "doc",
                    "docx",
                    "png",
                    "jpg",
                    "zip",
                    "txt"
                ])) {
                    return ["result" => false, "message" => "فرمت فایل بارگذاری شده قابل قبول نیست"];
                }

                if ($size_byte / 1024 / 1024 > $max_size_mb) {

                    return [
                        "result" => false,
                        "message" => "حداکثر اندازه فایل جهت بارگذاری " . $max_size_mb . " مگابایت می باشد."
                    ];

                }

            }
        }

        return ["result" => true];
    }

    public function checkPermission(OfficeAutomationWork $office_automation_work, $user_id)
    {

        if ($office_automation_work->user_id == $user_id) {
            return "";
        }
        $count = OfficeAutomationToDoList::where("user_id", $user_id)->
        where("office_automation_work_id", $office_automation_work->id)->
        count();
        if ($count != 0) {
            return "";
        }
        $count = OfficeAutomationAction::where("user_id", $user_id)->

        where("office_automation_work_id", $office_automation_work->id)->
        count();
        if ($count != 0) {
            return "";
        }

        return back()->withErrors("کار مورد نظر در کارتابل شما وجود ندارد.");
    }

    public static function sendSms(OfficeAutomationAction $office_automation_action, $action)
    {
        $office_automation_priority_sms = Setting::getStringValue("office_automation_priority_sms");
        $office_automation_priority_sms = json_decode($office_automation_priority_sms, true);

        if (!isset($office_automation_priority_sms[$office_automation_action->office_automation_to_do_list->priority_id][$action])) {
            return "NotSend";
        }

        $template = "officeautomation" . $action;
        $worker = null;

        switch ($action) {
            case "creatework":
                $token = $office_automation_action->office_automation_to_do_list->getCode();
                $token2 = $office_automation_action->office_automation_to_do_type->caption;

                $token3 = "_APP_NAME_/DCWT/" . $office_automation_action->office_automation_work->id . "/" .
                    $office_automation_action->office_automation_work->random;

                $token10 = $office_automation_action->worker->cooperation_type->caption2 . ".گرامی." . $office_automation_action->worker->fullName();
                $token20 = $office_automation_action->office_automation_to_do_list->caption;

                $worker = $office_automation_action->worker;
                break;

            case "workdone":

                $token = $office_automation_action->office_automation_to_do_list->getCode();
                $token2 = $office_automation_action->worker->fullName();

                $token3 = "_APP_NAME_/DCWT/" . $office_automation_action->office_automation_work->id . "/" .
                    $office_automation_action->office_automation_work->random;

                $token10 = $office_automation_action->office_automation_to_do_list->worker->cooperation_type->caption2 . ".گرامی." . $office_automation_action->office_automation_to_do_list->worker->fullName();
                $token20 = $office_automation_action->office_automation_to_do_list->caption;

                $worker = $office_automation_action->office_automation_to_do_list->worker;

                break;

            case "confirm":
            case "reject":

                $token = $office_automation_action->office_automation_to_do_list->getCode();
                $token2 = $office_automation_action->office_automation_to_do_list->worker->fullName();

                $token3 = "_APP_NAME_/DCWT/" . $office_automation_action->office_automation_work->id . "/" .
                    $office_automation_action->office_automation_work->random;

                $token10 = $office_automation_action->worker->cooperation_type->caption2 . ".گرامی." . $office_automation_action->worker->fullName();
                $token20 = $office_automation_action->office_automation_to_do_list->caption;

                $worker = $office_automation_action->worker;

                break;
        }


        Notification::send(
            "00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
            new SMSNotification($template, $token, $token2, $token3, $token10, $token20)
        );


    }
}
