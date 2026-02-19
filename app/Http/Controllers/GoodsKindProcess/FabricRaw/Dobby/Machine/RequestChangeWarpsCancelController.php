<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class RequestChangeWarpsCancelController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.machine.request_change_warps_cancel.",
        "enable_status" => [ "021", "022" ],
        "button"        => [ "caption" => "کنسل کردن درخواست تعویض چله", "class" => "btn-danger" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.request_change_warps_cancel.",
        "message"       => [ "confirm" => "آیا از کنسل کردن درخواست تعویض چله اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = RequestChangeWarpsCancelController::$info["route"];
        $this->view_path  = RequestChangeWarpsCancelController::$info["view_path"];
    }

    public function submit( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();

        $warps_form = WarpsRequestForm::where( [
            "machine_id"    => $machine->id,
            "allocation_id" => $allocation->id
        ] )->
        orderByDesc( "id" )->
        first();

        if (  ! in_array( $warps_form->status_id, [ 7005001, 7005003 ] ) ) {
            // در انتظار تحویل چله و در انتظار تکمیل موجودی
            return back()->withErrors( "با توجه به اینکه چله به تولید تحویل شده امکان کنسل کردن درخواست وجود ندارد." );
        }

        // کنسل کردن درخواست
        $warps_form->cancelRequest();

        $machine->setStatus(
            null,
            53001,
            7003016,// در حال بافت
            null,
            "Fabric_Raw"
        );
        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 510; // کنسل شدن درخواست تعویض چله

        event( new MachineLogEvent( $machine, $machineLog ) );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, RequestChangeWarpsCancelController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
