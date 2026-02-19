<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation\ProductShow;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class SupplementaryController extends Controller
{
    public $route_path = "line_product_station.product.product_creation.product_show.supplementary.";
    public $view_path = "line_product_station.product.product_creation.product_show.supplementary.";

    function index(ProductCreationProcess $product_creation_process)
    {
        $view_path = "line_product_station.product.product_creation.product_show.supplementary.";
        $product = $product_creation_process->product;
        $unit_option = Option::get("unit", $product->unit_id, $product->goods_kind_id, [], "default_unit_ids");
        $sub_unit_option = Option::get("unit", $product->sub_unit_id, $product->goods_kind_id, [], "default_sub_unit_ids");
        $sub_unit2_option = Option::get("unit", $product->sub_unit2_id, $product->goods_kind_id, [], "default_sub_unit2_ids");
        $goods_type_option = Option::get("goods_type", $product->goods_type_id, $product->goods_kind_id, [], "default_goods_type_ids");

        return view($this->view_path . "index",
            compact("sub_unit_option", "unit_option", "goods_type_option",
                "sub_unit2_option", "product",'product_creation_process','view_path')
        );

    }
}
