<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCardController;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineInputLog;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Production\Production;
use Illuminate\Http\Request;

class FinishedAllocationController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.production_card.finished_allocation.",
        "enable_status" => [ "001", "002", "003", "004" ],
        "button"        => [ "caption" => "مشاهده سابقه تخصیص", "class" => "btn-info" ],
        "view_path"     => "goods_kind_process.fabric_raw.production_card.finished_allocation.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.production_card.dashboard.";

    public function __construct() {
        $this->route_path = FinishedAllocationController::$info["route"];
        $this->view_path  = FinishedAllocationController::$info["view_path"];
    }

    public function index( Production $production ) {

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $list = MachineAllocation::
        where( "production_id", $production->id )->
        groupBy( "allocation_id" )->
        orderByDesc( "id" )->paginate();
           $allow_show_log = \Auth::user()->posts->first()->checkButtonPermission( "production.dashboard.list.show_allocation_data" );

        return view( $this->view_path . "index", compact( "production", "list", "list","allow_show_log" ) );

    }

    public function allocation_input( Production $production, Allocation $allocation ) {

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $current_input_list = CurrentMachineInput::
        where( "allocation_id", $allocation->id )->get();


        return view( $this->view_path . "allocation_input", compact( "production", "allocation", "current_input_list" ) );

    }

    public function allocation_data( Production $production, Allocation $allocation ) {

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        $allow_show_log = \Auth::user()->posts->first()->checkButtonPermission( "production.dashboard.list.show_allocation_data" );

        if ( ! $allow_show_log ) {
            return back()->withErrors( "شما به صفحه مورد نظر دسترسی ندارید." );
        }

        $current_input_list = Allocation\AllocationData::
        where( "allocation_id", $allocation->id )->get();


        return view( $this->view_path . "allocation_data", compact( "production", "allocation", "current_input_list" ) );

    }

    public function checkPermission( Production $production ) {

        $result = ProductionCardController::checkPermissionConditions( $production, FinishedAllocationController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
