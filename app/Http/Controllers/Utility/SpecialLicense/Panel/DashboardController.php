<?php

namespace App\Http\Controllers\Utility\SpecialLicense\Panel;

use App\Events\Utility\SpecialLicenseEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Carrier\CarrierGroup;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\SpecialLicense\SpecialLicenseConfirmation;
use App\Models\Utility\Unit;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    //
    private $view_path = "utility.special_license.panel.dashboard.";
    private $route_path = "utility.special_license.panel.dashboard.";

    public function index(Request $request)
    {

        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $post_ids[] = -1;
        $user_id = Auth::id();

        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
            $special_license_type_id = $request->special_license_type_id;
            $status_id = $request->status_id;
            $start_datetime = $request->start_datetime;
            $end_datetime = $request->end_datetime;
        } else {
            $search = session("search_special_license");
            $order_by = session("order_by_special_license") ?? "id__desc";
            $status_id = session("status_id_special_license") ?? null;
            $special_license_type_id = session("special_license_type_id_special_license");
            $start_datetime = session("start_datetime_special_license");
            $end_datetime = session("end_datetime_special_license");
        }

        session([
            "search_special_license" => $search,
            "order_by_special_license" => $order_by,
            "status_id_special_license" => $status_id,
            "special_license_type_id_special_license" => $special_license_type_id,
            "start_datetime_special_license" => $start_datetime,
            "end_datetime_special_license" => $end_datetime,
        ]);

        $list = SpecialLicense::
        join("special_license_confirmation", function ($join) {
            $join->on("special_licenses.id", "=", "special_license_id");
            $join->on("current_priority_number", "=", "priority_number");
        })->
        where(function ($query) use ($post_ids, $user_id) {
            return $query->whereIn("post_id", $post_ids);
        })->
        WhereNull("special_license_confirmation.user_id")->
        when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                return $query->where("code", "like", "%" . $search . "%");
            });
        })->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);
        })->
        when($status_id, function ($query) use ($status_id) {
            return $query->where("special_licenses.status_id", $status_id);
        })->
        when($special_license_type_id, function ($query) use ($special_license_type_id) {
            return $query->where("special_licenses.special_license_type_id", $special_license_type_id);
        })->
        when($start_datetime, function ($query) use ($start_datetime) {
            return $query->where("special_licenses.created_at", ">=", $start_datetime);
        })->
        when($end_datetime, function ($query) use ($end_datetime) {
            $end_datetime = Carbon::parse($end_datetime)->addDay();
            return $query->where("special_licenses.created_at", "<=", $end_datetime);
        })->
        groupBy("special_licenses.id")->
        select("special_licenses.*")->
        paginate();

        $order_by_Option = Option::OrderBy("special_license", $order_by);
        $status_Option = Option::get("status_in_ids", $status_id, 0, [6040001, 6040002, 6040003]);
        $special_license_type_option = Option::get("special_license_type", $special_license_type_id);

        return view($this->view_path . "index", compact("list", "order_by_Option", "status_Option",
            "special_license_type_option", "search", "start_datetime", "end_datetime"));

    }

    public function my_license(Request $request)
    {

        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $post_ids[] = -1;
        $user_id = Auth::id();

        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
            $special_license_type_id = $request->special_license_type_id;
            $status_id = $request->status_id;
            $start_datetime = $request->start_datetime;
            $end_datetime = $request->end_datetime;
        } else {
            $search = session("search_special_license");
            $order_by = session("order_by_special_license") ?? "id__desc";
            $status_id = session("status_id_special_license") ?? null;
            $special_license_type_id = session("special_license_type_id_special_license");
            $start_datetime = session("start_datetime_special_license");
            $end_datetime = session("end_datetime_special_license");
        }

        session([
            "search_special_license" => $search,
            "order_by_special_license" => $order_by,
            "status_id_special_license" => $status_id,
            "special_license_type_id_special_license" => $special_license_type_id,
            "start_datetime_special_license" => $start_datetime,
            "end_datetime_special_license" => $end_datetime,
        ]);

        $list = SpecialLicense::
        where("user_id", $user_id)->
        when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                return $query->where("code", "like", "%" . $search . "%");
            });
        })->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);
        })->
        when($status_id, function ($query) use ($status_id) {
            return $query->where("special_licenses.status_id", $status_id);
        })->
        when($special_license_type_id, function ($query) use ($special_license_type_id) {
            return $query->where("special_licenses.special_license_type_id", $special_license_type_id);
        })->
        when($start_datetime, function ($query) use ($start_datetime) {
            return $query->where("special_licenses.created_at", ">=", $start_datetime);
        })->
        when($end_datetime, function ($query) use ($end_datetime) {
            $end_datetime = Carbon::parse($end_datetime)->addDay();
            return $query->where("special_licenses.created_at", "<=", $end_datetime);
        })->
        groupBy("special_licenses.id")->
        select("special_licenses.*")->
        paginate();

        $order_by_Option = Option::OrderBy("special_license", $order_by);
        $status_Option = Option::get("status_in_ids", $status_id, 0, [6040001, 6040002, 6040003]);
        $special_license_type_option = Option::get("special_license_type", $special_license_type_id);

        return view($this->view_path . "my_license", compact("list", "order_by_Option", "status_Option",
            "special_license_type_option", "search", "start_datetime", "end_datetime"));

    }

    public function view(SpecialLicense $special_license)
    {
        if ($special_license->user_id != Auth::id()) {
            return redirect()->route($this->route_path . "view_confirm", $special_license);
        }

        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $post_ids[] = -1;

        $confirmation_count = SpecialLicenseConfirmation::where([
            "special_license_id" => $special_license->id
        ])->
        orWhere("user_id", Auth::id())->
        count();
        if ($confirmation_count <= 0) {
            return back()->withErrors("شما امکان مشاهده مجوز مورد نظر را ندارید.");

        }


        $special_license_type = $special_license->special_license_type;
        $worker = $special_license->worker;
        $reference = $special_license->GetReference();
        $confirmation_worker = Worker::find(Auth::id());
        $software_name = Setting::getStringValue("software_name");


        return view($this->view_path . "view", compact("special_license", "worker",
            "special_license_type", "reference", "software_name",
            "confirmation_worker"));
    }

    public function view_confirm(SpecialLicense $special_license)
    {

        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $post_ids[] = -1;

        $result = $this->permission($special_license, $post_ids, true);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }


        $special_license_type = $special_license->special_license_type;
        $worker = $special_license->worker;
        $reference = $special_license->GetReference();
        $confirmation_worker = Worker::find(Auth::id());
        $software_name = Setting::getStringValue("software_name");
        return view($this->view_path . "view_confirm", compact("special_license", "worker",
            "special_license_type", "reference", "software_name",
            "confirmation_worker"));
    }

    public function DCSL_QR(SpecialLicense $special_license)
    {
        return $this->view_confirm($special_license);
    }

    public function confirm(Request $request, SpecialLicense $special_license)
    {

        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $post_ids[] = -1;
        $confirm_user_id = Auth::id();
        $result = $this->permission($special_license, $post_ids, true);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $special_license_confirmation_list = SpecialLicenseConfirmation::
        join("special_licenses", function ($join) {
            $join->on("special_licenses.id", "=", "special_license_id");
            //$join->on("current_priority_number", "=", "priority_number");
        })->
        where([
            "special_license_id" => $special_license->id
        ])->
        whereIn("post_id", $post_ids)->
        select("special_license_confirmation.*")->
        get();
        $event_id = 0;
        $message = "";

        foreach ($special_license_confirmation_list as $special_license_confirmation) {
            switch ($request->confirm_type) {
                case "confirm":
                    if ($special_license->user_id == $confirm_user_id) {
                        return back()->withErrors("امکان تایید درخواست برای شما وجود ندارد.");
                    }
                    $special_license_confirmation->user_id = $confirm_user_id;
                    $special_license_confirmation->status_id = 6040202; // تایید خبره
                    $special_license_confirmation->save();

                    $event_id = 6040002; // تایید خبره
                    break;
                case "reject":
                    if ($special_license->user_id == $confirm_user_id) {
                        return back()->withErrors("امکان تایید درخواست برای شما وجود ندارد.");
                    }
                    $special_license_confirmation->user_id = $confirm_user_id;
                    $special_license_confirmation->status_id = 6040203; // عدم تایید خبره
                    $special_license_confirmation->save();

                    $event_id = 6040003; // عدم تایید خبره
                    break;
                case "send_to_parent":

                    if ($special_license->user_id != $confirm_user_id) {
                        return back()->withErrors("امکان تایید درخواست برای شما وجود ندارد.");
                    }

                    // هر پستی که باید کاربر تایید کند به پست مافوق تغییر می کند.
                    $parent_ids = Post::whereIn("id", $post_ids)->
                    whereNotNull("parent_id")->
                    pluck("parent_id")->
                    toArray();
                    if (count($post_ids) == 0) {
                        return back()->withErrors("برای شما هیچ پست مافوقی در سامانه تعریف نشده است.");
                    }
                    $has_any_post_where_before_confirm =
                        SpecialLicenseConfirmation::
                        join("special_licenses", function ($join) {
                            $join->on("special_licenses.id", "=", "special_license_id");
                        })->
                        where([
                            "special_license_id" => $special_license->id
                        ])->
                        whereIn("post_id", $parent_ids)->
                        whereNotNull("special_license_confirmation.user_id")-> // آن پست قبلا تایید کرده باشد
                        where("special_license_confirmation.status_id", 6040202)->// تایید شده
                        select("special_license_confirmation.*")->
                        first();
                    if ($has_any_post_where_before_confirm) {
                        $special_license_confirmation->post_id = $has_any_post_where_before_confirm->post_id;
                        $special_license_confirmation->user_id = $has_any_post_where_before_confirm->user_id;
                        $special_license_confirmation->status_id = 6040202; // تایید خبره
                        $special_license_confirmation->save();
                    } else {
                        $special_license_confirmation->post_id = $parent_ids[0];
                        $special_license_confirmation->save();
                        $message = "<br/>" . "ارسال درخواست برای " . (Post::find($parent_ids[0])->caption);
                    }

                    $event_id = 6040004; // ارسال برای مافوق
                    break;
                default:
                    return back()->withErrors("اطلاعات ثبت نشده، لطفا دوباره تلاش کنید.");
            }
        }

        if ($request->confirm_type == "send_to_parent") {
            $special_license = SpecialLicense::UpdateSpecialLicense($special_license);
            event(new SpecialLicenseEvent($special_license, $event_id, null, null, null, $request->description . $message));
            return redirect()->route($this->route_path . "index")->with(["success" => "نظر شما برای تایید به پست مافوق ارسال گردید."]);

        } else {
            $special_license = SpecialLicense::UpdateSpecialLicense($special_license);
            event(new SpecialLicenseEvent($special_license, $event_id, null, null, null, $request->description . $message));
            return redirect()->route($this->route_path . "index")->with(["success" => "نظر شما با موفقیت ثبت گردد."]);
        }
    }

    public function permission($special_license, $post_ids, $check_confirm = false)
    {


        if ($check_confirm) {

            $allow_confirmation = SpecialLicense::
            join("special_license_confirmation", function ($join) {
                $join->on("special_licenses.id", "=", "special_license_id");
                $join->on("current_priority_number", "=", "priority_number");
            })->
            where([
                "special_license_id" => $special_license->id
            ])->
            whereIn("post_id", $post_ids)->

            count();
            if ($allow_confirmation <= 0) {
                return [
                    "result" => false,
                    "error" => "شما امکان مشاهده مجوز مورد نظر را ندارید."
                ];
            }
        }

        return [
            "result" => true,
        ];
    }
}
