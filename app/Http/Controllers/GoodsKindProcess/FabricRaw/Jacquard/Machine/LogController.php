<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralLogController;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.jacquard.machine.log.",
        "enable_status" => ["001", "002", "003", "004", "005", "006", "007", "008", "009", "010", "011", "012", "013", "014", "015", "016", "017", "018", "019", "020", "021", "022", "023", "024", "025", "026", "027", "028", "029", "030", "031", "032", "033", "034", "035", "036", "037", "038", "039", "040", "041", "042", "043", "044", "045", "046", "047", "048", "049", "050", "051", "052", "053", "054"],
        "button" => ["caption" => "مشاهده سابقه ماشین", "class" => "btn-info"],
        "view_path" => "goods_kind_process.fabric_raw.jacquard.machine.log.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = LogController::$info["route"];
        $this->view_path = LogController::$info["view_path"];
    }

    public function index(Request $request, Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        $publicController = $this->getGeneralController();
        return $publicController->index($request, $machine);
    }


    public function view_input_log(Machine $machine, CurrentMachineInput $current_machine_input)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        $publicController = $this->getGeneralController();
        return $publicController->view_input_log( $machine);
       }

    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, LogController::$info, true);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

    public function getGeneralController()
    {
        $publicController = new GeneralLogController();
        $publicController->route_path = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }
}
