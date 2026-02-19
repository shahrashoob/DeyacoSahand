<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class WarpsDeliveryConfirmationController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.machine.warps_delivery_confirmation.",
        "enable_status" => [ "006", "022", "028" ],
        "button"        => [ "caption" => "تایید تحویل چله از انبار", "class" => "btn-success" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.warps_delivery_confirmation.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = WarpsDeliveryConfirmationController::$info["route"];
        $this->view_path  = WarpsDeliveryConfirmationController::$info["view_path"];
    }

    public function index( Machine $machine ) {

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

        return view( $this->view_path . "index", compact( "warps_request_form", "machine" ) );

    }

    public function submit( Request $request, Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        $allocation         = $machine->getCurrentAllocation();
        $warps_request_form = WarpsRequestForm::where( [
            "status_id"     => 7005004, // در انتظار تایید درخواست کننده
            "allocation_id" => $allocation->id
        ] )->first();
        if ( ! isset( $warps_request_form->items ) ) {
            return back()->withErrors( "اطلاعات چله یافت نشده، لطفا بعداز چند دقیقه دوباره تلاش کنید." );

        }
        foreach ( $warps_request_form->items as $item ) {
            $carrier = "carrier_" . $item->input_line_code;

            if ( ! isset( $request->$carrier ) || $request->$carrier != $item->warehouse_product->carrier->code ?? "" ) {
                return back()->withErrors( "شماره حامل های وارد شده، صحیح نمی باشد." );
            }
        }

        // نوع رویداد ماشین
        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 480; // تایید تحویل چله

        // تحویل چله و عملیات مربوط به آن
        $allocation = $machine->getCurrentAllocation();
        Warps::warpsDeliveryConfirmation( $allocation );


        event( new MachineLogEvent( $machine, $machineLog ) );


        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }


    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, WarpsDeliveryConfirmationController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
