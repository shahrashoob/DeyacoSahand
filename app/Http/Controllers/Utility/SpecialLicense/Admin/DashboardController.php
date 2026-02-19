<?php

namespace App\Http\Controllers\Utility\SpecialLicense\Admin;

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
    private $view_path = "utility.special_license.admin.dashboard.";
    private $route_path = "utility.special_license.admin.dashboard.";

    public function index(Request $request)
    {

        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
            $special_license_type_id = $request->special_license_type_id;
            $status_id = $request->status_id;
            $start_datetime = $request->start_datetime;
            $end_datetime = $request->end_datetime;
            $user_id = $request->user_id;
        } else {
            $search = session("search_special_license");
            $order_by = session("order_by_special_license") ?? "id__desc";
            $status_id = session("status_id_special_license") ?? null;
            $special_license_type_id = session("special_license_type_id_special_license");
            $start_datetime = session("start_datetime_special_license");
            $end_datetime = session("end_datetime_special_license");
            $user_id = session("user_id_special_license");
        }

        session([
            "search_special_license" => $search,
            "order_by_special_license" => $order_by,
            "status_id_special_license" => $status_id,
            "special_license_type_id_special_license" => $special_license_type_id,
            "start_datetime_special_license" => $start_datetime,
            "end_datetime_special_license" => $end_datetime,
            "user_id_special_license" => $user_id,
        ]);

        $list = SpecialLicense::
        join("special_license_confirmation", function ($join) {
            $join->on("special_licenses.id", "=", "special_license_id");
        })->
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
        when($user_id, function ($query) use ($user_id) {
            return $query->where("special_licenses.user_id", $user_id);
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

        $order_by_Option = Option::OrderBy("special_license", $special_license_type_id);
        $status_Option = Option::get("status_in_ids", $status_id, 0, [6040001, 6040002, 6040003]);
        $worker_option = Option::get("worker", $user_id, 1);
        $special_license_type_option = Option::get("special_license_type", $special_license_type_id);

        return view($this->view_path . "index", compact("list", "order_by_Option", "status_Option",
            "special_license_type_option", "search", "start_datetime", "end_datetime","worker_option"));

    }

    public function view(SpecialLicense $special_license)
    {


        $special_license_type = $special_license->special_license_type;
        $worker = $special_license->worker;
        $reference = $special_license->GetReference();
        $confirmation_worker = Worker::find(Auth::id());
        $software_name=Setting::getStringValue("software_name");

        return view($this->view_path . "view", compact("special_license", "worker",
            "special_license_type", "reference","software_name",
            "confirmation_worker"));
    }


}
