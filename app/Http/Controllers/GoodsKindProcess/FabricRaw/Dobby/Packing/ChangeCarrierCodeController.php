<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Packing;

use App\Events\Form\PackingLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class ChangeCarrierCodeController extends Controller {
    var $view_path = "goods_kind_process.fabric_raw.packing.change_carrier_code.";
    var $route_path = "fabric_raw.packing.change_carrier_code.";
    var $dashboard_path = "fabric_raw.packing.dashboard.";
    public static $info = [
        "route"         => "fabric_raw.packing.change_carrier_code.",
        "enable_status" => [ "001" ],
        "button"        => [ "caption" => "تغییر شماره حامل", "class" => "btn-warning" ],
    ];

    public function index( PackingForm $packing_form ) {

        $result = $this->checkPermission( $packing_form );
        if ( $result != "" ) {
            return $result;
        }
        $carrier_type_option = Option::get( "carrier_type_goods_kind", 0, $packing_form->product->goods_kind_id );

        $route_path = $this->route_path;

        return view( $this->view_path . "index", compact( "packing_form", "route_path", "carrier_type_option" ) );
    }

    public function submit( Request $request, PackingForm $packing_form ) {

        $result = $this->checkPermission( $packing_form );
        if ( $result != "" ) {
            return $result;
        }

        $carrier_code = $request->carrier_id;

        $valid_carrier_type = $packing_form->product->goods_kind->
        carrier_type()->
        where( "carrier_type_id", $request->carrier_type_id )->
        exists();
        if ( ! $valid_carrier_type ) {
            return back()->withErrors( "نوع حامل برای رسته کالایی نامعتبر است." );
        }
        $result = Carrier::firstOrCreate( $carrier_code, $request->carrier_type_id, 5320001, null );
        if ( isset( $result["carrier"] ) && in_array( $result["carrier"]->status_id, [ 5320001] ) ) {
            $carrier = $result["carrier"];
        } elseif ( ! $result["result"] ) {
            return redirect()->back()->withErrors( $result["message"] );
        }

        if($carrier->status_id != 5320001){
            return back()->withErrors("حامل انتخاب شده خالی نمی باشد.");
        }

        $packing_form->carrier->SetEmpty( ); // خالی

        $packing_form->carrier_id = $carrier->id;
        $packing_form->save();
        event( new PackingLogEvent( $packing_form, 7007002,null,$carrier->getCaption() ) );

        // تغییر وضعیت حامل به پر در حال پر شدن
        $carrier->SetStatus( 5320006 );


        return redirect()->route( $this->dashboard_path . "view", $packing_form )->
        with( [ "success" => "عملیات با موفقیت انجام شد." ] );


    }

    public function checkPermission( PackingForm $packing_form ) {
        $result = DashboardController::checkPermissionConditions( $packing_form, ChangeCarrierCodeController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

    }
}
