<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShadeNumberController extends Controller
{
    public $route_path = "line_product_station.product.shade_number.";
    public $view_path = "line_product_station.product.shade_number.";

    public function index(Product $product)
    {

        return self::GetIndex($product, $this->view_path, $this->route_path, null);
    }

    public function store(Request $request, Product $product)
    {
        $result = self::PostStore($request, $product);
        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }

    public static function GetIndex(Product $product, $view_path, $route_path, $product_creation_process)
    {
        return view($view_path . "index", compact("product", "product_creation_process", "view_path", "route_path"));
    }

    public static function PostStore(Request $request, Product $product)
    {

        $result = Product\ShadeNumber::ExistsCode($request->code, $product->id);
        if ($result) {
            return [
                "result" => false,
                "error" => "کد شید تکراری است"
            ];
        }

        $shade = new Product\ShadeNumber();
        $shade->code = $request->code;
        $shade->product_id = $product->id;
        $shade->user_id = Auth::id();
        $shade->save();

        return [
            "result" => true,
            "message" => "شید با موفقیت اضافه گردید"
        ];


    }

}
