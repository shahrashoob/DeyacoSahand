<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer\Machine;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralAllocationCardController;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use Illuminate\Http\Request;


class AllocationCardController extends Controller {
    //
    public static $info = [
        "route"         => "warps.karl_mayer.machine.allocation_card.",
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
            "010", ],
        "button"        => [ "caption" => " پرینت کارت تخصیص ", "class" => "btn-info" ],
        "view_path"     => "goods_kind_process.warps.karl_mayer.machine.allocation_card.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.karl_mayer.machine.dashboard.";

    public function __construct() {
        $this->route_path = self::$info["route"];
        $this->view_path  = self::$info["view_path"];
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

        $result = DashboardController::checkPermissionConditions( $machine, self::$info );
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
