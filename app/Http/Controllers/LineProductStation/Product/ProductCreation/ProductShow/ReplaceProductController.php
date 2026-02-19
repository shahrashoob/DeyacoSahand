<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation\ProductShow;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\LineProduct\ReplaceProduct;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ReplaceProductController extends Controller
{
    public $route_path = "line_product_station.product.product_creation.product_show.replace_product.";
    public $view_path = "line_product_station.product.product_creation.product_show.replace_product.";

    public function index(ProductCreationProcess $product_creation_process)
    {


        return \App\Http\Controllers\LineProductStation\Product\ReplaceProductController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process);
    }



}
