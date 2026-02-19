<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind\GoodsKindClassificationProduct;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class

ClassificationController extends Controller
{
    public $route_path = "line_product_station.product.classification.";
    public $view_path = "line_product_station.product.classification.";

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path,$this->route_path,null);
    }

    public function submit(Request $request, Product $product)
    {
        $result = self::PostSubmit($request, $product);
        if ($result["result"]) {
            return redirect()->route("line_product_station.product.property.index", $product)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }

    public static function GetIndex(Product $product, $view_path,$route_path,$product_creation_process)
    {
        $classification_option_list = [];
        foreach ($product->goods_kind->classification as $item) {
            $classification_product = GoodsKindClassificationProduct::where([
                "product_id" => $product->id,
                "goods_kind_classification_id" => $item->id
            ])->first();
            $classification_option_list[$item->id] = Option::get(
                "goods_kind_classification_option",
                $classification_product->goods_kind_classification_option_id ?? 0,
                $item->id);
        }

        return view(
            $view_path . "index", compact(
                "product", "product_creation_process",        "view_path","route_path", "classification_option_list")
        );

    }

    public static function PostSubmit(Request $request, Product $product)
    {

        GoodsKindClassificationProduct::where([
            "product_id" => $product->id
        ])->
        delete();

        foreach ($product->goods_kind->classification as $item) {
            $option_id = "classification_" . $item->id;

            GoodsKindClassificationProduct::create([
                "product_id" => $product->id,
                "goods_kind_classification_id" => $item->id,
                "goods_kind_classification_option_id" => $request->$option_id
            ]);
        }

        return ["result" => true, "message" => "اطلاعات با موفقیت ذخیره شد"];

    }
}
