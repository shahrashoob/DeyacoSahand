<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class RequestChangeWarpsController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.machine.request_change_warps.",
        "enable_status" => [ "016" ],
        "button"        => [ "caption" => "درخواست تعویض چله", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.request_change_warps.",
        "message"       => [ "confirm" => "آیا از درخواست تعویض چله اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = RequestChangeWarpsController::$info["route"];
        $this->view_path  = RequestChangeWarpsController::$info["view_path"];
    }

    public function submit( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();

        if ( ! $allocation ) {
            return back()->withErrors( "تخصیص جاری برای ماشین وجود ندارد، لطفا دکمه پایان بافت کارت تولید را بزنید." );
        }
        // پیدا کردن کد چله
       // $warps_is_in_warehouse = Warps::warpsExistInWarehouse( $allocation );

        if ( $warps_is_in_warehouse ) {
            // چله در انبار هست
            $machine->setStatus(
                null,
                53002,
                DashboardController::$perfix_production_status_code . "022",
                1617 );

        } else {
            // چله در انبار نیست
            $machine->setStatus(
                null,
                53002,
                DashboardController::$perfix_production_status_code . "021",
                1616 );

        }

        // ران شدن ماژول چله
        WarpsRequestForm::newRequest( $allocation, $machine, $warps_is_in_warehouse );

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 270;

        event( new MachineLogEvent( $machine, $machineLog ) );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, RequestChangeWarpsController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
