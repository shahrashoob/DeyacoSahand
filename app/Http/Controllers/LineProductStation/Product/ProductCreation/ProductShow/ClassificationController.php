<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation\ProductShow;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind\GoodsKindClassificationProduct;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    public $route_path = "line_product_station.product.product_creation.product_show.classification.";
    public $view_path = "line_product_station.product.product_creation.product_show.classification.";

    public function index(ProductCreationProcess $product_creation_process)
    {

        return self::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process);
    }

    public static function GetIndex(Product $product, $view_path,$route_path,$product_creation_process)
    {
        $classification_product = GoodsKindClassificationProduct::where([
            "product_id" => $product->id,
        ])->get();

        return view(
            $view_path . "index", compact(
                "product", "product_creation_process", "classification_product" ,      "view_path","route_path")
        );

    }
}
