<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm;

use App\Events\Form\PackingLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;

class SendToEmployerController extends Controller  {
    public static $info = [
        "route"         => "fabric_raw.packing_form.send_to_employer.",
        "enable_status" => [ "008" ],
        "button"        => [ "caption" => "تکمیل و ارسال محصول به کارفرما", "class" => "btn-primary" ],
        "message"       => [ "confirm" => "آیا از تایید و ارسال به کارفرما اطمینان دارید؟" ],

    ];
    var $view_path = "goods_kind_process.fabric_raw.packing_form.send_to_employer.";
    var $route_path;
    var $dashboard_route = "fabric_raw.packing_form.";


    public function __construct() {
        $this->route_path = SendToEmployerController::$info["route"];
    }

    public function get_nosa_code( PackingForm $packing_form ) {

        $result = $this->checkPermission( $packing_form );
        if ( $result != "" ) {
            return $result;
        }
        return view( $this->view_path . "get_nosa_code", compact( "packing_form" ) );
    }

    public function submit_nosa_code( Request $request, PackingForm $packing_form ) {

        $result = $this->checkPermission( $packing_form );
        if ( $result != "" ) {
            return $result;
        }
        foreach ( $packing_form->items as $item ) {
            if ( $item->lot_number->nosa_code == null ) {
                $nosa_code = "nosa_code_" . $item->id;
                if ( ! $request->$nosa_code != "" ) {
                    return back()->withErrors( "لطفا کد نوسا برای همه کالا ها را وارد نمایید." );
                } else {
                    $item->lot_number->nosa_code = $request->$nosa_code;
                    $item->lot_number->save();
                }
            }
        }

        return redirect()->route( $this->dashboard_route . "view", $packing_form )->with( [ "success" => "کد نرم افزار مالی ثبت گردید." ] );

    }

    public function submit( PackingForm $packing_form ) {

        $result = $this->checkPermission( $packing_form );
        if ( $result != "" ) {
            return $result;
        }

        $lot_nomber_is_allowed_setting = Setting::find( 5 )->integer_value;
        foreach ( $packing_form->items as $item ) {
            if ( $item->lot_number->nosa_code == null && ! $lot_nomber_is_allowed_setting ) {
                return redirect()->route( $this->route_path . "get_nosa_code", $packing_form );
            }
        }

        if(!$packing_form->degree->warehouse){
            return back()->withErrors("انبار مرتبط با درجه کالا یافت نشد، لطفا با پشتیبانی تماس بگیرد.");
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
        foreach ( $packing_form->items as $item ) {
            FormItem::create( [
                "form_id"         => $form->id,
                "packing_form_item_id"=>$item->id,
                "product_id"      => $item->product_id,
                "amount"          => $item->amount,
                "sub_amount"      => $item->sub_amount,
                "carrier_id"      => $packing_form->carrier_id,
                "degree_id"       => $item->degree_id,
                "lot_number_id"   => $item->lot_number_id
            ] );
            $item->status_id = 7007009; //  در انتظار تایید دریافت محصول
            $item->save();
        }

        event( new FormLogEvent( $form ) );

        $packing_form->status_id = 7007009; //  در انتظار تایید دریافت محصول
        $packing_form->form_id   = $form->id;
        $packing_form->save();
        event( new PackingLogEvent( $packing_form, 7007005 ) );

        // تغییر وضعیت حامل
        if ( $packing_form->carrier ) {
            $packing_form->carrier->SetStatus( 5320007 );
        }

        return redirect()->route( $this->dashboard_route . "index" );
    }


    public function checkPermission( PackingForm $packing_form ) {

        $result = FabricRaw\PackingFormController::checkPermissionConditions( $packing_form, SendToEmployerController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
