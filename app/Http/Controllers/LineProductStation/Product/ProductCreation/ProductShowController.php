<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ProductShowController extends Controller
{
    //
    public static $info = [
        "route" => "line_product_station.product.product_creation.product_show.",
        "view" => "line_product_station.product.product_creation.product_show.",
        "enable_status" => ["001", "002", "003", "004", "005", "006", "007", "008",
            "009", "010", "011", "012", "013", "014", "015", "016", "017", "018", "019", "020",
            "021", "022", "023", "024", "025", "026", "027", "028", "029", "030", "031", "032", "033", "034","201","301","302","303","501"],
        "priority_number" => "",
        "button" => ["caption" => "مشاهده اطلاعات کالا/خدمت", "class" => "btn-primary"],
        "button_id" => "",

    ];
    protected $dashboard_path = "line_product_station.product.product_creation.dashboard.";

    public function __construct()
    {
        $this->view_path = self::$info["view"];
        $this->route_path = self::$info["route"];
    }

    public function index(ProductCreationProcess $product_creation_process)
    {
        $product = $product_creation_process->product;
        $status_option = Option::get("status", $product->active_status_id??"", 1100);
        if (isset($product->product_service_type_id)&&$product->product_service_type_id == 1) {
            $unit_option = Option::get("unit", $product->unit_id, $product->goods_kind_id, [], "default_unit_ids");
            $sub_unit_option = Option::get("unit", $product->sub_unit_id, $product->goods_kind_id, [], "default_sub_unit_ids");
            $sub_unit2_option = Option::get("unit", $product->sub_unit2_id, $product->goods_kind_id, [], "default_sub_unit2_ids");
        } else {
            $unit_option = Option::get("unit", $product->unit_id??"");
            $sub_unit_option = Option::get("unit", $product->sub_unit_id??"");
            $sub_unit2_option = Option::get("unit", $product->sub_unit2_id??"");
        }
        $supply_type_option = Option::get("supply_type", $product->supply_type_id??"");
        $goods_kind_option = Option::get("goods_kind", $product->goods_kind_id??"");
        $product_service_type_option = Option::get("product_service_type", $product->product_service_type_id??"");
        $service_id_in_employer_system_option = Option::get("product_service_active", $product->service_id_in_employer_system??"");

        return view($this->view_path . "index",
            compact(
                "sub_unit_option",
                "sub_unit2_option",
                "supply_type_option", "goods_kind_option",
                "unit_option", "status_option",
                "product_service_type_option",
                "service_id_in_employer_system_option",
                "product",'product_creation_process')
        );

    }

}
