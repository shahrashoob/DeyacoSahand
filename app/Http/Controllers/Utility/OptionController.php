<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsType;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function Symfony\Component\String\b;

class OptionController extends Controller {
    //
    public function get( Request $request ) {

        $id          = $request->id ?? "";
        $type        = $request->type ?? "";
        $lable       = $request->label ?? "**";
        $model       = $request->model ?? "";
        $selected_id = $request->selected_id ?? "not_set_id";
        $list        = $request->list ?? [];
        $default_value        = $request->default_value ?? "";
        $select_defult_if_count_is_one =$request->select_defult_if_count_is_one ?? false;

        $option      = Option::get( $model, $id, $type, $list ,$default_value,[], $select_defult_if_count_is_one);
        //  echo $select_defult_if_count_is_one;
        //  die();
        $view = view( "component.input._select", [
            "id"        => $selected_id,
            "label"     => $lable,
            "option"    => isset($option["items"])? $option["items"]:[],
            "val"       =>isset( $option["value"])?$option["value"]:"",
            "text"      =>isset( $option["text"])?$option["text"]:"",
            "class_col" => "",
          
            ] )->render();

        return $view;
    }

    public function get_degree( Request $request ) {
        $product_id   = $request->id ?? "";
        $value        = "test";
        $option_id    = $request->option_id ?? "";
        $option_label = $request->option_label ?? "";
        $option_val   = $request->option_val ?? "";

        $product = Product::find( $product_id );
        $option  = Option::get( "degree", 0, $product->goods_kind_id ?? 0 );

        $view = view( "component.input._select", [
            "id"        => $option_id,
            "label"     => $option_label,
            "option"    => $option["items"],
            "val"       => $option_val,
            "text"      => $value,
            "class_col" => ""
            
        ] )->
        render();

        return $view;
    }

    public function get_property_input_by_property_id( Request $request ) {
        $goods_kind_property = GoodsKindProperty::find( $request->id );
$value=$request->value;

        if ( ! $goods_kind_property ) {
            return view( "component.input._text", [
                "id"        => "search",
                "label"     => " متن جستجو",
                "value"     => $value,
                "class_col" => ""
            ] )->render();
        }

        switch ( $goods_kind_property->field_type_id ) {
            case 1:
                return view( "component.input._number", [
                    "id"        => "search",
                    "label"     => "مقدار " . $goods_kind_property->caption,
                    "value"     => $value,
                    "class_col" => ""
                ] )->render();
                break;
            case 2:
                return view( "component.input._text", [
                    "id"        => "search",
                    "label"     => "مقدار " . $goods_kind_property->caption,
                    "value"     =>$value,
                    "class_col" => ""
                ] )->render();
                break;
            case 3:
                $option = Option::get( "goods_kind_property_option", $value, $goods_kind_property->id );

                return view( "component.input._select", [
                    "id"        => "search",
                    "option"    => $option["items"],
                    "label"     => "مقدار " . $goods_kind_property->caption,
                    "val"       =>$option["value"] ,
                    "text"       =>$option["text"] ,
                    "class_col" => ""
                ] )->render();
                break;
            default:
                return view( "component.input._text", [
                    "id"        => "search",
                    "label"     => " متن جستجو",
                    "value"     => $value,
                    "class_col" => ""
                ] )->render();
                break;

        }

    }
}
