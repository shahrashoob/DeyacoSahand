<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;
use function view;

class WarpsDeliveryToWarehouseController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.machine.warps_delivery_to_warehouse.",
        "view_path"     => "goods_kind_process.fabric_raw.machine.warps_delivery_to_warehouse.",
        "enable_status" => [  ],
        "button"        => [ "caption" => "تحویل چله به انبار", "class" => "btn-danger" ],
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";
    public function __construct() {
        $this->route_path = WarpsDeliveryToWarehouseController::$info["route"];
        $this->view_path = WarpsDeliveryToWarehouseController::$info["view_path"];
    }
    public function index(Machine $machine , Form $form){

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        return view($this->view_path."index",compact("machine","form"));
    }
    public function submit( Machine $machine,Form $form ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        if ( $form->status_id != 500000400  ) {// در انتظار تحویل به انبار
            return back()->withErrors( "وضعیت فرم تحویل چله به انبار 'در انتظار تحویل به انبار' نمی باشد." );
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id =500;
        event( new MachineLogEvent( $machine, $machineLog, $form->item[0]->carrier->getCaption() ) );

        // تغییر وضعیت فرم درخواست کالا
        $form->status_id = 500000410; //  در انتظار تایید انبار
        $form->save();
        event( new FormLogEvent($form) );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, WarpsDeliveryToWarehouseController::$info,true );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
