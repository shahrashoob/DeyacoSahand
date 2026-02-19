<?php

namespace App\Http\Controllers\LineProductStation;

use App\Http\Controllers\Contractor\Panel\PrintController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LineProductStation\Product\ProductCreation\BasicInformationRegistrationController;
use App\Models\File\File;
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
use App\Models\Supplier\SupplierProduct;
use App\Models\Utility\Message;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductController extends Controller
{

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
        } else {
            $search = session("search_cost_center");
            $order_by = session("order_by_cost_center");
            $goods_kind_id = session("goods_kind_id");
            $property_id = session("property_id");
            $property_value = session("property_value");
            $property_value_id = session("property_value_id");
        }
        session([
            "search_cost_center" => $search,
            "order_by_cost_center" => $order_by,
            "goods_kind_id" => $goods_kind_id,
            "property_id" => $property_id,
            "property_value" => $property_value,
            "property_value_id" => $property_value_id,
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
        when($goods_kind_id != 0, function ($query) use ($goods_kind_id) {
            return $query->where("goods_kind_id", $goods_kind_id);
        })->
        when($property_id, function ($query) use ($property_id, $property_value_for_search) {
            return $query->join("goods_kind_property_values", "products.id", "product_id")->
            where("goods_kind_property_id", $property_id)->
            where("goods_kind_property_values.value", $property_value_for_search);
        })->
        select("products.*")->
        with("version")->
        paginate(50);


        $order_by_Option = Option::OrderBy("product_list", $order_by);
        $goods_kind_option = Option::get("goods_kind", $goods_kind_id);

        $property_option = Option::get("get_property_by_goods_kind", $property_id, $goods_kind_id);
        $property_value_option = Option::get("goods_kind_property_option", $property_value_id, $property_id);

        $property_list = GoodsKindProperty::        where(
            [
                "status_id" => 1200,
                "field_type_id" => 3
            ])->
        pluck("id", "id")->
        toArray();
        return view("line_product_station.product.index", compact("order_by_Option", "property_value_option", "property_list", "search", "property_value", "list", "goods_kind_option", "property_option"));
    }

    public
    function create()
    {

        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("line_product_station.product.create")) {
            return back()->withErrors("دسترسی تعریف کالا برای شما وجود ندارد.");
        }
        $product = new Product();
        $status_option = Option::get("status", $product->active_status_id, 1100);
        $unit_option = Option::get("unit", $product->unit_id, 0, []);
        $sub_unit_option = Option::get("unit", $product->sub_unit_id, 0, []);
        $sub_unit2_option = Option::get("unit", $product->sub_unit2_id, 0, []);
        $goods_kind_option = Option::get("goods_kind", $product->goods_kind_id);
        $supply_type_option = Option::get("supply_type", $product->supply_type_id);
        $goods_type_option = Option::get("goods_type", 0, $product->goods_kind_id, [], "--");
        $product_service_type_option = Option::get("product_service_type", $product->product_service_type_id);
        $exist_product_service_type_option = Option::get("product_service_type", $product->exist_product_service_type_id);

        $service_id_in_employer_system_option = Option::get("product_service_active", $product->service_id_in_employer_system);

        return view("line_product_station.product.create", compact("supply_type_option", "product_service_type_option", "goods_kind_option", "sub_unit2_option", "sub_unit_option", "supply_type_option", "goods_type_option", "unit_option", "product", "status_option", "service_id_in_employer_system_option", "exist_product_service_type_option"));
    }

    public
    function store(
        Request $request
    )
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("line_product_station.product.create")) {
            return back()->withErrors("دسترسی تعریف کالا برای شما وجود ندارد.");
        }
        $product_service_type_id = $request->product_service_type_id;
        $request["code"] = $request["code" . $product_service_type_id];
        $request["caption"] = $request["caption" . $product_service_type_id];
        $request["unit_id"] = $request["unit_id" . $product_service_type_id];
        $request["sub_unit_id"] = $request["sub_unit_id" . $product_service_type_id];
        $request["sub_unit2_id"] = $request["sub_unit2_id" . $product_service_type_id];
        $request["goods_type_id"] = $request["goods_type_id" . $product_service_type_id];
        $request["goods_kind_id"] = $request["goods_kind_id" . $product_service_type_id];
        $request["number_in_carton"] = $request["number_in_carton" . $product_service_type_id];
        $request["weight"] = $request["weight" . $product_service_type_id];
        $request["supply_type_id"] = $request["supply_type_id" . $product_service_type_id];
        $request["active_status_id"] = $request["active_status_id" . $product_service_type_id];
        $request["service_id_in_employer_system"] = $request["service_id_in_employer_system" . $product_service_type_id];


        if ($request->code == "" || Product::ExistsCode($request->code)) {
            return back()->withErrors("کد محصول تکراری است");
        }

        if ($request->caption == "" || Product::ExistsCode($request->caption, false, "caption")) {
            return back()->withErrors("نام محصول تکراری/ نامعتبر است");
        }
        $substr = substr_count($request->caption, ' ');
        if ($substr > 8) {
            return back()->withErrors("نام کالا حداکثر می تواند دارای 8 کاراکتر  Space باشد");
        }

        // اگر نوع کالا/خدمت از نوع کالا است، اطلاعات واحدها و نوع کالا در تب تکمیل اطلاعات پایه گرفته می شود و یا تکمیل می گردد.
        if ($product_service_type_id == 1) {
            $goods_kind = GoodsKind::find($request["goods_kind_id" . $product_service_type_id]);
            if (!$goods_kind) {
                return back()->withErrors("لطفا رسته کالایی را مشخص نمایید.");
            }
            $goods_kind_setting_value = GoodsKindSettingValue::getValues($goods_kind);
            if (count($goods_kind_setting_value["default_unit_ids"]) == 0) {
                return back()->withErrors("واحد اصلی مجاز برای رسته کالایی " . $goods_kind->caption . " " . " یافت نشد، لطفا با واحد پشتیبانی تماس بگیرید");
            }
            $request["unit_id"] = $goods_kind_setting_value["default_unit_ids"][0];
            if (count($goods_kind_setting_value["default_sub_unit_ids"]) == 1) {
                $request["sub_unit_id"] = $goods_kind_setting_value["default_sub_unit_ids"][0];
            }
            if (count($goods_kind_setting_value["default_sub_unit2_ids"]) == 1) {
                $request["sub_unit2_id"] = $goods_kind_setting_value["default_sub_unit2_ids"][0];
            }
            if (count($goods_kind_setting_value["default_goods_type_ids"]) == 0) {
                return back()->withErrors("نوع محصول مجاز برای رسته کالایی " . $goods_kind->caption . " " . " یافت نشد، لطفا با واحد پشتیبانی تماس بگیرید");
            }
            if (count($goods_kind_setting_value["default_goods_type_ids"]) == 1) {
                $request["goods_type_id"] = $goods_kind_setting_value["default_goods_type_ids"][0];
            }

        } else {
            $result_unit = self::checkUnit($request);
            if (!$result_unit["result"]) {
                return back()->withErrors($result_unit["error"]);
            }
        }
        $request["possibility_of_sale"] = $request->possibility_of_sale == "on";


        if ($request["supply_type_id"] == 3 && !$request["service_id_in_employer_system"]) {
            return back()->withErrors("لطفا کد خدمت در سامانه کارفرما را انتخاب نمایید.");
        }

        $product = Product::create($request->all());


        if (isset($request->image_file)) {

            $file = File::uploadFile($request->file('image_file'), $product->id . "_" . rand(1000, 9000) . ".png", 41, "upload/product/", true);

            $product->image_id = $file->id;
            $product->save();
        }

        if ($product_service_type_id == 1) {
            return redirect()->route("line_product_station.product.edit_supplementary", $product)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

        } else {
            return redirect()->route("line_product_station.product.edit", $product)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

        }
    }

    public
    function edit(
        Product $product
    )
    {

        $status_option = Option::get("status", $product->active_status_id, 1100);
        if ($product->product_service_type_id == 1) {
            $unit_option = Option::get("unit", $product->unit_id, $product->goods_kind_id, [], "default_unit_ids");
            $sub_unit_option = Option::get("unit", $product->sub_unit_id, $product->goods_kind_id, [], "default_sub_unit_ids");
            $sub_unit2_option = Option::get("unit", $product->sub_unit2_id, $product->goods_kind_id, [], "default_sub_unit2_ids");
        } else {
            $unit_option = Option::get("unit", $product->unit_id);
            $sub_unit_option = Option::get("unit", $product->sub_unit_id);
            $sub_unit2_option = Option::get("unit", $product->sub_unit2_id);
        }
        $supply_type_option = Option::get("supply_type", $product->supply_type_id);
        $goods_kind_option = Option::get("goods_kind", $product->goods_kind_id);
        $product_service_type_option = Option::get("product_service_type", $product->product_service_type_id);
        $exist_product_service_type_option = Option::get("product_service_type", $product->exist_product_service_type_id);
        $service_id_in_employer_system_option = Option::get("product_service_active", $product->service_id_in_employer_system);

        return view("line_product_station.product.edit",
            compact(
                "sub_unit_option",
                "sub_unit2_option",
                "supply_type_option", "goods_kind_option",
                "unit_option", "status_option",
                "product_service_type_option",
                "service_id_in_employer_system_option", "exist_product_service_type_option",
                "product")
        );

    }

    public
    function update(
        Request $request, Product $product
    )
    {

        $product_service_type_id = $request->product_service_type_id;
        $request["code"] = Message::convert_farsi_digits_to_english($request["code" . $product_service_type_id]);
        $request["caption"] = Message::convert_farsi_digits_to_english($request["caption" . $product_service_type_id]);
        $request["goods_kind_id"] = $request["goods_kind_id" . $product_service_type_id];
        $request["number_in_carton"] = $request["number_in_carton" . $product_service_type_id];
        $request["weight"] = $request["weight" . $product_service_type_id];
        $request["supply_type_id"] = $request["supply_type_id" . $product_service_type_id];
        $request["active_status_id"] = $request["active_status_id" . $product_service_type_id];
        $request["service_id_in_employer_system"] = $request["service_id_in_employer_system" . $product_service_type_id];


        if ($request->code == "" || Product::ExistsCode($request->code, $product->id)) {
            return back()->withErrors("کد محصول تکراری/ نامعتبر است");
        }

        if ($request->caption == "" || Product::ExistsCode($request->caption, $product->id, "caption")) {
            return back()->withErrors("نام محصول تکراری/ نامعتبر است");
        }

        $substr = substr_count($request->caption, ' ');
        if ($substr > 8) {
            return back()->withErrors("نام کالا حداکثر می تواند دارای 8 کاراکتر  Space باشد");
        }

        // اگر نوع کالا/خدمت از نوع کالا است، اطلاعات واحدها و نوع کالا در تب تکمیل اطلاعات پایه گرفته می شود و یا تکمیل می گردد.
        if ($product_service_type_id == 1) {
            $goods_kind = GoodsKind::find($request["goods_kind_id" . $product_service_type_id]);
            if (!$goods_kind) {
                return back()->withErrors("لطفا رسته کالایی را مشخص نمایید.");
            }
            $goods_kind_setting_value = GoodsKindSettingValue::getValues($goods_kind);

            if (count($goods_kind_setting_value["default_unit_ids"]) == 1) {
                $request["unit_id"] = $goods_kind_setting_value["default_unit_ids"][0];
            }
            if (count($goods_kind_setting_value["default_sub_unit_ids"]) == 1) {
                $request["sub_unit_id"] = $goods_kind_setting_value["default_sub_unit_ids"][0];
            }

            if (count($goods_kind_setting_value["default_goods_type_ids"]) == 1) {
                $request["goods_type_id"] = $goods_kind_setting_value["default_goods_type_ids"][0];
            }

        } else {
            $request["unit_id"] = $request->unit_id2;
            $request["sub_unit_id"] = $request->sub_unit_id2;
            $request["sub_unit2_id"] = $request->sub_unit2_id;
        }

        if ($request["supply_type_id"] == 3 && !$request["service_id_in_employer_system"]) {
            return back()->withErrors("لطفا کد خدمت در سامانه کارفرما را انتخاب نمایید.");
        }

        $product->update($request->all());

        if (isset($request->image_file)) {

            $file = File::uploadFile($request->file('image_file'), $product->id . "_" . rand(1000, 9000) . ".png", 41, "upload/product/", true);

            $product->image_id = $file->id;
            $product->save();
        }

        if ($product_service_type_id == 1) {
            Product\Version\ProductVersion::GetVersion($product, true, false);
            return redirect()->route("line_product_station.product.edit_supplementary", $product)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);
        } else {
            return redirect()->route("line_product_station.product.edit", $product)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

        }
    }

    public
    function edit_supplementary(
        Product $product
    )
    {


        $unit_option = Option::get("unit", $product->unit_id, $product->goods_kind_id, [], "default_unit_ids");
        $sub_unit_option = Option::get("unit", $product->sub_unit_id, $product->goods_kind_id, [], "default_sub_unit_ids");
        $sub_unit2_option = Option::get("unit", $product->sub_unit2_id, $product->goods_kind_id, [], "default_sub_unit2_ids");
        $goods_type_option = Option::get("goods_type", $product->goods_type_id, $product->goods_kind_id, [], "default_goods_type_ids");
        $unit_of_measure_type_in_production_option = Option::get("unit_of_measure_type", $product->unit_of_measure_type_id_in_production, $product->goods_kind_id, "default_unit_of_measure_type_ids_in_production");
        $unit_of_measure_type_in_sale_option = Option::get("unit_of_measure_type", $product->unit_of_measure_type_id_in_sale, $product->goods_kind_id, "default_unit_of_measure_type_ids_in_sale");

        return view("line_product_station.product.edit_supplementary",
            compact("sub_unit_option", "unit_option", "goods_type_option",
                "sub_unit2_option", "product", "unit_of_measure_type_in_production_option", "unit_of_measure_type_in_sale_option")
        );

    }

    public
    function update_supplementary(
        Request $request, Product $product
    )
    {
        $request["unit_id"] = $request["unit_id"];
        $request["sub_unit_id"] = $request["sub_unit_id"];
        $request["sub_unit2_id"] = $request["sub_unit2_id"];
        $request["goods_type_id"] = $request["goods_type_id"];


        $result_unit = BasicInformationRegistrationController::checkUnit($request);
        if (!$result_unit["result"]) {
            return back()->withErrors($result_unit["error"]);
        }

        if (!isset($request["sub_unit2_id"]) || !$request["sub_unit2_id"]) {
            $request["frame_ratio_unit2"] = null;
        }

        $product->update($request->all());
        Product\Version\ProductVersion::GetVersion($product, true, false);
        return redirect()->route("line_product_station.product.sale.index", $product)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);


    }


    public function copy_from_other()
    {

        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("line_product_station.product.create")) {
            return back()->withErrors("دسترسی تعریف کالا برای شما وجود ندارد.");
        }
        $product_option = Option::get("product_from_list", 0, 0, Product::all());

        return view("line_product_station.product.copy_from_other.copy_from_other", compact("product_option"));

    }


    public function submit_copy_from_other(Request $request)
    {

        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("line_product_station.product.create")) {
            return back()->withErrors("دسترسی تعریف کالا برای شما وجود ندارد.");
        }
        if ($request->code == "" || Product::ExistsCode($request->code)) {
            return back()->withErrors("کد محصول تکراری است");
        }

        if ($request->caption == "" || Product::ExistsCode($request->caption, false, "caption")) {
            return back()->withErrors("نام محصول تکراری/ نامعتبر است");
        }
        $substr = substr_count($request->caption, ' ');
        if ($substr > 8) {
            return back()->withErrors("نام کالا حداکثر می تواند دارای 8 کاراکتر  Space باشد");
        }

        $product = Product::find($request->product_id);

        if (!$product) {
            return back()->withErrors("کالایی که می خواهید از آن کپی کنید در سامانه تعریف نشده است.");
        }

        $new_product = self::PostCopyFromOther($request, $product, null);
        return redirect()->route("line_product_station.product.edit", $new_product)->with(["success" => "یک کالا با موفقیت کپی شد."]);

    }

    public static function PostCopyFromOther(Request $request, $product, $copyToProduct)
    {

        $new_product_info = $product->toArray();
        if (!isset($copyToProduct)) {
            $new_product_info["code"] = $request->code;
            $new_product_info["caption"] = $request->caption;


            $new_product = Product::create($new_product_info);

            $new_product->active_status_id = 1210;
            $new_product->image_id = null;
            $new_product->save();
        } else {
            unset($new_product_info["code"]);
            unset($new_product_info["caption"]);
            unset($new_product_info["image_id"]);
            $new_product_info["active_status_id"] = 1210;
            $new_product = $copyToProduct;
            $new_product->update($new_product_info);
        }
//

//        // مشخصات فروش
        if ($request->copy_sale) {
            $sale_list = Product\TypeOfSaleProduct\TypeOfSaleProductProduct::where("product_id", $product->id)->get();

            foreach ($sale_list as $item) {
                $new_sale = $item->toArray();
                $new_sale["product_id"] = $new_product->id;
                Product\TypeOfSaleProduct\TypeOfSaleProductProduct::create($new_sale);
            }
        }
//
//        // طبقه بندی
        if ($request->copy_classification) {
            $classification_list = GoodsKindClassificationProduct::where([
                "product_id" => $product->id
            ])->
            get();
            foreach ($classification_list as $item) {
                $new_classification = $item->toArray();
                $new_classification["product_id"] = $new_product->id;
                GoodsKindClassificationProduct::create($new_classification);
            }
        }

        // مشخصات کالا
        if ($request->copy_product_property_value) {
            $property_list = GoodsKindPropertyValue::where("product_id", $product->id)->get();
            foreach ($property_list as $item) {
                $property = $item->toArray();
                $property["product_id"] = $new_product->id;
                GoodsKindPropertyValue::create($property);
            }
        }

        // کالای مصرفی
        if ($request->copy_consumed) {
            $consumed_list = ConsumedProduct::where("product_id", $product->id)->get();
            foreach ($consumed_list as $item) {
                $consumed = $item->toArray();
                $consumed["product_id"] = $new_product->id;
                ConsumedProduct::create($consumed);
            }
        }

        $new_route = [];
        if ($request->copy_route) {
            foreach ($product->route as $route_item) {
                $new_route = $route_item->toArray();
                $new_route["product_id"] = $new_product->id;
                $new_route = ProductRoute::create($new_route);

                // مسیر محصول
                foreach ($route_item->line_product_station as $line_product_station) {
                    $new_line_product_station = $line_product_station->toArray();
                    $new_line_product_station["product_id"] = $new_product->id;
                    $new_line_product_station["product_route_id"] = $new_route->id;
                    unset($new_line_product_station["product_code_in_contractor_system"]);
                    unset($new_line_product_station["service_code_in_contractor_system"]);
                    LineProductStation::create($new_line_product_station);
                }

                // ضایعات
                if ($request->copy_waste) {
                    $wast_list = Product\Waste\ProductWaste::
                    where("product_id", $product->id)->
                    where("product_route_id", $route_item->id)->
                    get();
                    foreach ($wast_list as $waste_item) {
                        $waste_item = $waste_item->toArray();
                        $waste_item["product_id"] = $new_product->id;
                        $waste_item["product_route_id"] = $new_route->id;
                        Product\Waste\ProductWaste::create($waste_item);
                    }
                }


                if ($request->copy_bom) {
                    //BOM
                    $bom_list = BOM::where(["product_id" => $product->id, "product_route_id" => $route_item->id])->get();
                    foreach ($bom_list as $bom) {
                        $bom_info = $bom->toArray();
                        $bom_info["product_id"] = $new_product->id;
                        $bom_info["product_route_id"] = $new_route->id;

                        $new_bom = BOM::create($bom_info);

                        $bom_item_lists = Product\BOM\BOMItem::where("bill_of_material_id", $bom->id)->get();
                        // BOM Item
                        foreach ($bom_item_lists as $bom_item) {
                            $bom_item_info = $bom_item->toArray();
                            $bom_item_info["product_id"] = $new_product->id;
                            $bom_item_info["bill_of_material_id"] = $new_bom->id;

                            $new_bom_item = Product\BOM\BOMItem::create($bom_item_info);

                            $list_bom_degree = \App\Models\LineProduct\Product\BOM\BOMDegree::
                            where([
                                "product_id" => $product->id,
                                "bill_of_material_item_id" => $bom_item->id
                            ])->
                            get();
                            //BOM Degree
                            foreach ($list_bom_degree as $bom_degree) {
                                $bom_degree_info = $bom_degree->toArray();

                                $bom_degree_info["product_id"] = $new_product->id;
                                $bom_degree_info["bill_of_material_item_id"] = $new_bom_item->id;
                                BOMDegree::create($bom_degree_info);
                            }

                            // BOM Replace
                            $list_replace = BOMReplace::
                            where([
                                "product_id" => $product->id,
                                "bill_of_material_item_id" => $bom_item->id
                            ])->
                            get();

                            foreach ($list_replace as $bom_replace) {
                                $bom_replace_info = $bom_replace->toArray();
                                $bom_replace_info["bill_of_material_item_id"] = $new_bom_item->id;
                                $bom_replace_info["bill_of_material_id"] = $new_bom->id;
                                $bom_replace_info["product_id"] = $new_product->id;
                                BOMReplace::create($bom_replace_info);
                            }
                            // BOM Fault Illegal
                            $list_fault_illegal = Product\BOM\BOMFaultIllegal::
                            where([
                                "product_id" => $product->id,
                                "bill_of_material_item_id" => $bom_item->id
                            ])->
                            get();

                            foreach ($list_fault_illegal as $fault_illegal) {
                                $bom_fault_illegal = $fault_illegal->toArray();
                                $bom_fault_illegal["bill_of_material_item_id"] = $new_bom_item->id;
                                $bom_fault_illegal["bill_of_material_id"] = $new_bom->id;
                                $bom_fault_illegal["product_id"] = $new_product->id;
                                Product\BOM\BOMFaultIllegal::create($bom_fault_illegal);
                            }

                            // Material Flow
                            if ($request->copy_material_flow) {
                                $list_material_flow = Product\MaterialFlow::
                                where([
                                    "product_id" => $product->id,
                                    "bill_of_material_item_id" => $bom_item->id
                                ])->
                                get();
                                foreach ($list_material_flow as $material_flow) {
                                    $material_flow_info = $material_flow->toArray();
                                    $material_flow_info["bill_of_material_item_id"] = $new_bom_item->id;
                                    $material_flow_info["bill_of_material_id"] = $new_bom->id;
                                    $material_flow_info["product_id"] = $new_product->id;
                                    Product\MaterialFlow::create($material_flow_info);
                                }
                            }


                        }

                        // ایجاد کالاهای جایگزنی تولید
                        Product\BOM\BOMPermutation::CreateBOMMood($new_bom);
                    }
                    // کالاهای جایگزنین هم باید تغییر کنند.
                    Product\BOM\BOMItem::where([
                        "product_id" => $new_product->id,
                        "dependent_on_material_id" => $product->id
                    ])->
                    update([
                        "dependent_on_material_id" => $new_product->id
                    ]);
                }

            }
        }

        // ضایعات
        if ($request->copy_waste) {
            $wast_list = Product\Waste\ProductWaste::where("product_id", $product->id)->whereNull("product_route_id")->get();
            foreach ($wast_list as $waste_item) {
                $waste_item = $waste_item->toArray();
                $waste_item["product_id"] = $new_product->id;
                $waste_item["product_route_id"] = null;
                Product\Waste\ProductWaste::create($waste_item);
            }
        }
        // لات
        if ($request->copy_lot_number) {
            $lot_list = LotNumber::where("product_id", $product->id)->select("product_id", "code")->get();
            foreach ($lot_list as $lot_item) {
                $lot_item = $lot_item->toArray();
                $lot_item["product_id"] = $new_product->id;
                $lot_item["user_id"] = Auth::id();
                $lot_item["machine_id"] = 0;
                LotNumber::create($lot_item);
            }
        }
        // انبارش کالا
        if ($request->copy_warehouse) {
            $list_warehouse = ProductWarehouseStorageType::where([
                "product_id" => $product->id,
            ])->get();
            foreach ($list_warehouse as $warehouse_item) {
                $warehouse_item = $warehouse_item->toArray();
                $warehouse_item["product_id"] = $new_product->id;
                ProductWarehouseStorageType::create($warehouse_item);
            }
        }
        if (isset($request->copy_warehouse) && $request->copy_warehouse == 0) {
            $new_product->default_packing_type_id = null;
            $new_product->min_inventory = 0;
            $new_product->max_inventory = 1;
            $new_product->save();
        }
        // Replace Product
        if ($request->copy_product_replace) {
            $replace_product_list = ReplaceProduct::where([
                "product_id" => $product->id
            ])->get();

            foreach ($replace_product_list as $replace) {
                $replace_info = $replace->toArray();
                $replace_info["product_id"] = $new_product->id;
                ReplaceProduct::create($replace_info);
            }
        }

        // MachineProductPropertyValue
        if ($request->copy_route && $request->copy_route_property) {
            $property_value_list = MachineProductPropertyValue::where("product_id", $product->id)->get();
            foreach ($property_value_list as $item) {
                $property_value = $item->toArray();
                $property_value["product_id"] = $new_product->id;
                MachineProductPropertyValue::create($property_value);
            }
        }

        // ProductPackingType
        if ($request->copy_packing_type) {
            $packing_type_list = Product\ProductPackingType::where("product_id", $product->id)->get();
            foreach ($packing_type_list as $item) {
                $packing_type_info = $item->toArray();
                $packing_type_info["product_id"] = $new_product->id;
                Product\ProductPackingType::create($packing_type_info);
            }
        }

        return $new_product;
    }

    public function print(Product $product)
    {
        $worker = Worker::find(Auth::id());
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = route("DCPR_QR", [$product]); // DC Product

        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);


        $packing_type_label_printing_type = PackingTypeLabelPrintingType::find(2);

        $qr = QrCode::size($packing_type_label_printing_type->qr_size)->generate($url);


        $software_name = Setting::getStringValue("software_name");
        $view_path = "line_product_station.product.print.";
        $html = [];
        $html[0] = view($view_path . "_head")->render();
        $html[0] .= view($view_path . "_print_info", compact("product", "software_name", "qr"))->render() . $html[0];
        $html[0] .= view($view_path . "_footer")->render();


        $print_file = PrinterFile::create([
            "user_id" => $worker->id,
            "filename" => "product" . $product->id . ".pdf",
            "status_id" => 305001, // در انتظار دانلود
            "is_landscape" => $packing_type_label_printing_type->orientation == "L" ? 1 : 0,
            "printer_id" => $worker->default_label_printer_id
        ]);
        Pdf::labelPrinter($html,
            $packing_type_label_printing_type->orientation,
            "product" . $product->id, [
                $packing_type_label_printing_type->width,
                $packing_type_label_printing_type->long
            ],
            $print_file
        );

        return back()->with(["success" => "برای دریافت کارت کالا به محل لیبل پرینتر " . $worker->default_label_printer_id . " مراجعه فرمایید."]);

    }

}
