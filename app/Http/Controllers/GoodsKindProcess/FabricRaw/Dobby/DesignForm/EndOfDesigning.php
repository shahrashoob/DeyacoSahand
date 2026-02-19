<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\DesignForm;

use App\Events\Fabric_Raw\FabricRawDesignFormLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\Fabric_Raw\Machine;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignForm;
use App\Models\LineProduct\Machine\MachineLog;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;


class EndOfDesigning extends Controller {
    public static $info = [
        "route"         => "fabric_raw.design_form.end_of_designing.",
        "enable_status" => [ "002", "005" ],
        "button"        => [ "caption" => "پایان طراحی", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.design_form.end_of_designing.",
        "message"       => [ "confirm" => "آیا از پایان طراحی اطمینان دارید؟" ],
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.design_form.dashboard.";

    public function __construct() {
        $this->route_path = EndOfDesigning::$info["route"];
        $this->view_path  = EndOfDesigning::$info["view_path"];
    }

    public function submit( Request $request, FabricRawDesignForm $design_form ) {

        $result = $this->checkPermission( $design_form );
        if ( $result != "" ) {
            return $result;
        };

        $design_form->status_id = DashboardController::$perfix_design_form_status_code . "004";
        $design_form->save();
        event( new FabricRawDesignFormLogEvent( $design_form ) );

        if ( $design_form->machine->production_status_id == \App\Http\Controllers\GoodsKindProcess\Fabric_Raw\Dobby\Machine\DashboardController::$perfix_production_status_code . "020" ) {
            $machineLog                        = new MachineLog();
            $machineLog->machine_event_type_id = 240;

            $design_form->machine->on_status_id          = 53002;
            $design_form->machine->machine_off_reason_id = 1602;
            $design_form->machine->production_status_id  = \App\Http\Controllers\GoodsKindProcess\Fabric_Raw\Dobby\Machine\DashboardController::$perfix_production_status_code . "004";
            $design_form->machine->save();


            event( new MachineLogEvent( $design_form->machine, $machineLog ) );
        }

        return redirect()->route( $this->dashboard_route . "index"  )->with(["success"=>"عملیات با موفقیت انجام شد."]);

    }

    public function checkPermission( FabricRawDesignForm $design_form ) {


        $result = DashboardController::checkPermissionConditions( $design_form, EndOfDesigning::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

}
