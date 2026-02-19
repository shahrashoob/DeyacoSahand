<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation\ProductShow;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LotNumberController extends Controller
{
    //
    var $route_path="line_product_station.product.product_creation.product_show.lot_number.";
    var $view_path="line_product_station.product.product_creation.product_show.lot_number.";
    public function index(ProductCreationProcess $product_creation_process)
    {
        return \App\Http\Controllers\LineProductStation\Product\LotNumberController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process);
    }

}
