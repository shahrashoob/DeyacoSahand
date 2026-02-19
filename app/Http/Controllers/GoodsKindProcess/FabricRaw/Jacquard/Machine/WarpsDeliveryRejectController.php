<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use function back;
use function event;
use function redirect;

class WarpsDeliveryRejectController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.warps_delivery_reject.",
        "enable_status" => [ "045", "022" ],
        "button"        => [ "caption" => "عدم تایید تحویل چله از انبار", "class" => "btn-danger" ],
        "message"       => [ "confirm" => "آیا از عدم تایید تحویل چله اطمینان دارید؟" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        1/0;
        $this->route_path = WarpsDeliveryRejectController::$info["route"];
    }

    public function submit( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();

        $product_request_form = ProductRequestForm::where( [
            "status_id"     => 7005004, // در انتظار تایید درخواست کننده
            "allocation_id" => $allocation->id
        ] )->first();

        if ( ! isset( $product_request_form ) ) {
            return back()->withErrors( "وضعیت فرم تحویل چله از انبار 'در انتظار تایید درخواست کننده' نمی باشد." );
        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 330;
        event( new MachineLogEvent( $machine, $machineLog ) );

        $result = $product_request_form->rejectRequest();
        if ( !$result["result"] ) {
            return back()->withErrors( $result["error"] );
        }

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
