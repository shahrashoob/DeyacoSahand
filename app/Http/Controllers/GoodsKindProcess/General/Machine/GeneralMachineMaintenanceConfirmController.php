<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind\GoodsKindProductFault;
use App\Models\LineProduct\Machine\Fault\CurrentMachineFault;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineType\MachineTypeMachineFaultProductFault;
use App\Models\LineProduct\Machine\MachineTypeMachineFault;
use App\Models\LineProduct\Machine\Maintenance\Maintenance;
use App\Models\LineProduct\Product\Fault\ProductFaultProductFaultSign;
use http\Exception\BadConversionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralMachineMaintenanceConfirmController extends Controller {
    var $view_path = "goods_kind_process.general.machine.machine_maintenance_confirm.";
    var $route_path;
    var $dashboard_route;

    public function index( Machine $machine ) {

        $list = Maintenance::where( [
            "machine_id" => $machine->id,
        ] )->
        whereNotIn( "status_id", [ 6003003, 6003006 ] )-> // خاتمه یافته و عدم تایید
        get();


        $route_path      = $this->route_path;
        $dashboard_route = $this->dashboard_route;

        return view( $this->view_path . "index", compact( "machine", "route_path", "dashboard_route", "list" ) );
    }

    public function confirm( Request $request, Machine $machine, Maintenance $maintenance ) {

        if ( $maintenance->status_id != 6003005 ) {
            return back()->withErrors( "عملیات نت در انتظار تایید درخواست کننده نمی باشد." );
        }

        $result = Maintenance::UpdateMaintenance( $maintenance, "confirm_done" );
        if ( $result["result"] ) {

            return redirect()->route( $this->route_path . "index", $machine )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );


        } else {
            return back()->withErrors( $result["error"] );
        }

    }

    public function reject( Request $request, Machine $machine, Maintenance $maintenance ) {

        if ( $maintenance->status_id != 6003005 ) {
            return back()->withErrors( "عملیات نت در انتظار تایید درخواست کننده نمی باشد." );
        }

        $result = Maintenance::UpdateMaintenance( $maintenance, "reject_done" );
        if ( $result["result"] ) {

            return redirect()->route( $this->route_path . "index", $machine )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );


        } else {
            return back()->withErrors( $result["error"] );
        }

    }
}
