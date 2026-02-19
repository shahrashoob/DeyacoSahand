<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\File\File;
use App\Models\HR\Employment\Employment;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\User;
use App\Models\HR\User\UserDevice;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WorkerController extends Controller
{

    var $view_path = "hr.worker.";

    public function index(Request $request)
    {
        $post_user = Auth::user()->posts->first();
        $show_subset = $post_user->checkButtonPermission("hr.worker.show_subset_users");
        $show_all_list = $post_user->checkButtonPermission("hr.worker.list");
        if (!($show_all_list || $show_subset)) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }
        $user_ids = [];
        if (!$show_all_list && $show_subset) {
            $user_ids = self::GetSubsetUser();
        }
        if ($request->isMethod('post')) {
            $search = $request->search;

        } else {
            $search = session("search");
        }
        session([
            "search" => $search,

        ]);
        $list = Worker::
        whereIn("cooperation_type_id", $post_user->post->get_cooperation_status_permission())->
        whereNotIn("cooperation_type_id", [2,3,6,])->
        where("user_type_id", 1)->
        when(!$show_all_list && $show_subset, function ($query) use ($user_ids) {
            return $query->whereIn("id", $user_ids);
        })->
        when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                return $query->where("firstname", "like", "%" . $search . "%")->
                orwhere("lastname", "like", "%" . $search . "%")->
                orwhere("national_code", "like", "%" . $search . "%");
            });
        })->
        paginate(900);

        return view("hr.worker.list", compact("list", 'search'));
    }

    public function edit(Worker $worker)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.worker.edit") || !self::AllowUserShow($post_user, $worker->id)) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید (1001)");
        }
        if ($worker->cooperation_type_id == 3) {
            # مشتری
            return back()->withErrors("برای ویرایش مشخصات کاربران با نوع همکاری مشتری از منو لیست مشتریان اقدام فرمایید.");
        }
        if ($worker->cooperation_type_id == 6) {
            # تامین کننده
            return back()->withErrors("برای ویرایش مشخصات کاربران با نوع همکاری تامین کننده از منو لیست تامین کنندگان اقدام فرمایید.");
        }
        if ($worker->cooperation_type_id == 2) {
            # پیمانکار
            return back()->withErrors("برای ویرایش مشخصات کاربران با نوع همکاری پیمانکار از منو لیست پیمانکاران اقدام فرمایید.");
        }
        $post_user = Auth::user()->posts->first();
//        if (!$post_user->checkButtonPermission("hr.worker.edit")) {
//            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
//        }
        $country_option = Option::get("country", $worker->country_id);
        $one_time_token_status_option = Option::get("status", $worker->one_time_token_status_id, 1100);
        $cooperation_type_option = Option::get("cooperation_type", $worker->cooperation_type_id, 0, [1, 2, 4]);


        return view("hr.worker.edit", compact("worker", "post_user", "country_option", "cooperation_type_option", "one_time_token_status_option"));
    }

    public function update_info(Request $request, Worker $worker)
    {

        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.worker.edit") || !self::AllowUserShow($post_user, $worker->id)) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }


        if (!in_array($request->cooperation_type_id, $post_user->post->get_cooperation_status_permission())) {
            return back()->withErrors("شما اجازه دسترسی به نوع همکاری  انتخاب شده، را ندارید. ");
        }
        if ($worker->cooperation_type_id == 3) {
            # مشتری
            return back()->withErrors("برای ویرایش مشخصات کاربران با نوع همکاری مشتری از منو لیست مشتریان اقدام فرمایید.");
        }
        // بررسی اینکه بعد از تغییر نوع همکاری، تمامی پست های فرد با نوع همکاری پست هایی که دارد، مطابقت دارد یا خیر
        $post_user_list = PostUser::where("user_id", $worker->id)->get();
        foreach ($post_user_list as $item) {

            if (!$item->post->has_cooperation_type($request->cooperation_type_id)) {
                return back()->withErrors("با توجه به اینکه نوع همکاری شاغل با نوع همکاری " . $item->post->caption . "، مطابقت ندارد، امکان ثبت تغییرات وجود ندارد.");
            }
        }



//        //ارسال پیامک برای ناظر ها
//        $supervisor=Post::where("is_system_supervisor",1)->get();
//        foreach ($supervisor as $item){
//            foreach ($item->worker as $super_worker){
//
//                Notification::send(
//                    "00" . ( $super_worker->country->area_code ?? "98" ) . $super_worker->mobile,
//                    new SMSNotification( "supervisoralertforcreateuser", "ERP", null ,null,$worker->fullname()) );
//
//            }
//
//        }

        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.worker.edit")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }


        if (isset($request->image_file)) {

            $file = File::uploadFile($request->file('image_file'), $worker->id . "_" . rand(1000, 9000) . ".png", 41, "upload/worker/", true);

            $worker->image_id = $file->id;
            $worker->save();
        }


        if (Worker::where([
            "national_code" => $request->national_code,
            "cooperation_type_id" => $request->cooperation_type_id
        ])->where("id", "!=", $worker->id)->exists()) {
            return back()->withErrors("کد ملی مشابه در سیستم وجود دارد");
        }
        if (Worker::where("email", $request->email)->where("id", "!=", $worker->id)->exists()) {
            return back()->withErrors("نام کاربری  مشابه در سیستم وجود دارد");
        }
        $check_user_name = Worker::CheckUserName($request->email);
        if (!$check_user_name["result"]) {
            return back()->withErrors($check_user_name["error"]);
        }

        if (Worker::where([
            "mobile" => $request->mobile,
            "cooperation_type_id" => $request->cooperation_type_id
        ])->where("id", "!=", $worker->id)->exists()) {
            return back()->withErrors("شماره موبایل مشابه در سیستم وجود دارد");
        }
        if ($request["password"] != "" && $request["password"] == $request["confirm_password"]) {
            $password = $request['password'];
            $request["password"] = Hash::make($password);
            $request["required_reset_password"] = 1;
            $worker->update($request->all());
            Notification::send(
                "00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
                new SMSNotification("changepass", $worker->email, $password, null, $worker->fullname()));
        } else {
            unset($request["password"]);
        }
        $request["is_possible_to_work_remotely"] = $request->is_possible_to_work_remotely ? 1 : 0;
        $worker->update($request->all());
        UserDevice::where("user_id", $worker->id)->delete();

        return redirect()->route("hr.worker.index")->with(["success" => "تغییرات با موفقیت ثبت شد."]);
    }


    public function create()
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.worker.create")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }


        $country_option = Option::get("country");
        $cooperation_type_option = Option::get("cooperation_type", 0, 0, [1, 2, 4]);
        $one_time_token_status_option = Option::get("status", 1210, 1100);

        return view("hr.worker.create", compact("country_option", "cooperation_type_option", "post_user", "one_time_token_status_option"));
    }

    public function store(Request $request)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.worker.create")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }


        $password = "***";
        if (Worker::where("national_code", "like", $request->national_code)->exists()) {
            return back()->withErrors("کد ملی مشابه در سیستم وجود دارد");
        }
        if (Worker::where("email", "like", $request->email)->exists()) {
            return back()->withErrors("نام کاربری  مشابه در سیستم وجود دارد");
        }
        if (Worker::where("mobile", "like", $request->mobile)->exists()) {
            return back()->withErrors("شماره موبایل  مشابه در سیستم وجود دارد");
        }

        if ($request["password"] != "" && $request["password"] == $request["confirm_password"]) {
            $password = $request["password"];
            $request["password"] = Hash::make($request['password']);
            $request["required_reset_password"] = 1;
        } else {
            unset($request["password"]);
        }

        $worker = Worker::create($request->all());
        $token3 = "_APP_NAME_";
        Notification::send(
            "00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
            new SMSNotification("createuser", $worker->email, $password, $token3, $worker->fullname()));


        //ارسال پیامک برای ناظر ها
        $supervisor = Post::where("is_system_supervisor", 1)->get();
        $company_name = Setting::where("key", "software_name")->first(); // is_active_sms_module

        foreach ($supervisor as $item) {

            foreach ($item->worker as $super_worker) {

                Notification::send(
                    "00" . ($super_worker->mobile_country->area_code ?? "98") . $super_worker->mobile,
                    new SMSNotification("supervisoralertforcreateuser", $company_name->string_value ?? "***", null, null, $worker->fullname()));

            }

        }

        return redirect()->route("hr.worker.create")->with(["success" => "یک کاربر با موفقیت ثبت شد."]);

    }

    public function print_worker_card(Worker $worker)
    {

        $current_worker = Worker::find(Auth::user()->id);
        if (!$current_worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        $post_user = Auth::user()->posts->first();
        self::AllowUserShow($post_user, $worker->id);
        if (!self::AllowUserShow($post_user, $worker->id)) {
            return redirect()->route("hr.worker.index")->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }
        $static_ip = Setting::getStringValue("static_ip");
        $local_ip = url("");

        $url = route("Personal", [$worker, $worker->getRandom()]);

        //اگر شرکت دارای ای پی بیرونی و ای پی لوکال باشد، لینک را بر روی ای پی بیرونی تنظیم می کنیم.
        if ($static_ip != "") {
            $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);
        }

        $qr = QrCode::size(80)->generate($url);

        $post_user = PostUser::where("user_id", $worker->id)->get();

        $software_name = Setting::getStringValue("company_name");
        $html[0] = view($this->view_path . "print._head")->render();
        $html[0] .= view($this->view_path . "print._print_info", compact("worker", "post_user", "qr", "software_name"))->render() . $html[0];
        $html[0] .= view($this->view_path . "print._footer")->render();

        $print_file = PrinterFile::create([
            "user_id" => $worker->id,
            "filename" => $worker->national_code . ".pdf",
            "status_id" => 305001, // در انتظار دانلود
            "is_landscape" => 0,
            "printer_id" => $current_worker->default_label_printer_id
        ]);
        Pdf::labelPrinter($html,
            "L",
            $worker->national_code . ".pdf", [
                60,
                93
            ],
            $print_file
        );

        return back()->with(["success" => "پرینت کارت پرسنلی برای چاپ به پرینتر پیش فرض سیستم ارسال شد."]);
    }

    public static function GetSubsetUser()
    {
        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $child_post_ids = Post::whereIn("parent_id", $post_ids)->pluck("id")->toArray();
        foreach ($child_post_ids as $post_id) {
            $post_ids[] = $post_id;
        }
        $user_ids = PostUser::
        join("posts", "posts.id", "post_id")->
        whereIn("parent_id", $post_ids)->
        pluck("user_id")->toArray();
        $user_ids[] = -1;;
        return $user_ids;
    }

    public static function AllowUserShow($post_user, $user_id)
    {
        if ($post_user->checkButtonPermission("hr.worker.list")) {
            return true;
        }
        if (in_array($user_id, self::GetSubsetUser())) {
            return true;
        }
        return false;
    }
}
