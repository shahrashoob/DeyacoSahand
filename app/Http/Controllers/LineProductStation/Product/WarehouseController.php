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

class WarehouseController extends Controller
{
    public $route_path = "line_product_station.product.warehouse.";
    public $view_path = "line_product_station.product.warehouse.";
    public $dashboard_route = "line_product_station.product.index";

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path, $this->route_path, null);
    }

    public function submit(Request $request, Product $product)
    {
        $result = self::PostSubmit($request, $product);
        if ($result["result"]) {
            return redirect()->route("line_product_station.product.classification.index", $product)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }

    public function warehouse_shelving(Product $product)
    {
        return self::WarehouseShelving($product,$this->view_path, $this->route_path,$this->dashboard_route, null);
    }

    public static function GetIndex(Product $product, $view_path, $route_path, $product_creation_process)
    {
        $warehouse_storage_types = WarehouseStorageType::all();
        $product_reservoir = $product->product_reservoirs()->
        pluck("reservoir_id", "reservoir_id")->
        toArray();
        $product_warehouse_storage_type = Product\ProductWarehouseStorageType::getValuesArray($product);
        $reservoir_list = Reservoir::where("active_status_id", 1200)->get();

        // لیست انبارهایی که قفسه بندی دارند
        $warehouse_ids_where_have_shelving = WarehouseShelving::pluck("warehouse_id", "warehouse_id")->toArray();
        $warehouse_list = Warehouse::whereIn("id", $warehouse_ids_where_have_shelving)->get();

        //همه جایگاه های خاص را لیست می کنیم.
        $list_shelving["warehouse_shelving_ids"] = WarehouseShelvingProduct::
        where("product_id", $product->id)->
        whereNull("warehouse_shelving_type_id")->
        pluck("warehouse_shelving_id")->
        toArray();

        $warehouse_shelving_types = WarehouseShelvingProduct::
        where("product_id", $product->id)->
        whereNull("warehouse_shelving_id")->
        select("warehouse_shelving_type_id", "warehouse_id")->
        get();
        foreach ($warehouse_shelving_types as $warehouse_shelving_type) {
            $list_shelving["warehouse_shelving_type_ids"][$warehouse_shelving_type->warehouse_id][] = $warehouse_shelving_type->warehouse_shelving_type_id;
        }

        $list_shelving["warehouse_ids_where_have_shelving"] = $warehouse_ids_where_have_shelving;


        $warehouse_shelving_option = Option::get("warehouse_shelving_option_list", 0, 0, $list_shelving);
        $warehouse_shelving_type_option = Option::get("warehouse_shelving_option_type_list", 0, 0, $list_shelving);

        $packing_type_option=Option::get("packing_type", $product->default_packing_type_id,$product->goods_kind_id);


        return view($view_path . "index", compact(
                "product",
                "product_creation_process",
                "warehouse_storage_types",
                "reservoir_list", "product_reservoir",
                "view_path", "route_path", 'product_warehouse_storage_type',"packing_type_option", "warehouse_shelving_type_option", 'warehouse_shelving_option', "warehouse_ids_where_have_shelving", "warehouse_list"
            )
        );
    }

    public static function PostSubmit(Request $request, Product $product)
    {
        $warehouse_storage_types = WarehouseStorageType::all();

        $warehouse_storage_types_ids = [];
        foreach ($warehouse_storage_types as $warehouse_storage_type) {

            $id = "warehouse_storage_type_" . $warehouse_storage_type->id;

            if (!isset($request->$id)) {
                continue;
            }

            if (!$request->reservoir && $warehouse_storage_type->id == 3) {
                return [
                    "result" => false,
                    "error" => "لطفا حداقل یک مخرن را انتخاب نمایید."
                ];
            }

            $warehouse_storage_types_ids[] = $warehouse_storage_type->id;
        }

        if (count($warehouse_storage_types_ids) == 0) {
            return [
                "result" => false,
                "message" => "لطفا حداقل یک نوع انبارش را انتخاب نمایید."
            ];
        }


        $product->product_reservoirs()->delete();
        $product->product_warehouse_storage_type()->delete();
        // بروز رسانی انواع انبارش
        foreach ($warehouse_storage_types as $warehouse_storage_type) {

            $id = "warehouse_storage_type_" . $warehouse_storage_type->id;

            if (!isset($request->$id)) {
                continue;
            }

            ProductWarehouseStorageType::create([
                "product_id" => $product->id,
                "warehouse_storage_type_id" => $warehouse_storage_type->id
            ]);

            switch ($warehouse_storage_type->id) {
                case 1:
                case 2:
                case 4:

                    break;
                case 3: // انبارش با مخزن

                    foreach ($request->reservoir as $reservoir_id => $value) {
                        Product\ProductReservoir::create([
                            "product_id" => $product->id,
                            "reservoir_id" => $reservoir_id
                        ]);
                    }
                    break;
            }
        }


        $product->warehouse_storage_type_id = $warehouse_storage_types_ids[0];
        $product->save();

        $product->update($request->all());

        // بروز رسانی قفسه های مجاز کالا

        // لیست انبارهایی که قفسه بندی دارند
        $warehouse_ids_where_have_shelving = WarehouseShelving::pluck("warehouse_id", "warehouse_id")->toArray();

        $list_for_add_warehouse_shelving_id = [];
        foreach ($warehouse_ids_where_have_shelving as $warehouse_id) {
            $key = "warehouse" . $warehouse_id . "_shelving_ids";

            if ($request->$key && count($request->$key) > 0) {
                foreach ($request->$key as $warehouse_shelving_id) {
                    $list_for_add_warehouse_shelving_id[$warehouse_id][] = $warehouse_shelving_id + 0;
                }
            }
        }


        $list_for_add_warehouse_shelving_type_id = [];
        foreach ($warehouse_ids_where_have_shelving as $warehouse_id) {
            $key = "warehouse" . $warehouse_id . "_shelving_type_ids";

            if ($request->$key && count($request->$key) > 0) {
                foreach ($request->$key as $warehouse_shelving_type_id) {
                    $list_for_add_warehouse_shelving_type_id[$warehouse_id][] = $warehouse_shelving_type_id + 0;
                }
            }
        }


        WarehouseShelvingProduct::where("product_id", $product->id)->delete();

        if ($request->product_have_specific_location) {
            // افزود لیست محل های خاص
            if (count($list_for_add_warehouse_shelving_id) > 0) {
                $list = [];
                foreach ($list_for_add_warehouse_shelving_id as $warehouse_id => $warehouse_shelving_ids) {
                    foreach ($warehouse_shelving_ids as $warehouse_shelving_id) {
                        $list[] = ["product_id" => $product->id, "warehouse_shelving_id" => $warehouse_shelving_id, "warehouse_id" => $warehouse_id];
                    }
                }
                WarehouseShelvingProduct::insert($list);
            }


            // لیست همه سایت ها و ردیف های مجاز کالا د انبار
            if (count($list_for_add_warehouse_shelving_type_id) > 0) {
                $list = [];
                foreach ($list_for_add_warehouse_shelving_type_id as $warehouse_id => $warehouse_shelving_type_ids) {
                    foreach ($warehouse_shelving_type_ids as $warehouse_shelving_type_id) {
                        $list[] = ["product_id" => $product->id, "warehouse_shelving_type_id" => $warehouse_shelving_type_id, "warehouse_id" => $warehouse_id];
                    }
                }

                WarehouseShelvingProduct::insert($list);
            }
        }


        return [
            "result" => true,
            "message" => "اطلاعات با موفقیت ذخیره شد"
        ];


    }

    public static function WarehouseShelving(Product $product, $view_path, $route_path,$dashboard_route, $product_creation_process)
    {
        $warehouse_storage_types = WarehouseStorageType::all();
        $product_reservoir = $product->product_reservoirs()->
        pluck("reservoir_id", "reservoir_id")->
        toArray();
        $product_warehouse_storage_type = Product\ProductWarehouseStorageType::getValuesArray($product);
        $reservoir_list = Reservoir::where("active_status_id", 1200)->get();

        // لیست انبارهایی که قفسه بندی دارند
        $warehouse_ids_where_have_shelving = WarehouseShelving::pluck("warehouse_id", "warehouse_id")->toArray();
        $warehouse_list = Warehouse::whereIn("id", $warehouse_ids_where_have_shelving)->get();

        //همه جایگاه های خاص را لیست می کنیم.
        $list_shelving["warehouse_shelving_ids"] = WarehouseShelvingProduct::
        where("product_id", $product->id)->
        whereNull("warehouse_shelving_type_id")->
        pluck("warehouse_shelving_id")->
        toArray();

        $warehouse_shelving_types = WarehouseShelvingProduct::
        where("product_id", $product->id)->
        whereNull("warehouse_shelving_id")->
        select("warehouse_shelving_type_id", "warehouse_id")->
        get();
        foreach ($warehouse_shelving_types as $warehouse_shelving_type) {
            $list_shelving["warehouse_shelving_type_ids"][$warehouse_shelving_type->warehouse_id][] = $warehouse_shelving_type->warehouse_shelving_type_id;
        }

        $list_shelving["warehouse_ids_where_have_shelving"] = $warehouse_ids_where_have_shelving;


        $warehouse_shelving_option = Option::get("warehouse_shelving_option_list", 0, 0, $list_shelving);
        $warehouse_shelving_type_option = Option::get("warehouse_shelving_option_type_list", 0, 0, $list_shelving);

        $packing_forms = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_item.packing_form_id")->
        where("product_id", $product->id)->
        whereNotNull("warehouse_shelving_id")->
        paginate(30);

        $product_reservoirs = Product\ProductReservoir::
        where("product_id", $product->id)->paginate(30);

        return view($view_path . "warehouse_shelving_by_product",compact("view_path","route_path","product_creation_process",
            "warehouse_shelving_option","warehouse_shelving_type_option","packing_forms",
            "product_reservoirs","product","warehouse_list","dashboard_route"));
    }
}
