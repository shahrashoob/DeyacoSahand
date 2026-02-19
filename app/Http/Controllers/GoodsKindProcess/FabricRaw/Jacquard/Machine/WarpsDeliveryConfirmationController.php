<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class WarpsDeliveryConfirmationController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.jacquard.machine.warps_delivery_confirmation.",
        "enable_status" => [  "045","022" ],
        "button"        => [ "caption" => "تایید تحویل چله از انبار", "class" => "btn-success" ],
        "view_path"     => "goods_kind_process.fabric_raw.jacquard.machine.warps_delivery_confirmation.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct() {
        1/0;
        $this->route_path = WarpsDeliveryConfirmationController::$info["route"];
        $this->view_path  = WarpsDeliveryConfirmationController::$info["view_path"];
    }

    public function index( Machine $machine ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $allocation = Allocation::where( "machine_id", $machine->id )->whereIn( "status_id", [5310010,5310040] )->orderBy( "id" )->first();


        $product_request_form = ProductRequestForm::where( [
            "status_id"     => 7005004, // در انتظار تایید درخواست کننده
            "allocation_id" => $allocation->id
        ] )->first();

        if(!isset($product_request_form->forms[0])){
            return back()->withErrors("فرم انبار تحویل چله یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
        }
        $form=$product_request_form->forms[0];

        if ( ! isset( $product_request_form ) ) {
            return back()->withErrors( "وضعیت فرم تحویل چله از انبار 'در انتظار تایید درخواست کننده' نمی باشد." );
        }

        return view( $this->view_path . "index", compact( "form","product_request_form", "machine" ) );

    }

    public function submit( Request $request, Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        $allocation = Allocation::where( "machine_id", $machine->id )->whereIn( "status_id", [5310010,5310040] )->orderBy( "id" )->first();

        $product_request_form = ProductRequestForm::where( [
            "status_id"     => 7005004, // در انتظار تایید درخواست کننده
            "allocation_id" => $allocation->id
        ] )->first();
        if ( ! isset( $product_request_form->items ) ) {
            return back()->withErrors( "وضعیت فرم تحویل چله از انبار 'در انتظار تایید درخواست کننده' نمی باشد." );

        }

        foreach ( $product_request_form->forms[0]->form->item as $form_item ) {
            $carrier = "carrier_" . $form_item->id;

            if ( ! isset( $request->$carrier ) || $request->$carrier != ($form_item->carrier->code ?? "") ) {
                return back()->withErrors( "شماره حامل های وارد شده، صحیح نمی باشد." );
            }
        }

        // نوع رویداد ماشین
        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 480; // تایید تحویل چله


       //  تحویل چله و عملیات مربوط به آن

        // در صورت مجاز بودن فرم تایید و تراکنش انبار ثبت شود.
        $product_request_form->checkIfValidConfirmRequest( );

        // در این تابع وضعیت جدید فرم ثبت می شود.
        $product_request_form->updateExistFormStatusForm($product_request_form->forms[0]->form);

        /// بروز رسانی لات چله
      //  Warps::updateCarrierInCurrentInputOutputBand($machine,"setCurrentCarrier",$allocation);

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
