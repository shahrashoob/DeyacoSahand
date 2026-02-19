<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public $route_path = "line_product_station.product.sale.";
    public $view_path = "line_product_station.product.sale.";

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path,$this->route_path,null);
    }

    public function submit(Request $request, Product $product)
    {
        $result = self::PostSubmit($request, $product);
        if ($result["result"]) {
            return redirect()->route("line_product_station.product.warehouse.index", $product)->with(["success" => $result["message"]]);
        } else {
            return redirect()->back()->withErrors($result["error"]);
        }
    }

    public static function GetIndex(Product $product, $view_path,$route_path,$product_creation_process)
    {
        $type_of_sale_product_list = Product\TypeOfSaleProduct\TypeOfSaleOfProduct::all();
        $service_option = [];
        foreach ($type_of_sale_product_list as $item) {
            $type_of_sale_product_product = Product\TypeOfSaleProduct\TypeOfSaleProductProduct::
            where([
                "product_id" => $product->id,
                "type_of_sale_of_product_id" => $item->id
            ])->
            first();

            $service_option[$item->id] = Option::get("service_all", $type_of_sale_product_product->service_id ?? "");
        }

        $type_of_sale_product_list_for_product = Product\TypeOfSaleProduct\TypeOfSaleProductProduct::
        where("product_id", $product->id)->
        pluck("type_of_sale_of_product_id")->
        toArray();

        return view($view_path . "index", compact("product", "type_of_sale_product_product","type_of_sale_product_list", "type_of_sale_product_list_for_product", "service_option","product_creation_process",
        "view_path","route_path"
        ));

    }

    public
    static function PostSubmit(Request $request, Product $product)
    {
        Product\TypeOfSaleProduct\TypeOfSaleProductProduct::where("product_id", $product->id)->delete();

        $type_of_sale_product_list = Product\TypeOfSaleProduct\TypeOfSaleOfProduct::all();
        $count = 0;
        foreach ($type_of_sale_product_list as $item) {
            if ($request["type_of_sale_" . $item->id]) {
                Product\TypeOfSaleProduct\TypeOfSaleProductProduct::
                create([
                    "product_id" => $product->id,
                    "type_of_sale_of_product_id" => $item->id,
                    "service_id" => $request["service_id_" . $item->id]
                ]);
                $count++;
            }
        }

        if ($request->possibility_of_sale == "on" && $count == 0) {
            return [
                "result" => false,
                "error" => "لطفا حداقل یک نوع فروش را انتخاب نمایید."
            ];
        }


        $request["possibility_of_sale"] = $request->possibility_of_sale == "on";
        $product->update($request->all());

        return [
            "result" => true,
            "message" => "اطلاعات با موفقیت ذخیره گردید ."
        ];

    }
}
