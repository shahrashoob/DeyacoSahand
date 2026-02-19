<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation\ProductShow;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Tariff\NewTariffProduct;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Accounting\Tariff\ProductTariffLog;
use App\Models\Accounting\Tariff\ProductTariffPricing;
use App\Models\Accounting\Tariff\Tariff;
use App\Models\Form\Packing\PackingFormActualCost;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    // line_product_station/product/actual_cost/
    public $route_path = "line_product_station.product.product_creation.product_show.pricing.";
    public $view_path = "line_product_station.product.product_creation.product_show.pricing.";

    public function index(ProductCreationProcess $product_creation_process)
    {
        return \App\Http\Controllers\LineProductStation\Product\PricingController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process, 0, 1, 1, []);
    }

}
