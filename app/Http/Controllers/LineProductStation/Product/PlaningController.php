<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductWarehouseStorageType;
use App\Models\LineProduct\Reservoir\Reservoir;
use App\Models\Utility\Option;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseShelving\WarehouseShelving;
use App\Models\Warehouse\WarehouseShelving\WarehouseShelvingProduct;
use App\Models\Warehouse\WarehouseShelving\WarehouseShelvingType;
use App\Models\Warehouse\WarehouseStorageType;
use Illuminate\Http\Request;

class PlaningController extends Controller
{
    public $route_path = "line_product_station.product.planing.";
    public $view_path = "line_product_station.product.planing.";
    public $dashboard_route = "line_product_station.product.index";

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path, $this->route_path, null);
    }

    public function submit(Request $request, Product $product)
    {
       
        $result = self::PostSubmit($request, $product);
        if ($result["result"]) {
            return redirect()->route("line_product_station.product.actual_cost.index", $product)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }


    public static function GetIndex(Product $product, $view_path, $route_path, $product_creation_process)
    {
        $product_planing_algorithm = Option::get("algorithm", $product->product_planing_algorithm_id,500);
        $production_algorithm = Option::get("algorithm", $product->production_algorithm_type_id ,300);
        $order_point_algorithm = Option::get("algorithm", $product->order_point_algorithm_type_id ,700);
        $lidetime_algorithm = Option::get("algorithm", $product->lidetime_algorithm_type_id ,800);

        return view($view_path . "index", compact(
                "product",
                "product_creation_process", "route_path", "view_path","product_planing_algorithm","production_algorithm","order_point_algorithm","lidetime_algorithm"
            )
        );
    }
    public static function PostSubmit(Request $request, Product $product)
    {
        // return $request->all();
        if ($request->testing_amount < 0) {
            return [
                "result" => false,
                "error" => "لطفا مقداری بیش از 0 برای مقدار تست وارد کنید."
            ];
        }

        $product->update($request->all());
        $product->in_implementation = $request->in_implementation === 'on' ? 1 : 0;
        $product->in_order_by_user  = $request->in_order_by_user  === 'on' ? 1 : 0;
        $product->in_order_by_diaco_script = $request->in_order_by_diaco_script === 'on' ? 1 : 0;
        $product->by_deyaco_script = $request->by_deyaco_script === 'on' ? 1 : 0;
        $product->save();



        return [
            "result" => true,
            "message" => "اطلاعات با موفقیت ذخیره شد"
        ];


    }

}
