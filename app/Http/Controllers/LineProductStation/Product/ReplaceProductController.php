<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\ReplaceProduct;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ReplaceProductController extends Controller
{
    public $route_path = "line_product_station.product.replace_product.";
    public $view_path = "line_product_station.product.replace_product.";

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path, $this->route_path, null);
    }

    public function submit(Request $request, Product $product)
    {
        $result = self::PostSubmit($request, $product);
        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }


    public function delete(Product $product, $replace_product_id)
    {
        $result = self::GetDelete($product, $replace_product_id);
        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

    }

    public static function GetIndex(Product $product, $view_path, $route_path, $product_creation_process)
    {
        $product_option = Option::get("product_by_goods_kind", 0, $product->goods_kind_id);

        return view($view_path . "index", compact("product", "product_creation_process", "view_path", "route_path", "product_option"));
    }

    public static function PostSubmit(Request $request, Product $product)
    {
        if ($request->replace_material_id == $product->id || $request->replace_product_id == $product->id) {
            return [
                "result" => false,
                "error" => "جایگزین محصول نمی تواند خودش باشد."
            ];
        }
        if (
            ReplaceProduct::where([
                "product_id" => $product->id,
                "replace_product_id" => $request->replace_product_id
            ])->exists() ||
            ReplaceProduct::where([
                "product_id" => $product->id,
                "replace_product_id" => $request->replace_material_id
            ])->exists()) {
            return [
                "result" => false,
                "error" => "این جایگزین قبلا تعریف شده است."
            ];
        }

        ReplaceProduct::create([
            "product_id" => $product->id,
            "replace_product_id" => $request->replace_material_id
        ]);


        return [
            "result" => true,
            "message" => "محصول جایگزین با موفقیت ثبت گردید"
        ];


    }

    public static function GetDelete($product, $replace_product_id)
    {
        if (!ReplaceProduct::where([
            "product_id" => $product->id,
            "replace_product_id" => $replace_product_id
        ])->exists()) {
            return [
                "result" => false,
                "error" => "کد محصول نا معتبر است."
            ];
        }

        $bom_replace = Product\BOM\BOMReplace::where("replace_product_id", $replace_product_id)->get();
        if (count($bom_replace) > 0) {
            $message = "با توجه به اینکه کالای جایگزین در BOM کالاهای زیر استفاده شده است، امکان حذف وجود ندارد: <br/>";
            $k = 1;
            foreach ($bom_replace as $item) {
                $message .= ($k++) . "- " . $item->product->fullCaption() . "<br/>";
            }
            return [
                "result" => false,
                "error" => $message
            ];
        }
        ReplaceProduct::where([
            "product_id" => $product->id,
            "replace_product_id" => $replace_product_id
        ])->delete();

        return [
            "result" => true,
            "message" => "حذف با موفقیت انجام شد."
        ];
    }
}
