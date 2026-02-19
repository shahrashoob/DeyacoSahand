<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation\ProductShow;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;

class BOMPermutationController extends Controller
{
    public $route_path = "line_product_station.product.product_creation.product_show.bom_permutation.";
    public $view_path = "line_product_station.product.product_creation.product_show.bom_permutation.";

    public function index(ProductCreationProcess $product_creation_process)
    {
        return \App\Http\Controllers\LineProductStation\Product\BOM\BOMPermutationController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process);
    }

}
