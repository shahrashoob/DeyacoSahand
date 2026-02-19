<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation\ProductShow;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind\GoodsKindSettingValue;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMDegree;
use App\Models\LineProduct\Product\BOM\BOMReplace;
use App\Models\LineProduct\Product\ConsumedProduct\ConsumedProduct;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ConsumedProductController extends Controller
{
    // line_product_station/product/consumed_product
    public $route_path = "line_product_station.product.product_creation.product_show.consumed_product.";
    public $view_path = "line_product_station.product.product_creation.product_show.consumed_product.";

    public function index(ProductCreationProcess $product_creation_process)
    {


        return \App\Http\Controllers\LineProductStation\Product\ConsumedProductController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process);
    }



}
