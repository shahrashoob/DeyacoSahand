<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer\Machine;


use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralRequestRawMaterialController;
use App\Models\LineProduct\Machine\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RequestRawMaterialController extends Controller {
    public static $info = [
        "route"         => "warps.karl_mayer.machine.request_raw_material.",
        "enable_status" => [ "002", "003", "004", "005", "006", "007", "008", "009", "010", "011", "012" ],
        "button"        => [ "caption" => "درخواست مواد اولیه", "class" => "btn-primary" ],
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.karl_mayer.machine.dashboard.";

    public function __construct() {
        $this->route_path = self::$info["route"];
    }

    public function index( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->index(  $machine );

    }

    public function submit( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->submit(   $request,  $machine );

    }

    public function select_material(  Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();

        return $publicController->select_material(     $machine );

    }

    public function submit_select_material( Request $request, Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $publicController = $this->getGeneralController();

        $result= $publicController->submit_select_material( $request,  $machine );
        if(!$result["result"]){
            return back()->withErrors($result["error"]);
        }

        return redirect()->route( $this->dashboard_route . "view", $machine )->with( [ "success" => "درخواست مواد اولیه با موفقیت ثبت گردید " ] );


    }
    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, self::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

    public function getGeneralController() {
        $publicController                  = new GeneralRequestRawMaterialController();
        $publicController->route_path      = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }
}

