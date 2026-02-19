<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralFinishAllocationController;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use Illuminate\Http\Request;

class FinishedAllocationController extends Controller
{
    public static $info = [
        "route"         => "fabric.finishing_machine.machine.finished_allocation.",
        "enable_status" => ["001","002","003","901","902","903","904","905" ],
        "button"        => [ "caption" => "مشاهده سابقه تخصیص", "class" => "btn-info" ],
        "view_path"     => "goods_kind_process.fabric.finishing_machine.machine.finished_allocation.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric.finishing_machine.machine.dashboard.";
    public function __construct() {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
    }


    public function index( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();
        return $publicController->index($machine,"fabric.production_card.");
    }

    public function material_consumed_list( Machine $machine, Allocation $allocation ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();
        return $publicController->material_consumed_list($machine,$allocation);
    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, self::$info,true );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
    public function getGeneralController()
    {
        $publicController = new GeneralFinishAllocationController();
        $publicController->route_path = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }
}
