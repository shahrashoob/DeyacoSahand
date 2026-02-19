<?php

namespace App\Http\Controllers\LineProductStation\Carrier;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Carrier\CarrierLog;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public $view_path = "line_product_station.carrier.";
    public $route_path = "line_product_station.carrier.";

    public function index($code = "")
    {
        $list = Carrier::where("code", $code)->get();
        return view($this->view_path . "index", compact("list", "code"));
    }

    public function search(Request $request)
    {

        $code = $request->code;

        return redirect()->route($this->route_path . "index", $code);
    }

    public function edit(Carrier $carrier)
    {

        $status_option = Option::get("status", $carrier->status_id, 5320);

        $list = CarrierLog::where("carrier_id", $carrier->id)->orderByDesc('id')->paginate(50);
        $permission_update = $this->check_permission("line_product_station.carrier.carrier_type.update");
        $allow_edit = false;
        if ($permission_update["result"]) {
            $allow_edit = true;
        }
        return view($this->view_path . "edit", compact("carrier", "status_option", "list", "allow_edit"));
    }

    public function update(Request $request, Carrier $carrier)
    {
        $permission_update = $this->check_permission("line_product_station.carrier.carrier_type.update");
        if (!$permission_update["result"]) {
            return back()->withErrors($permission_update["error"]);
        }
        $carrier->SetStatus($request->status_id, null, 5320112);
        $carrier->log_message = "";
        $carrier->save();

        return redirect()->route($this->route_path . "index", $carrier->code)->with(["success" => "وضعیت با موفقیت ویرایش شد."]);
    }

    public function check_permission($route)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission($route, false, true)) {
            return [
                "result" => false,
                "error" => "دسترسی  عملیات برای شما تعریف نشده است",
            ];
        }
        return [
            "result" => true
        ];
    }
}
