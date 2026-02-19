<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation\ProductShow;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class WasteController extends Controller
{
    var $view_path = "line_product_station.product.product_creation.product_show.waste.";
    var $route_path = "line_product_station.product.product_creation.product_show.waste.";
    public static $max_waste = 4;

    public function index(ProductCreationProcess $product_creation_process)
    {


        return \App\Http\Controllers\LineProductStation\Product\WasteController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process);
    }


}
