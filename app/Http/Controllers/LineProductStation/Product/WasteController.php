<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class WasteController extends Controller
{
    var $view_path = "line_product_station.product.waste.";
    var $route_path = "line_product_station.product.waste.";
    public static $max_waste = 4;

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path, $this->route_path, null);
    }

    public function submit(Request $request, Product $product)
    {
        $result = self::PostSubmit($request, $product);
        if ($result["result"]) {
            return redirect()->route("line_product_station.product.material_flow.index", $product)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }

    public static function GetIndex(Product $product, $view_path, $route_path, $product_creation_process)
    {

        $max_waste=self::$max_waste;
        $list = Product\Waste\ProductWaste::where("product_id", $product->id)->get();

        $product_waste_value = [];
        $product_waste_percent = [];
        foreach ($list as $product_waste) {
            $key = $product_waste->waste_type_id . "_" . ($product_waste->product_route_id ?? 0) . "_" . $product_waste->number;
            $product_waste_value[$key] = $product_waste;


        }

        foreach ($product->route()->where("active_status_id", 1200)->get() as $route) {

            for ($k = 1; $k <= $max_waste; $k++) {


                $key = "2" . "_" . $route->id . "_" . $k;
                $value = isset($product_waste_value[$key]) ? $product_waste_value[$key]->waste_id : 0;
                $percent = isset($product_waste_value[$key]) ? $product_waste_value[$key]->percent : "";
                $product_waste_percent[$key] = $percent;
                $product_option_list [$key] = Option::get("product_by_goods_kind", $value, 8);;
            }
        }

        for ($k = 1; $k <= $max_waste; $k++) {


            $key = "1" . "_0_" . $k;
            $value = isset($product_waste_value[$key]) ? $product_waste_value[$key]->waste_id : 0;

            $product_option_list [$key] = Option::get("product_by_goods_kind", $value, 8);;
        }


        return view($view_path . "index", compact("product", "product_option_list", "max_waste", "product_waste_percent",
            "product_creation_process", "view_path", "route_path"));

    }

    public static function PostSubmit(Request $request, Product $product)
    {
        $max_waste=self::$max_waste;
        Product\Waste\ProductWaste::where("product_id", $product->id)->delete();

        // ضایعات مسیر محصول
        foreach ($product->route()->where("active_status_id", 1200)->get() as $route) {
            for ($k = 1; $k <= $max_waste; $k++) {
                $key = "waste_in_route_" . $route->id . "_" . $k;
                $key_percent = $key . "_percent";

                if (isset($request->$key)) {
                    Product\Waste\ProductWaste::create([
                        "product_id" => $product->id,
                        "waste_id" => $request->$key,
                        "waste_type_id" => 2,
                        "product_route_id" => $route->id,
                        "number" => $k,
                        "percent" => $request->$key_percent ?? 0
                    ]);
                }
            }
        }

        // ضایعات مصرف
        for ($k = 1; $k <= $max_waste; $k++) {
            $key = "waste_during_consumption_id_" . $k;
            if (isset($request->$key)) {
                Product\Waste\ProductWaste::create([
                    "product_id" => $product->id,
                    "waste_id" => $request->$key,
                    "waste_type_id" => 1,
                    "product_route_id" => null,
                    "number" => $k
                ]);
            }
        }


        return [
            "result" => true,
            "message" => "اطلاعات با موفقیت ذخیره شد"
        ];


    }


}
