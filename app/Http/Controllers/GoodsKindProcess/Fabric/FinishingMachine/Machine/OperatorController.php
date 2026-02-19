<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine;


use App\Http\Controllers\Controller;

use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralOperatorController;
use App\Models\LineProduct\Machine\Machine;
use Illuminate\Http\Request;

class OperatorController extends Controller {
    public static $info = [
        "route"         => "fabric.finishing_machine.machine.operator.",
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
            "901",
            "902",
            "903",
            "904",
            "905",
        ],
        "button"        => [ "caption" => "ثبت اپراتور ماشین", "class" => "btn-primary" ],


    ];
    var $route_path;
    var $dashboard_route = "fabric.finishing_machine.machine.dashboard.";

    public function __construct() {
        $this->route_path=self::$info["route"];
    }

    public function index( Machine $machine ) {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->index($machine);
    }

    public function submit( Request $request, Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->submit($request,$machine);
    }


    public function getGeneralController()
    {
        $publicController = new GeneralOperatorController();
        $publicController->route_path = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }
    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, self::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
