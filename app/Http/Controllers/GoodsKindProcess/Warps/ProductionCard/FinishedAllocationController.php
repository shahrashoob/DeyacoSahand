<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\ProductionCard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Warps\ProductionCardController;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Production\Production;
use Illuminate\Http\Request;

class FinishedAllocationController extends Controller
{
    public static $info = [
        "route"         => "warps.production_card.finished_allocation.",
        "enable_status" => ["001","002","003","004"],
        "button"        => [ "caption" => "مشاهده سابقه تخصیص", "class" => "btn-info" ],
        "view_path"     => "goods_kind_process.warps.production_card.finished_allocation.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "warps.production_card.dashboard.";
    public function __construct() {
        $this->route_path = FinishedAllocationController::$info["route"];
        $this->view_path = FinishedAllocationController::$info["view_path"];
    }

    public function index( Production $production ) {

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $list      = MachineAllocation::
        where( "production_id", $production->id )->
            groupBy("allocation_id")->
        orderByDesc( "id" )->paginate();


        return view( $this->view_path . "index", compact( "production", "list" ,"list") );

    }

    public function checkPermission( Production $production ) {

        $result = ProductionCardController::checkPermissionConditions( $production, FinishedAllocationController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
