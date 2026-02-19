<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ProductRouteController extends Controller
{

    private $route_path = "line_product_station.product.route.";
    private $view_path = "line_product_station.product.route.";

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path, $this->route_path, null);
    }

    public function create(Product $product)
    {
        return self::GetCreate($product, $this->view_path, $this->route_path, null);
    }

    public function store(Request $request, Product $product)
    {

        $result = self::PostStore($request, $product);
        if ($result["result"]) {
            return redirect()->route($this->route_path . "index", $product)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }

    public function edit(Product $product, ProductRoute $product_route)
    {

        return self::GetEdit($product, $product_route, $this->view_path, $this->route_path, null);
    }

    public function update(Request $request, Product $product, ProductRoute $product_route)
    {
        $result = self::PostUpdate($request, $product,$product_route);
        if ($result["result"]) {
            return redirect()->route($this->route_path . "index", $product)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

    }

    public function destroy(Product $product, ProductRoute $product_route)
    {
        $result= self::GetDestroy($product, $product_route, $this->view_path, $this->route_path, null);

        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }


    /**************** توابع Static *************/

    public static function GetIndex(Product $product, $view_path, $route_path, $product_creation_process)
    {
        return view($view_path . "index", compact("product", "product_creation_process", "view_path", "route_path"));
    }

    public static function GetCreate(Product $product, $view_path, $route_path, $product_creation_process)
    {
        $status_option = Option::get("status", 1200, 1100);
        return view($view_path . "create", compact("product", "status_option", "product_creation_process", "view_path", "route_path"));
    }
    public static function PostStore(Request $request, Product $product)
    {
        $exist_item = ProductRoute::exist($product->id, $request->caption);
        if ($exist_item) {
            return [
                "result" => false,
                "error" => "این مسیر قبلا تعریف شده است."
            ];

        }
        $request["product_id"] = $product->id;
        $request["supply_type_id"] = $product->supply_type_id;
        ProductRoute::create($request->all());

        return [
            "result" => true,
            "message" => "یک مسیر با موفقیت اضافه گردید."
        ];


    }

    public static function GetEdit(Product $product, ProductRoute $product_route, $view_path, $route_path, $product_creation_process)
    {
        $status_option = Option::get("status", $product_route->active_status_id, 1100);
        return view($view_path . "edit", compact("product", "status_option", "product_route", "product_creation_process", "view_path", "route_path"));
    }
    public static function PostUpdate(Request $request, Product $product, ProductRoute $product_route)
    {
        $exist_item = ProductRoute::exist($product->id, $request->caption, $product_route->id);
        if ($exist_item) {
            return [
                "result" => false,
                "error" => "این مسیر قبلا تعریف شده است."
            ];
        }


        $product_route->update($request->all());

        return [
            "result" => true,
            "message" => "اطلاعات با موفقیت ذخیره گردید."
        ];

    }

    public static function GetDestroy(Product $product, ProductRoute $product_route, $view_path, $route_path, $product_creation_process)
    {

        return [
            "result" => false,
            "error" => "این ماژول در دست پیاده سازی است."
        ];

//        if ($product->id != $line_product_station->product_id) {
//            return back()->withErrors("درخواست نا معتبر");
//        }
//
//        $line_product_station->delete();
//
//        return back()->with(["success" => "حذف با موفقیت انجام شد."]);
    }


}
