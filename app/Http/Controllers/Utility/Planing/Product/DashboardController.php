<?php

namespace App\Http\Controllers\Utility\Planing\Product;

use App\Http\Controllers\Contractor\Panel\PrintController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LineProductStation\Product\ProductCreation\BasicInformationRegistrationController;
use App\Http\Controllers\Sales\ProductRequestPermissionController;
use App\Models\File\File;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKind\GoodsKindSettingValue;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMDegree;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\GoodsKind\GoodsKindClassificationProduct;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\MachineProductPropertyValue;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMReplace;
use App\Models\LineProduct\Product\ConsumedProduct\ConsumedProduct;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\LineProduct\Product\ProductWarehouseStorageType;
use App\Models\LineProduct\ProductType;
use App\Models\LineProduct\ReplaceProduct;
use App\Models\Order\Order;
use App\Models\Order\OrderList;
use App\Models\Production\Production;
use App\Models\Supplier\SupplierProduct;
use App\Models\Utility\Message;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DashboardController extends Controller
{

    var $view_path = "utility.planing.product.dashboard.";

    public function index(Request $request)
    {
//        $request->all();
        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
            $goods_kind_id = $request->goods_kind_id;
            $property_id = $request->property_id;
            $property_value = $request->property_value;
            $property_value_id = $request->property_value_id;
            $product_planing_algorithm_id = $request->product_planing_algorithm_id;
        } else {
            $search = session("search_cost_center");
            $order_by = session("order_by_cost_center");
            $goods_kind_id = session("goods_kind_id");
            $property_id = session("property_id");
            $property_value = session("property_value");
            $property_value_id = session("property_value_id");
            $product_planing_algorithm_id = session("product_planing_algorithm_id");
        }
        session([
            "search_cost_center" => $search,
            "order_by_cost_center" => $order_by,
            "goods_kind_id" => $goods_kind_id,
            "property_id" => $property_id,
            "property_value" => $property_value,
            "property_value_id" => $property_value,
            "product_planing_algorithm_id" => $product_planing_algorithm_id,
        ]);


        $property = GoodsKindProperty::find($property_id ?? 0);


        $property_value_for_search = ($property->field_type_id ?? 0) == 3 ? $property_value_id : $property_value;

        $list = Product::  when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                return $query->where("code", "like", "%" . $search . "%")->
                orWhere("caption", "like", "%" . $search . "%");
            });

        })->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);
        })->
        when($product_planing_algorithm_id != "", function ($query) use ($product_planing_algorithm_id) {
            return $query->where("product_planing_algorithm_id", $product_planing_algorithm_id);
        })->
        when($goods_kind_id != 0, function ($query) use ($goods_kind_id) {
            return $query->where("goods_kind_id", $goods_kind_id);
        })->
        when($property_id, function ($query) use ($property_id, $property_value_for_search) {
            return $query->join("goods_kind_property_values", "products.id", "product_id")->
            where("goods_kind_property_id", $property_id)->
            where("goods_kind_property_values.value", $property_value_for_search);
        })->
        select("products.*")->
        with("goods_kind", "product_planing_algorithm")->
        paginate(50);

        $product_ids = [];
        $product_ids = [-1];

        foreach ($list as $item) {
            $product_ids[] = $item->id;

        }

        $order_by_Option = Option::OrderBy("product_list", $order_by);
        $goods_kind_option = Option::get("goods_kind", $goods_kind_id);

        $property_option = Option::get("get_property_by_goods_kind", $property_id, $goods_kind_id);
        $property_value_option = Option::get("goods_kind_property_option", $property_value_id, $property_id);
        $algorithm_option = Option::get("algorithm", $product_planing_algorithm_id, 500);
        // مقدار سفارش
        // براساس وضعیت های مجاز برنامه ریزی در تنظیمات فروش
        $status_list = Setting::getStringValue("sale_planing_status_list");
        $status_list = json_decode($status_list, true);


        $property_list = GoodsKindProperty::        where(
            [
                "status_id" => 1200,
                "field_type_id" => 3
            ])->
        pluck("id", "id")->
        toArray();

//        $list_order_amount = Order::join("order_factor", "orders.id", "order_id")->
//
//        when($product_ids, function ($query) use ($product_ids) {
//            return $query->whereIn("order_factor.product_id", $product_ids);
//        })->
//        whereIn("orders.status_id", $status_list)->
//        groupBy("product_id")->
//        selectRaw("sum(carton* number_in_carton) as amount, product_id")->
//        pluck("amount", "product_id");
//        $list_order_amount = Order::join("order_factor", "orders.id", "order_id")->
//
//        when($product_ids, function ($query) use ($product_ids) {
//            return $query->whereIn("order_factor.product_id", $product_ids);
//        })->
//        whereIn("orders.status_id", $status_list)->
//        groupBy("product_id")->
//        selectRaw("sum(carton* number_in_carton) as amount, product_id")->
//        pluck("amount", "product_id");

        /// مقدار کارت تولید و تخصیص
        $production_sum = Production::where("status_id", "!=", 520)->
        when($product_ids, function ($query) use ($product_ids) {
            return $query->whereIn("product_id", $product_ids);
        })->selectRaw("sum(number) as number, product_id")->
        groupBy("product_id")->
        pluck("number", "product_id");


        $production_form_item_sum = Production::
        join("production_form_item", "production_cards.id", "production_form_item.production_id")->
        where("production_cards.status_id", "!=", 520)->
        when($product_ids, function ($query) use ($product_ids) {
            return $query->whereIn("production_cards.product_id", $product_ids);
        })->
        selectRaw("sum(final_amount) as final_amount, production_cards.product_id")->
        pluck("final_amount", "product_id");

        $production_result = [];

        foreach ($product_ids as $product_id) {
            $production_result[$product_id] = 0;
            if (isset($production_sum[$product_id])) {
                $production_result[$product_id] += $production_sum[$product_id];
            }
            if (isset($production_form_item_sum[$product_id])) {
                $production_result[$product_id] -= $production_form_item_sum[$product_id];
            }

            $production_sum[$product_id] = max(0, $production_result[$product_id]);

            $list_current_delivery_products[$product_id] = 0;
        }

        // موجودی فعلی کالا
        $product_inventory = WarehouseProduct::getProductInventoryList($product_ids);

        //موجودی در راه
        $in_the_way_products = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_id")->
        whereIn("product_id", $product_ids)->

        whereIn("packing_forms.status_id",
            [
                7007002, // در انتظار تایید انبار
                7007005, // در انتظار تحویل به انبار
                7007026, // در انتظار کنترل کیفیت
            ]
        )->
        groupBy("product_id")->
        selectRaw("sum(final_amount) as final_amount, product_id")->
        pluck("final_amount", "product_id");


        return view("utility.planing.product.dashboard.index", compact("order_by_Option",
            "property_value_option", "search", "property_value", "list",
            "goods_kind_option", "property_option", "property_list", "production_sum", "product_inventory", "in_the_way_products",
            "product_planing_algorithm_id", "algorithm_option"
        ));
    }

    public function packing_type_details(Product $product)
    {

        $result = PackingForm::GetInventoryBuyPackingType($product);
        $inventory = $result["inventory"];
        $packing_type_list = $result["packing_type_list"];

        return view($this->view_path . "packing_type_details", compact("product", "packing_type_list", "inventory"));

    }

    public function packing_form_details(Product $product, PackingType $packing_type)
    {

        $packing_forms = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_id")->
        where("product_id", $product->id)->
        where("warehouse_status_id", 4201)->
        where("packing_type_id", $packing_type->id)->
        whereIn("packing_forms.status_id",
            [
                7007003, // تحویل شده به انبار
            ]
        )->
        paginate(30);


        return view($this->view_path . "packing_form_details", compact("product", "packing_type", "packing_forms"));

    }


}