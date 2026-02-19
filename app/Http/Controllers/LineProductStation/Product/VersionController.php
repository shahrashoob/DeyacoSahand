<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingFormActualCost;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsKindPropertyOption;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    // line_product_station/product/actual_cost/
    public $route_path = "line_product_station.product.version.";
    public $view_path = "line_product_station.product.version.";

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path, $this->route_path, null);
    }

    public function details(Product $product, $type ,Product\Version\ProductVersion $product_version)
    {
        return self::GetDetails($product, $type, $product_version,$this->view_path,$this->route_path,null);
    }

    public static function GetDetails(Product $product, $type,Product\Version\ProductVersion $product_version, $view_path, $route_path, $product_creation_process)
    {
        $before_product_version = Product\Version\ProductVersion::
        where("product_id", $product->id)->
        where("id", "<", $product_version->id)->
        orderBy("id", "desc")->
        first();

        switch ($type) {
            case "product_cols":
                $version_cols=Product\Version\ProductVersion::$version_cols;
                return view($view_path . "product_cols", ["product" => $product,"before_product_version"=>$before_product_version,"product_version"=>$product_version,"version_cols"=>$version_cols, "product_creation_process" => $product_creation_process, "route_path" => $route_path,"view_path"=>$view_path]);
                break;
            case "property":
                $current_property=json_decode($product_version->property_json,true);
                $before_property=[];
                if($before_product_version && $before_product_version->property_json){
                    $before_property=json_decode($before_product_version->property_json,true);
                }
                $all_property_ids=array_merge(array_keys($current_property),array_keys($before_property));
                $all_properties=GoodsKindProperty::whereIn("id", $all_property_ids)->get()->keyBy("id");

               foreach ($current_property as $key=>$property) {
                    if( $all_properties[$key]->field_type_id==3){
                        $current_property[$key]=GoodsKindPropertyOption::find($property)->caption??"";
                    }
               }

                foreach ($before_property as $key=>$property) {
                    if( $all_properties[$key]->field_type_id==3){
                        $before_property[$key]=GoodsKindPropertyOption::find($property)->caption??"";
                    }
                }

                return view($view_path . "property", ["all_properties"=>$all_properties, "current_property"=>$current_property,"before_property"=>$before_property,"product" => $product,"before_product_version"=>$before_product_version,"product_version"=>$product_version, "product_creation_process" => $product_creation_process, "route_path" => $route_path,"view_path"=>$view_path]);
                break;
            case "consume":
                $current_consume=json_decode($product_version->consume_json,true);
                $before_consume=[-1=>""];
                if($before_product_version && $before_product_version->consume_json){
                    $before_consume=json_decode($before_product_version->consume_json,true);
                }
                $all_consume_ids=array_merge($current_consume,$before_consume);
                $all_products=Product::whereIn("id", $all_consume_ids)->get()->keyBy("id");
                return view($view_path . "consume", ["all_products"=>$all_products,"current_consume"=>$current_consume,"before_consume"=>$before_consume,"product" => $product,"before_product_version"=>$before_product_version,"product_version"=>$product_version, "product_creation_process" => $product_creation_process, "route_path" => $route_path,"view_path"=>$view_path]);

            case "bom":
                return view($view_path . "bom", ["product" => $product,"before_product_version"=>$before_product_version,"product_version"=>$product_version, "product_creation_process" => $product_creation_process, "route_path" => $route_path]);

            case "bom_permutation":
                $current_bom_permutation=json_decode($product_version->bom_permutation_json,true);
                $before_bom_permutation=[];
                if($before_product_version && $before_product_version->bom_permutation_json){
                    $before_bom_permutation=json_decode($before_product_version->bom_permutation_json,true);
                }
                $all_bom_permutation_ids=array_merge($current_bom_permutation,$before_bom_permutation);
                $all_bom_permutation=Product\BOM\BOMPermutation::whereIn("id", $all_bom_permutation_ids)->get()->keyBy("id");
                return view($view_path . "bom_permutation", ["product" => $product,"all_bom_permutation"=>$all_bom_permutation,"current_bom_permutation"=>$current_bom_permutation,"before_bom_permutation"=>$before_bom_permutation,"product_version"=>$product_version, "product_creation_process" => $product_creation_process, "route_path" => $route_path,"view_path"=>$view_path]);

        }
    }

    public static function GetIndex(Product $product, $view_path, $route_path, $product_creation_process)
    {

        return view($view_path . "index", compact("product", "view_path", "route_path", "product_creation_process"));
    }

}
