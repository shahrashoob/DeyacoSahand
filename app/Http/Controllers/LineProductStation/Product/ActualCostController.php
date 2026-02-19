<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingFormActualCost;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ActualCostController extends Controller
{
    // line_product_station/product/actual_cost/
    public $route_path = "line_product_station.product.actual_cost.";
    public $view_path = "line_product_station.product.actual_cost.";

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path,$this->route_path,null);
    }


    public static function GetIndex(Product $product, $view_path,$route_path,$product_creation_process)
    {
        $packing_form_actual_cost = PackingFormActualCost::where("product_id", $product->id)->first();
        return view($view_path . "index", compact("product", "packing_form_actual_cost",        "view_path","route_path","product_creation_process"));
    }

}
