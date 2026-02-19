<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralAllocationCardController;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use AWS\CRT\Log;
use Illuminate\Support\Facades\Auth;
use function back;
use function view;

class AllocationCardController extends Controller {
    //
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.allocation_card.",
        "enable_status" => [
            "001",
            "002",
            "003",
            "004",
            "005",
            "006",
            "007",
            "008",
            "009",
            "010",
            "011",
            "012",
            "013",
            "014",
            "015",
            "016",
            "017",
            "018",
            "019",
            "020",
            "021",
            "022",
            "023",
            "024",
            "025",
            "026",
            "027",
            "028",
            "029",
            "030",
            "031",
            "032",
            "033",
            "034",
            "035",
            "036",
            "037",
            "038",
            "039",
            "040",
            "041",
            "042",
            "043",
            "044",
            "045",
            "046",
            "047",
            "048",
            "049",
            "050",
            "051",
            "052","053","054" ],
        "button"        => [ "caption" => " پرینت کارت تخصیص ", "class" => "btn-info" ],
        "view_path"     => "goods_kind_process.fabric_raw.jacquard.machine.allocation_card.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        $this->route_path = AllocationCardController::$info["route"];
        $this->view_path  = AllocationCardController::$info["view_path"];
    }

    public function index(Machine $machine, $allocation_id = null)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }
        $publicController = $this->getGeneralController();
        return $publicController->index($machine, $allocation_id);
    }
    public function print(Machine $machine, Allocation $allocation)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();
        return $publicController->print($machine, $allocation);
    }

    public function download(Machine $machine, Allocation $allocation)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();
        return $publicController->download($machine, $allocation);
    }
    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, AllocationCardController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

    public function getGeneralController()
    {
        $publicController = new GeneralAllocationCardController();
        $publicController->route_path = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }
}
