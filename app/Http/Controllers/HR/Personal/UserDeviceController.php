<?php

namespace App\Http\Controllers\HR\Personal;

use App\Http\Controllers\Controller;
use App\Models\HR\User\UserDevice;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDeviceController extends Controller
{
    public static $info = [
        "route" => "hr.personal.user_device.",
        "enable_status" => ["001", "003", "005", "006", "008", "009", "010", "011","012"],
        "button" => [
            "caption" => " دستگاه های متصل",
            "class" => "btn btn-primary text-white",
            "icon" => "feather icon-log-in"
        ],
        "view_path" => "hr.personal.user_device.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "hr.personal.index";

    public function __construct()
    {
        $this->route_path = UserDeviceController::$info["route"];
        $this->view_path = UserDeviceController::$info["view_path"];
    }

    public function index(Worker $worker)
    {
        $result = ShiftWorkDayController::AllowUserShow($worker);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $list = UserDevice::where('user_id', $worker->id)->paginate();
        return view($this->view_path . "index", compact("worker", 'list'));
    }

    public function destroy(UserDevice $user_device)
    {
        $user_id = $user_device->user_id;
        $result = ShiftWorkDayController::AllowUserShow($user_device->worker);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $user_device->delete();
        return redirect()->route($this->route_path . "index", $user_id)->with(["success" => "یک دستگاه با موفقیت حذف گردید"]);
    }
}
