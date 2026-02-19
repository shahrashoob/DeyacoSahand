<?php

namespace App\Http\Controllers\LineProductStation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsKindPropertyOption;
use App\Models\LineProduct\GoodsKindPropertyValue;
use Illuminate\Http\Request;

class GoodsKindOptionController extends Controller {
    private $view_path = "line_product_station.goods_kind.option.";
    private $route_path = "line_product_station.goods_kind.option.";

    public function index( GoodsKindProperty $goods_kind_property ) {

        if($goods_kind_property->status_id==1210){
            return back()->withErrors("این مشخصه غیرفعال است");
        }
        return view( $this->view_path . "index", compact( "goods_kind_property" ) );
    }

    public function store( Request $request, GoodsKindProperty $goods_kind_property ) {


        foreach ( $request->data["new"] as $key => $value ) {
            if ( isset( $value ) ) {
                if ( GoodsKindPropertyOption::ExistsCode( $goods_kind_property->id, $value ) ) {
                    return back()->withErrors( "عنوان " . $value . "تکراری است" );
                }
                $property                         = new GoodsKindPropertyOption();
                $property->caption                = $value;
                $property->goods_kind_id          = $goods_kind_property->goods_kind_id;
                $property->goods_kind_property_id = $goods_kind_property->id;
                $property->save();
            }
        }

        return back()->with( [ "success" => "موارد جدید با موفقیت اضافه شد" ] );
    }

    public function update( Request $request, GoodsKindProperty $goods_kind_property ) {

        if ( ! isset( $request->data ) ) {
            return back()->withErrors("اطلاعات به درستی ارسال نشده، لطفا مجدد تلاش کنید");
        }
        foreach ( $request->data["option"] as $key => $item ) {
            if ( $item == "" || GoodsKindPropertyOption::ExistsCode( $goods_kind_property->id, $item, $key ) ) {
                return back()->withErrors( "عنوان " . $item . "تکراری است" );
            }
            $property = GoodsKindPropertyOption::find( $key );
            if ( $property ) {
                $property->caption = $item;
                $property->enabled = isset( $request->data["enabled"][ $key ] ) && $request->data["enabled"][ $key ] == "on";
                $property->color =  $request->data["color"][ $key ];
                $property->save();
            }
        }

        return back()->with( [ "success" => "اطلاعات با موفقیت ذخیره شد" ] );

    }


    public function destroy(GoodsKindProperty $goods_kind_property, GoodsKindPropertyOption $goods_kind_property_option) {


        $first_item=  GoodsKindPropertyValue::where("goods_kind_property_id",$goods_kind_property->id)->
         where("value",$goods_kind_property_option->id)->first();
       if(!isset($first_item)){
           $goods_kind_property_option->delete();
           return back()->with(["success"=>"آیتم با موفقیت حذف شد."]);
       }
       else{
           return back()->withErrors("این آیتم در کالای ". $first_item->product->fullCaption()." استفاده شده است. ");
       }
        return back()->withErrors( "امکان حذف گزینه های لیست وجود ندارد." );
    }

    public function edit_number(GoodsKindProperty $goods_kind_property){
        return view( $this->view_path . "edit_number", compact( "goods_kind_property" ) );

    }
    public function update_number(Request $request,GoodsKindProperty $goods_kind_property){
        if($request->min_value > $request->max_value){
            return back()->withErrors("مقداری به درستی وارد نشده است.");
        }
       $goods_kind_property->update($request->all());

        return redirect()->route("line_product_station.goods_kind.property.index",$goods_kind_property->goods_kind_id)
            ->with(["success"=>"اطلاعات با موفقیت ذخیره گردید."]);

    }

}
