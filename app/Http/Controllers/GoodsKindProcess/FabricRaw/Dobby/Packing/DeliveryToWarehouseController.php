<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Packing;

use App\Events\Form\PackingLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function back;
use function event;
use function redirect;
use function view;

class DeliveryToWarehouseController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.packing.delivery_to_warehouse.",
        "enable_status" => [ "001" ],
        "button"        => [ "caption" => "تکمیل و تحویل به انبار", "class" => "btn-primary" ],
        "message"       => [ "confirm" => "آیا از تایید و تحویل به انبار اطمینان دارید؟" ],

    ];
    var $view_path = "goods_kind_process.fabric_raw.packing.delivery_to_warehouse.";
    var $route_path;
    var $dashboard_route = "fabric_raw.packing.dashboard.";


    public function __construct() {
        $this->route_path = DeliveryToWarehouseController::$info["route"];
    }

    public function get_nosa_code( PackingForm $packing_form ) {
        return view( $this->view_path . "get_nosa_code", compact( "packing_form" ) );
    }

    public function submit_nosa_code( Request $request, PackingForm $packing_form ) {
        if ( $request->nosa_code != "" ) {
            $packing_form->lot_number->nosa_code = $request->nosa_code;
            $packing_form->lot_number->save();

            return redirect()->route( $this->dashboard_route . "view", $packing_form )->with( [ "success" => "کد نرم افزار مالی ثبت گردید." ] );
        }

        return redirect()->back()->withErrors( "کد نرم افزار مالی معتبر نمی باشد" );
    }

    public function submit( PackingForm $packing_form ) {

        $result = $this->checkPermission( $packing_form );
        if ( $result != "" ) {
            return $result;
        }


      return  $lot_nomber_is_allowed_setting = Setting::find( 5 )->integer_value;
        if ( $packing_form->lot_number->nosa_code == null && ! $lot_nomber_is_allowed_setting ) {
            return redirect()->route( $this->route_path . "get_nosa_code", $packing_form );
        }
        $form = Form::CreateFrom( [
            "order_id"           => 0,
            "order_list_id"      => 0,
            "production_card_id" => 0,
            "user_id"            => Auth::user()->id,
            "form_type_id"       => 304,
            "trans_kind"         => 2,
            "warehouse_id"       => $packing_form->degree->warehouse->id,
            "status_id"          => 500000410, // در انتظار تایید انبار
        ] );
        $form->getCode( "FRE" );
        FormItem::create( [
            "form_id"         => $form->id,
            "product_id"      => $packing_form->product_id,
            "amount"          => $packing_form->getFinalAmount(),
            "sub_amount"      => $packing_form->getSubAmount(),
            "packing_type_id" => $packing_form->packing_type_id,
            "carrier_id"      => $packing_form->carrier_id,
            "degree_id"       => $packing_form->degree_id,
            "lot_number_id"   => $packing_form->lot_number_id
        ] );
        event( new FormLogEvent( $form ) );

        $packing_form->status_id = 7007002; //  در انتظار تایید انبار
        $packing_form->form_id   = $form->id;
        $packing_form->save();
        event(new PackingLogEvent($packing_form,7007005));

        // تغییر وضعیت حامل
        $packing_form->carrier->SetStatus( 5320007);

        return redirect()->route( $this->dashboard_route . "index" );
    }


    public function checkPermission( PackingForm $packing_form ) {

        $result = DashboardController::checkPermissionConditions( $packing_form, DeliveryToWarehouseController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
