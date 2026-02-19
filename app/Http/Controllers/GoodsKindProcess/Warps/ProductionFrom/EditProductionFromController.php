<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\ProductionFrom;

use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LotNumber;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class EditProductionFromController extends Controller {
    var $view_path = "goods_kind_process.warps.production_form.edit_production_form.";
    var $route_path = "warps.production_form.edit_production_form.";
    var $dashboard_route = "warps.production_form.dashboard.";
    public static $info = [
        "route"         => "warps.production_form.edit_production_form.",
        "enable_status" => [ 500000420, 500000100 ,500000400],
        "button"        => [ "caption" => " ویرایش فرم تولید چله ", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.warps.production_form.edit_production_form.",
    ];

    public function index( Form $form ) {
        $result = $this->checkPermission( $form );
        if ( $result != "" ) {
            return $result;
        }
        $form_item     = $form->item()->first();
        $product       = $form_item->product;
        $degree_option = Option::get( "degree", $form_item->degree->id ?? 0, $product->goods_kind->id );

        $product_option = Option::get( "product_by_goods_kind", $product->id, 3 );

        return view( $this->view_path . "index", compact( "form","product_option", "product", "form_item", "degree_option" ) );
    }
    public function submit(Request $request, Form $form ) {
        $result = $this->checkPermission( $form );
        if ( $result != "" ) {
            return $result;
        }
        $form_item = $form->item()->first();
        $product   = $form_item->product;

        $degree=Degree::find($request->degree_id);
        $result = $product->getWarehouseForForm($degree);
        if ( ! $result["result"] ) {
            return redirect()->route($this->route_path."index")->withErrors( $result["error"] );
        }
        $warehouse_id = $result["warehouse_id"];



        // LotNumber
        if ( $request->new_lot_number && ! LotNumber::ExistsCode( $request->new_lot_number, $product->id ) ) {
            LotNumber::insert( [ "code" => $request->new_lot_number, "product_id" => $product->id ] );
        }
        $lot_number = null;
        if ( LotNumber::ExistsCode( $request->lot_number, $product->id ) ) {

            $lot_number = LotNumber::where( [ "product_id" => $product->id, "code" => $request->lot_number ] )->first();
        }
        else {

            $degree_option = Option::get( "degree", $request->degree_id, $product->goods_kind->id );
            return view( $this->view_path . "index", compact( "form", "product", "form_item", "degree_option","request" ) );

        }

        if($form_item->carrier->code != $request->carrier_code) {
            $result = Carrier::firstOrCreate( $request->carrier_code, 1, 5320001, $product->id );
            if ( ! $result["result"] ) {
                return back()->withErrors( $result["message"] );
            }
            $carrier = $result["carrier"];

            if ( $carrier->status_id != 5320001 ) {
                return back()->withErrors( "شماره غلطک وارد شده خالی نیست، لطفا یک شماره غلطک خالی وارد کنید." );
            }
            $carrier->status_id = 5320004; //  پر شده در انتظار تحویل به انبار
            $carrier->save();

            // حذف محصول از غلطک قبلی
            $form_item->carrier->SetEmpty();

            // به روز رسانی حامل
            $form_item->carrier_id=$carrier->id;
            $form_item->save();
        }

        $form_item->update( [
            "amount"        => $request->amount,
            "sub_amount"    => $request->sub_amount,
            "degree_id"     => $request->degree_id,
            "lot_number_id" => $lot_number->id
        ] );

        if($form->status_id == 500000420){

            $form->status_id = 500000400;
            $form->save();
        }

        return redirect()->route( $this->dashboard_route . "show_form", $form )->with(["success"=>"فرم چله با موفقیت ذخیره شد"]);

    }
    public function checkPermission( Form $form ) {
        $result = DashboardController::checkPermissionConditions( $form, EditProductionFromController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
