<?php

namespace App\Http\Controllers\Utility\File\Product;

use App\Http\Controllers\Controller;
use App\Models\File\File;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Product;
use Illuminate\Http\Request;

class ShowController extends Controller {
    //
    public function show_property( Product $product, GoodsKindProperty $goods_kind_property,$back_to_edit=null ) {
        if ( $goods_kind_property->field_type_id != 4 ) {
            return back()->withErrors( "نوع مشخصه از نوع تصویر نمی باشد." );
        }

        $goods_kind_property_value = GoodsKindPropertyValue::where( [
            "goods_kind_property_id" => $goods_kind_property->id,
            "product_id"             => $product->id
        ] )->first();
        $file=File::find($goods_kind_property_value->value);
        if ( ! $goods_kind_property_value || !$file ) {
            return back()->withErrors( "فایل معتبر نمی باشد." );
        }

        return view("utility.file.product.show_property",compact("product","goods_kind_property","file","back_to_edit"));
    }
}
