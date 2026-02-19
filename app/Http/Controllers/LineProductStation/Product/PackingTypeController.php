<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class PackingTypeController extends Controller
{
    public $route_path = "line_product_station.product.packing_type.";
    public $view_path = "line_product_station.product.packing_type.";

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path,$this->route_path,null);
    }

    public function submit(Request $request, Product $product)
    {
        $result = self::PostSubmit($request, $product);
        if ($result["result"]) {
            return redirect()->route("line_product_station.product.quality_control.index", $product)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }

    public static function GetIndex(Product $product, $view_path,$route_path,$product_creation_process)
    {
        return view($view_path . "index", compact("product", "product_creation_process",        "view_path","route_path"));
    }

    public static function PostSubmit(Request $request, Product $product)
    {
        $data = $request["data"];
        $packing_list = [];
        $is_it_salable_list = [];
        if (isset($data["packing_type"])) {
            foreach ($data["packing_type"] as $key => $item) {
                $packing_list[] = $key;
                if (isset($item["permission"])) {
                    // بسته بندی مجاز فروش
                    if (isset($item["is_it_salable"])) {
                        $is_it_salable_list[] = $key;
                    }

                }

            }
        }

        if ($product->possibility_of_sale && count($is_it_salable_list) == 0) {

            return [
                "result" => false,
                "error" =>"با توجه به اینکه محصول قابلیت فروش دارد، باید بسته بندی های مجاز فروش مشخص شوند."
            ];

        }

        $product->packing_types()->sync($packing_list);

// به روزرسانی بسته هایی که قابلیت فروش دارند.
        $product->packing_types()->update(["is_it_salable" => 0]);
        foreach ($is_it_salable_list as $item) {
            $product->packing_types()->where("packing_type_id", $item)->update(["is_it_salable" => 1]);
        }

        return [
            "result" => true,
            "message" =>"بسته بندی ها با موفقیت ذخیره گردید."
        ];


    }
}
