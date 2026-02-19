<?php

namespace App\Http\Controllers\GoodsKindProcess\Yarn\ProductionFrom;

use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImplementationPeriodFormController extends Controller {
    var $view_path = "goods_kind_process.yarn.production_form.implementation_period.";
    var $route_path = "yarn.production_form.implementation_period_form.";

    // فرم تولید نخ (ریسندگی) ( ویژه دوره پیاده سازی
    public function index() {
        $goods_kind     = GoodsKind::getByCaptionEn( "Yarn" );
        $product_option = Option::get( "product_by_goods_kind", 0, $goods_kind->id );

        return view( $this->view_path . "index", compact( "product_option" ) );
    }

    public function submit( Request $request ) {
        $product = Product::find( $request->product_id );
        $result  = $product->getWarehouseForForm();
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["error"] );
        }

        return redirect()->route( $this->route_path . "complete_form", $product );

    }

    public function complete_form( Product $product ) {

        $property       = GoodsKindProperty::where( "goods_kind_id", $product->goods_kind->id )->get();
        $property_value = GoodsKindPropertyValue::where( "product_id", $product->id )->pluck( "value", "goods_kind_property_id" );

        $degree_option = Option::get( "product_degree", 0, $product->id );

        return view( $this->view_path . "complete_form", compact( "product", "property", "property_value", "degree_option" ) );

    }

    public function submit_complete_form( Request $request, Product $product ) {

        $result = $product->getWarehouseForForm();
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["error"] );
        }
        $warehouse_id = $result["warehouse_id"];

        $result = Carrier::firstOrCreate(  $request->carrier_code,  3 ,5320001,$product->id ); // غلطک چله
        if(!$result["result"]){
            return redirect()->back()->withErrors($result["message"]);
        }
        $carrier=$result["carrier"];



        if ( $request->new_lot_number && ! LotNumber::ExistsCode( $request->new_lot_number, $product->id ) ) {
            LotNumber::insert( [ "code" => $request->new_lot_number, "product_id" => $product->id ] );
        }

        // LotNumber
        $lot_number = null;
        if ( LotNumber::ExistsCode( $request->lot_number, $product->id ) ) {
            $lot_number = LotNumber::where( [ "product_id" => $product->id, "code" => $request->lot_number ] )->first();
        } else {
            $property       = GoodsKindProperty::where( "goods_kind_id", $product->goods_kind->id )->get();
            $property_value = GoodsKindPropertyValue::where( "product_id", $product->id )->pluck( "value", "goods_kind_property_id" );

            $degree_option = Option::get( "product_degree", $request->degree_id, $product->id );

            return view( $this->view_path . "complete_form", compact( "product", "property", "property_value", "degree_option", "request" ) );

        }


        $form = Form::CreateFrom(
            [
                "user_id"      => Auth::user()->id,
                "form_type_id" => 302,
                "trans_kind"   => 2,
                "warehouse_id" => $warehouse_id
            ]
        );

        $form_item = FormItem::create( [
            "form_id"       => $form->id,
            "product_id"    => $product->id,
            "amount"        => $request->amount,
            "sub_amount"    => $request->sub_amount,
            "carrier_id"    => $carrier->id,
            "degree_id"     => $request->degree_id,
            "lot_number_id" => $lot_number->id
        ] );


        return redirect()->route( $this->route_path . "show_form", $form );
    }

    public function show_form( Form $form ) {
        if ( $form->status_id != 500000100 ) {
            return back()->withErrors( "این فرم قبلا ثبت شده است." );
        }
        $form_item = $form->item()->first();
        $product   = $form_item->product;

        return view( $this->view_path . "show_form", compact( "form", "product", "form_item" ) );
    }

    public function submit_form( Request $request, Form $form ) {
        if ( $form->status_id != 500000100 ) {
            return back()->withErrors( "این فرم قبلا ثبت شده است." );
        }
        $form->status_id = 500000400;
        $form->save();
        event( new FormLogEvent( $form ) );

        return redirect()->route( "warps.dashboard.index" ) -> with( [ "success" => "فرم با موفقیت ثبت گردید" ] );
    }
}

