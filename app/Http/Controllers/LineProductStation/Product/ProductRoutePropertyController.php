<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\MachineProductPropertyValue;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ProductRoutePropertyController extends Controller
{
    //
    private $route_path = "line_product_station.product.route_property.";
    private $view_path = "line_product_station.product.route_property.";

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path, $this->route_path, null);
    }

    public function submit(Request $request, Product $product)
    {

        $result = self::PostSubmit($request, $product);
        if ($result["result"]) {
            return redirect()->route("line_product_station.product.bom.index", $product)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

    }

    public static function GetIndex(Product $product, $view_path, $route_path, $product_creation_process)
    {
        $property_option = [];
        switch ($product->supply_type_id) {
            case 1:
                foreach ($product->route as $route) {
                    foreach ($route->line_product_station as $item) {
                        foreach ($item->station->machine_product_property as $property) {
                            $property_value = MachineProductPropertyValue::where([
                                "machine_type_id" => $item->machine_type_id,
                                "product_id" => $product->id,
                                "station_id" => $item->station_id,
                                "machine_product_property_id" => $property->id,
                                "station_sub_operation_id" => $item->station_sub_operation_id
                            ])->
                            first();
                            if ($property->field_type_id == 3) {
                                $property_option[$property->id][$item->station_sub_operation_id] = Option::get("machine_product_property_option", $property_value->value ?? null, $property->id);
                            }
                        }
                    }
                    break;
                }
        }

        return view($view_path . "index", compact("product", "property_option", "product_creation_process", "view_path", "route_path"));

    }
    public static function PostSubmit(Request $request, Product $product)
    {
        $data = $request->data;
        foreach ($product->route as $route) {
            foreach ($route->line_product_station as $item) {
                foreach ($item->station->machine_product_property as $property) {
                    if (!isset($data[$item->id][$item->station_sub_operation_id][$property->id])) {
                        return [
                            "result" => false,
                            "error" => "لطفا مشخصه " . $property->caption . " را برای " . $route->caption . " تکمیل نمایید."
                        ];

                    }

                    $property_value = MachineProductPropertyValue::where([
                        "machine_type_id" => $item->machine_type_id,
                        "product_id" => $product->id,
                        "station_id" => $item->station_id,
                        "machine_product_property_id" => $property->id,
                        "station_sub_operation_id" => $item->station_sub_operation_id
                    ])->
                    first();
                    if (!$property_value) {
                        $property_value = MachineProductPropertyValue::create([
                            "machine_type_id" => $item->machine_type_id,
                            "product_id" => $product->id,
                            "station_id" => $item->station_id,
                            "machine_product_property_id" => $property->id,
                            "station_sub_operation_id" => $item->station_sub_operation_id
                        ]);
                    }
                    $property_value->value = $data[$item->id][$item->station_sub_operation_id][$property->id];
                    $property_value->save();

                }
            }
        }

        return [
            "result" => true,
            "message" => "اطلاعات با موفقیت ذخیره شد"
        ];


    }
}
