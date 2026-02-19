<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;

class GeneralProductionChannelManagementController extends Controller {
// مدیریت کانال تولید
    var $view_path = "goods_kind_process.general.machine.production_channel_management.";
    var $route_path;
    var $dashboard_route;

    public function __construct() {
    }

    public function index( Machine $machine ) {
        $list = ProductionChannel::where( "machine_id", $machine->id )->
        whereIn( "status_id", [ 3358001, 3358002 ] )-> // کانال تولید جاری / رزور
        get();

        $route_path=$this->route_path;
        $dashboard_route = $this->dashboard_route;
        return view( $this->view_path . "index", compact( "machine", "list","route_path" ,"dashboard_route") );
    }

//    public function store( Request $request, Machine $machine ) {
//
//        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );
//
//    }
//
//    public function update_to_produced(  Machine $machine ) {
//
//        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );
//
//    }

    public function update_to_canceled( Machine $machine ) {

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


}
