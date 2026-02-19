<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class PreparationForPiningController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.machine.preparation_for_pinning.",
        "enable_status" => [ "039" ],
        "button"        => [ "caption" => "آماده سازی لامل ریزی", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.preparation_for_pinning.",
        "message"       => [ "confirm" => "آیا از آماده شدن ماشین جهت لامل ریزی اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = PreparationForPiningController::$info["route"];
        $this->view_path  = PreparationForPiningController::$info["view_path"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 180;

        $machine->setStatus(
            null,
            53002,
            7003014,
            1609,
            "Fabric_Raw"
        );

        event( new MachineLogEvent( $machine, $machineLog ) );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, PreparationForPiningController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
