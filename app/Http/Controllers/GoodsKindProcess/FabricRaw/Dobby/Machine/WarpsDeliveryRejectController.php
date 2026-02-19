<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use function back;
use function event;
use function redirect;

class WarpsDeliveryRejectController extends Controller
{
    public static $info = [
        "route"         => "fabric_raw.machine.warps_delivery_reject.",
        "enable_status" => [ "006", "022" ],
        "button"        => [ "caption" => "عدم تایید تحویل چله از انبار", "class" => "btn-danger" ],
        "message"       => [ "confirm" => "آیا از عدم تایید تحویل چله اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";
    public function __construct() {
        $this->route_path = WarpsDeliveryRejectController::$info["route"];
    }

    public function submit( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();

        $warps_request_form = WarpsRequestForm::where( [
            "status_id"     => 7005004, // در انتظار تایید درخواست کننده
            "allocation_id" => $allocation->id
        ] )->first();

        if ( ! isset( $warps_request_form ) ) {
            return back()->withErrors( "وضعیت فرم تحویل چله از انبار 'در انتظار تایید درخواست کننده' نمی باشد." );
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id =330;
        event( new MachineLogEvent( $machine, $machineLog ) );

        // تغییر وضعیت فرم درخواست کالا
        $warps_request_form->status_id = WarpsRequestForm::$perfix_status_code . "005";
        $warps_request_form->save();
        event( new WarpsRequestFormLogEvent( $warps_request_form ) );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, WarpsDeliveryRejectController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

}
