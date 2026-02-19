<?php

namespace App\Http\Controllers\LineProductStation\Product\BOM;

use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorOperation;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\MachineTypeInputBand;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMDegree;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\BOM\BOMPermutation;
use App\Models\LineProduct\Station;
use App\Models\LineProduct\StationOperation;
use App\Models\Utility\Option;
use App\Models\Utility\Unit;
use App\Models\Warehouse\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BOMItemController extends Controller
{
    var $view_path = "line_product_station.product.bom.bom_item.";
    var $route_path = "line_product_station.product.bom.bom_item.";

    public function create(BOM $bom, $product_creation_process = null)
    {


        $product_option = Option::get("consumed_product", 0, $bom->product_id);
        $warehouse_option = Option::get("warehouse_delivery", 0, 0, "[0,0]");
        $warehouse_consume_option = Option::get("warehouse_consume", 0, 0, "[0,0]");
        $station_option = Option::get("station_bom", 0, $bom->product_route->id);
        $station_operation_option = Option::get("station_operation", 0);
        $contractor_option = Option::get("contractor");
        $contractor_operation_option = Option::get("contractor_operation");
        $delivery_unit_option = Option::get("delivery_unit_bom", 0, 0);
        $station_sub_operation_option = Option::get("station_sub_operation_bom", 0, 0);
        $dependent_on_material_option = Option::get("dependent_on_material", 0, $bom->id);;
        $bill_of_material_entering_type_option = Option::get("bill_of_material_entering_type", 1, $bom->id);;
        $bill_of_material_dependency_type_option = Option::get("bill_of_material_dependency_type", 1);;

       $product_ids_no_warehouse= Product::whereIn("warehouse_storage_type_id",[1,3,4])->pluck("id")->toArray();

        // نوع انبارش مواد مصرفی
        $material_warehouse_storage_type = $bom->product->consumed_product()->join("products", "material_id", "products.id")->
        pluck("warehouse_storage_type_id", "material_id")->
        toArray();

        // لیست واحد های کالای اصلی که به کاربر نمایش داده می شود.
        $sub_unit_materials = $bom->product->consumed_product()->join("products", "material_id", "products.id")->
        selectRaw("sum(sub_unit_id) as sub_unit_id, sum(sub_unit2_id) as sub_unit_2_id")->
        first();
        $unit_for_main_list = [1];
        if (isset($bom->product->sub_unit_id) || $sub_unit_materials->sub_unit_id) {
            $unit_for_main_list[] = 2;
        }
        if (isset($bom->product->sub_unit2_id) || $sub_unit_materials->sub_unit_2_id) {
            $unit_for_main_list[] = 3;
        }
        $unit_type_main_option = Option::get("unit_type", 1, 0, $unit_for_main_list);;

        // لیست واحد های ماده اولیه که به کاربر نمایش داده می شود.
        $unit_for_main_list = [1];

        if ($sub_unit_materials->sub_unit_id) {
            $unit_for_main_list[] = 2;
        }
        if ($sub_unit_materials->sub_unit_2_id) {
            $unit_for_main_list[] = 3;
        }
        $unit_type_material_option = Option::get("unit_type", 1, 0, $unit_for_main_list);;


        $consume_goods_kind_ids = Product\ConsumedProduct\ConsumedProduct::
        join("products", "products.id", "material_id")->
        where("product_id", $bom->product_id)->
        pluck("products.goods_kind_id", "material_id");

        $structureBOMItem = BOM::getStructureBOMItem($bom);

        return view($this->view_path . "create", compact("delivery_unit_option", "contractor_option", "contractor_operation_option", "station_option", "warehouse_option", "product_option",
            "bom", "station_operation_option", "station_sub_operation_option", "warehouse_consume_option",
            "consume_goods_kind_ids", "product_creation_process", "dependent_on_material_option","bill_of_material_dependency_type_option",
            "unit_type_material_option", "bill_of_material_entering_type_option","product_ids_no_warehouse",
            "unit_type_main_option", "material_warehouse_storage_type", "structureBOMItem"
        ));


    }

    public function store(Request $request, BOM $bom, $product_creation_process = null)
    {


        $productive_consume_warehouse_id = $request->productive_consume_warehouse_id;
        $sampling_consume_warehouse_id = $request->sampling_consume_warehouse_id;
        $material = Product::find($request->material_id);
        if (!$material) {
            return back()->withErrors("لطفا ماده اولیه را انتخاب نمایید.");
        }
        if (in_array($material->warehouse_storage_type_id,[1,3,4]) ) {
            // کالا بدون انبارش است (مثل آب)

            // انبار مصرف
            $productive_consume_warehouse_type_id = 100; // بدون انبار
            $productive_consume_warehouse_id = null;

            // انبار مصرف نمونه گیری
            $sampling_consume_warehouse_type_id = 100; // بدون انبار
            $sampling_consume_warehouse_id = null;

            $warehouse_type_id = 100; // بدون انبار
            $warehouse_id = null;
        } else {
            // مواد مصرفی انبارش دارند و باید انبارهای مصرف چک شود.
            $warehouse_type_id = 1; // انبار کالا
            // به دست آوردن انبار مصرف کالای تولیدی
            if ($productive_consume_warehouse_id == -100) { // عددی که به صورت توافقی انتخاب کردیم در Option : warehouse_select_multiple
                $productive_consume_warehouse_type_id = 2;
                $productive_consume_warehouse_id = null;
            } else {
                $productive_consume_warehouse = Warehouse::where("id", $productive_consume_warehouse_id)->first();
                if (!$productive_consume_warehouse) {
                    return back()->withErrors("انبار مصرف کالای تولیدی نادرست است.");
                }

                $productive_consume_warehouse_type_id = $productive_consume_warehouse->warehouse_type_id;
            }

            // به دست آوردن انبار مصرف نمونه گیری
            if ($sampling_consume_warehouse_id == -100) { // عددی که به صورت توافقی انتخاب کردیم در Option : warehouse_select_multiple
                $sampling_consume_warehouse_type_id = 2;
                $sampling_consume_warehouse_id = null;
            } else {

                $sampling_consume_warehouse = Warehouse::where("id", $sampling_consume_warehouse_id)->first();
                if (!$sampling_consume_warehouse) {
                    return back()->withErrors("انبار مصرف کالای تولیدی نادرست است.");
                }

                $sampling_consume_warehouse_type_id = $sampling_consume_warehouse->warehouse_type_id;

            }
        }

        $request["warehouse_type_id"] = $warehouse_type_id;

        // اجازه تعریف چند انبارک مصرف برای یک آیتم BOM وجود ندارد.
        $bom_item_same_consume_warehouse = BOMItem::where([
            "bill_of_material_id" => $bom->id,
            "product_id" => $bom->product->id,
            "material_id" => $request->material_id
        ])->get();
        foreach ($bom_item_same_consume_warehouse as $item) {
            if (
                $item->productive_consume_warehouse_type_id != $productive_consume_warehouse_type_id ||
                $item->productive_consume_warehouse_id != $productive_consume_warehouse_id ||
                $item->sampling_consume_warehouse_type_id != $sampling_consume_warehouse_type_id ||
                $item->sampling_consume_warehouse_id != $sampling_consume_warehouse_id
            ) {
                return back()->withErrors("امکان تعریف چند انبار مصرف  برای یک ماده اولیه وجود ندارد.");
            }
            if ($item->warehouse_id != $request->warehouse_id) {
                return back()->withErrors("امکان تعریف چند انبار تحویل کالا برای یک ماده اولیه وجود ندارد.");
            }
        }

        // وابستگی ماده اولیه: اگر وابسته به کالای اصلی باشد، مقدار وابستگی را null قرار می دهیم.
        if ($request->dependent_on_material_id == $bom->product_id) {
            $request->dependent_on_material_id = null;
        }

        if ($request->dependent_on_material_id == $request->material_id) {
            return back()->withErrors(
                "یک ماده اولیه نمی تواند به خودش وابسته باشد، لطفا در فیلد وابستگی ماده اولیه،
             کالای اصلی یا یک ماده اولیه دیگری را انتخاب نمایید."
            );
        }
        $dependent_on_material = Product::find($request->dependent_on_material_id ?? 0);
        if ($dependent_on_material && !Unit::CheckUnitForProduct($dependent_on_material, $request->dependent_on_main_unit_type_id)) {
            $unit_type = Unit\UnitType::find($request->dependent_on_main_unit_type_id);
            return back()->withErrors(
                " با توجه به اینکه " . $unit_type->caption .
                " برای " . $dependent_on_material->caption .
                " تعریف نشده است، امکان انتخاب " . $unit_type->caption .
                " برای واحد مرجع کالای اصلی وجود ندارد."
            );
        }


        if (!Unit::CheckUnitForProduct($material, $request->dependent_on_material_unit_type_id)) {
            $unit_type = Unit\UnitType::find($request->dependent_on_material_unit_type_id);
            return back()->withErrors(
                " با توجه به اینکه " . $unit_type->caption .
                " برای " . $material->caption .
                " تعریف نشده است، امکان انتخاب " . $unit_type->caption .
                " برای واحد مرجع ماده اولیه اصلی وجود ندارد."
            );
        }


        if($request->bill_of_material_dependency_type_id == 2 && $request->station_operation_type_id==2){
            return back()->withErrors("نوع وابستگی به مسیر محصول فقط برای عملیات های بچی قابل قبول می باشد و در عملیات های پیوسته نامعتبر است.");
        }

        switch ($bom->product->supply_type_id) {
            case 1:

                $station = Station::find($request->station_id);
                $station_operation=StationOperation::find($request->station_operation_id);
                if (!$station_operation) {
                    return back()->withErrors("لطفا نوع عملیات در ایستگاه کاری را مشخص نمایید.");
                }
                if ((!$station || !$station_operation) && $bom->product->supply_type_id == 1) {
                    return back()->withErrors("عملیات به درستی انتخاب نشده است.");
                }
                $line_product_station = LineProductStation::where([
                    "product_id" => $bom->product->id,
                    "station_id" => $request->station_id,
                    "station_operation_id" => $request->station_operation_id,
                    "station_sub_operation_id" => $request->station_sub_operation_id
                ])->first();
                if (!$line_product_station) {
                    return back()->withErrors("مسیر محصول مرتبط برای کالا تعریف نشده است.");
                }
                $material = Product::find($request->material_id);
                $machine_type = $line_product_station->machine_type;
                $machine_type_input_band = MachineTypeInputBand::join("machine_type_input_band_goods_kind", "machine_type_input_band_id", "machine_type_input_bands.id")->
                where([
                    "goods_kind_id" => $material->goods_kind_id ?? 0,
                    "machine_type_id" => $machine_type->id ?? 0,
                ])->first();

                if (!$machine_type_input_band) {
                    return back()->withErrors("نوع رسته کالایی " . $material->goods_kind->caption . " برای ورودی های ماشین " . $machine_type->caption . " تعریف نشده است.");
                }
                $max_input_line = $machine_type_input_band->input_line_number;

                if (isset($request->material_id) && isset($request->amount)) {

                    $request["bill_of_material_id"] = $bom->id;
                    $request["product_id"] = $bom->product->id;

                    // به تعداد خط وروی که درخواست شده، ورودی های مشابه تولید می کند.
                    for (
                        $input_line_code = max(1, $request->input_line_code_from);
                        $input_line_code <= min($request->input_line_code_to, $max_input_line);
                        $input_line_code++
                    ) {
                        $bom_other = BOMItem::where([
                            "bill_of_material_id" => $bom->id,
                            "product_id" => $bom->product->id,
                            "material_id" => $request->material_id,
                            "station_id" => $request->station_id,
                            "station_operation_id" => $request->station_operation_id,
                            "station_sub_operation_id" => $request->station_sub_operation_id,
                            "input_line_code" => $input_line_code
                        ])->first();
                        if (!$bom_other) {
                            $request["input_line_code"] = $input_line_code;

                            $request["productive_consume_warehouse_id"] = $productive_consume_warehouse_id;
                            $request["productive_consume_warehouse_type_id"] = $productive_consume_warehouse_type_id;

                            $request["sampling_consume_warehouse_type_id"] = $sampling_consume_warehouse_type_id;
                            $request["sampling_consume_warehouse_id"] = $sampling_consume_warehouse_id;

                            $bom_item = BOMItem::create($request->all());
                        }
                    }

                    if (!isset($bom_item)) {
                        return back()->withErrors("هیچ ردیف BOM اضافه نشده، این خط ورودی قبلا تعریف شده است و یا شماره خط ورودی غیر مجاز است.");
                    }

                    session([
                        "input_line_code_from" => $request->input_line_code_from,
                        "input_line_code_to" => $request->input_line_code_to,
                    ]);

                    return redirect()->route("line_product_station.product.bom_degree.index", [
                        $bom->product->id,
                        $request->material_id,
                        $bom_item,
                        $product_creation_process
                    ])->with(["success" => "BOM با موفقیت اضافه شد، لطفا درجه های مجاز را اضافه کنید"]);

                }

                break;
            case 3:
                $contractor = Contractor::find($request->contractor_id);
                $contractor_operation = ContractorOperation::find($request->contractor_operation_id);

                if ((!$contractor || !$contractor_operation) && $bom->product->supply_type_id == 3) {
                    return back()->withErrors("عملیات به درستی انتخاب نشده است.");
                }
                if (isset($request->material_id) && isset($request->amount)) {

                    $request["bill_of_material_id"] = $bom->id;
                    $request["product_id"] = $bom->product->id;


                    $request["productive_consume_warehouse_id"] = $productive_consume_warehouse_id;
                    $request["productive_consume_warehouse_type_id"] = $productive_consume_warehouse_type_id;

                    $request["sampling_consume_warehouse_type_id"] = $sampling_consume_warehouse_type_id;
                    $request["sampling_consume_warehouse_id"] = $sampling_consume_warehouse_id;

                    $bom_item = BOMItem::create($request->all());

                    return redirect()->route("line_product_station.product.bom_degree.index", [
                        $bom->product->id,
                        $request->material_id,
                        $bom_item,
                        $product_creation_process
                    ])->with(["success" => "BOM با موفقیت اضافه شد، لطفا درجه های مجاز را اضافه کنید"]);

                }
                break;
            default:
                return back()->withErrors("نوع تامین کالا برای تعریف BOM نامتعتبر است.");
                break;
        }


        // بازسازی کالاهای جایگزین برای کالا
        Product\BOM\BOMPermutation::CreateBOMMood($bom);

        return back()->withErrors("عملیات افزودن با خطا مواجه شد");
    }


    public function edit(BOMItem $bom_item, $product_creation_process = null)
    {

        $bom = $bom_item->bom;
        $consume_goods_kind_ids = Product\ConsumedProduct\ConsumedProduct::
        join("products", "products.id", "material_id")->
        where("product_id", $bom_item->product_id)->
        where("material_id", $bom_item->material_id)->
        pluck("products.goods_kind_id", "material_id")->
        toArray();

        $product_option = Option::get("consumed_product", $bom_item->material_id, $bom->product_id);
//        $warehouse_option = Option::get("warehouse_delivery", $bom_item->warehouse_id, 0, json_encode(array_values($consume_goods_kind_ids)));
//        $warehouse_consume_option = Option::get("warehouse_consume", 0, 0, "[0,0]");
        $station_option = Option::get("station_bom", $bom_item->station_id, $bom->product_route->id);
        $station_operation_option = Option::get("station_operation", $bom_item->station_operation_id, $bom_item->station_id);
        $contractor_option = Option::get("contractor", $bom_item->contractor_id);
        $contractor_operation_option = Option::get("contractor_operation", $bom_item->contractor_operation_id);
        $delivery_unit_option = Option::get("delivery_unit_bom", 0, 0);
        $station_sub_operation_option = Option::get("station_sub_operation_bom", $bom_item->station_sub_operation_id, $bom_item->station_operation_id);
        $dependent_on_material_option = Option::get("dependent_on_material", $bom_item->dependent_on_material_id, $bom->id);;
        $bill_of_material_entering_type_option = Option::get("bill_of_material_entering_type", $bom_item->bill_of_material_entering_type_id, $bom->id);;
        $bill_of_material_dependency_type_option = Option::get("bill_of_material_dependency_type", $bom_item->bill_of_material_dependency_type_id);;

        $product_ids_no_warehouse= Product::whereIn("warehouse_storage_type_id",[1,3,4])->pluck("id")->toArray();
        // نوع انبارش مواد مصرفی
        $material_warehouse_storage_type = $bom->product->consumed_product()->join("products", "material_id", "products.id")->
        pluck("warehouse_storage_type_id", "material_id")->
        toArray();

        // لیست واحد های کالای اصلی که به کاربر نمایش داده می شود.
        $sub_unit_materials = $bom->product->consumed_product()->join("products", "material_id", "products.id")->
        selectRaw("sum(sub_unit_id) as sub_unit_id, sum(sub_unit2_id) as sub_unit_2_id")->
        first();
        $unit_for_main_list = [1];
        if (isset($bom->product->sub_unit_id) || $sub_unit_materials->sub_unit_id) {
            $unit_for_main_list[] = 2;
        }
        if (isset($bom->product->sub_unit2_id) || $sub_unit_materials->sub_unit_2_id) {
            $unit_for_main_list[] = 3;
        }
        $unit_type_main_option = Option::get("unit_type", $bom_item->dependent_on_main_unit_type_id, 0, $unit_for_main_list);;

        // لیست واحد های ماده اولیه که به کاربر نمایش داده می شود.
        $unit_for_main_list = [1];

        if ($sub_unit_materials->sub_unit_id) {
            $unit_for_main_list[] = 2;
        }
        if ($sub_unit_materials->sub_unit_2_id) {
            $unit_for_main_list[] = 3;
        }
        $unit_type_material_option = Option::get("unit_type", $bom_item->dependent_on_material_unit_type_id, 0, $unit_for_main_list);;


        $consume_goods_kind_ids = Product\ConsumedProduct\ConsumedProduct::
        join("products", "products.id", "material_id")->
        where("product_id", $bom->product_id)->
        pluck("products.goods_kind_id", "material_id");

        $structureBOMItem = BOM::getStructureBOMItem($bom);

        return view($this->view_path . "edit", compact(
            "delivery_unit_option", "contractor_option", "contractor_operation_option", "bom_item",
            "station_option", "product_option",
            "bom", "station_operation_option", "station_sub_operation_option",
            "consume_goods_kind_ids", "product_creation_process", "dependent_on_material_option",
            "unit_type_material_option", "bill_of_material_entering_type_option",
            "unit_type_main_option", "material_warehouse_storage_type", "structureBOMItem",
            "bill_of_material_dependency_type_option","product_ids_no_warehouse"
        ));


    }

    public function update(Request $request, BOM $bom, BOMItem $bom_item, $product_creation_process = null)
    {


        // وابستگی ماده اولیه: اگر وابسته به کالای اصلی باشد، مقدار وابستگی را null قرار می دهیم.
        if ($request->dependent_on_material_id == $bom->product_id) {
            $request->dependent_on_material_id = null;
        }

        $material = Product::find($request->material_id);
        if (!$material) {
            return back()->withErrors("لطفا ماده اولیه را انتخاب نمایید.");
        }
        if ($request->dependent_on_material_id == $request->material_id) {
            return back()->withErrors(
                "یک ماده اولیه نمی تواند به خودش وابسته باشد، لطفا در فیلد وابستگی ماده اولیه،
             کالای اصلی یا یک ماده اولیه دیگری را انتخاب نمایید."
            );
        }


        if($request->bill_of_material_dependency_type_id == 2 && $request->station_operation_type_id==2){
            return back()->withErrors("نوع وابستگی به مسیر محصول فقط برای عملیات های بچی قابل قبول می باشد و در عملیات های پیوسته نامعتبر است.");
        }

        $dependent_on_material = Product::find($request->dependent_on_material_id ?? 0);
        if ($dependent_on_material && !Unit::CheckUnitForProduct($dependent_on_material, $request->dependent_on_main_unit_type_id)) {
            $unit_type = Unit\UnitType::find($request->dependent_on_main_unit_type_id);
            return back()->withErrors(
                " با توجه به اینکه " . $unit_type->caption .
                " برای " . $dependent_on_material->caption .
                " تعریف نشده است، امکان انتخاب " . $unit_type->caption .
                " برای واحد مرجع کالای اصلی وجود ندارد."
            );
        }


        if (!Unit::CheckUnitForProduct($material, $request->dependent_on_material_unit_type_id)) {
            $unit_type = Unit\UnitType::find($request->dependent_on_material_unit_type_id);
            return back()->withErrors(
                " با توجه به اینکه " . $unit_type->caption .
                " برای " . $material->caption .
                " تعریف نشده است، امکان انتخاب " . $unit_type->caption .
                " برای واحد مرجع ماده اولیه اصلی وجود ندارد."
            );
        }

        $productive_consume_warehouse_id = $request->productive_consume_warehouse_id;
        $sampling_consume_warehouse_id = $request->sampling_consume_warehouse_id;
        $warehouse_id = $request->warehouse_id;

        if (in_array($material->warehouse_storage_type_id,[1,3,4]) ) {
            // کالا بدون انبارش است (مثل آب)

            // انبار مصرف
            $productive_consume_warehouse_type_id = 100; // بدون انبار
            $productive_consume_warehouse_id = null;

            // انبار مصرف نمونه گیری
            $sampling_consume_warehouse_type_id = 100; // بدون انبار
            $sampling_consume_warehouse_id = null;

            $warehouse_type_id = 100; // بدون انبار
            $warehouse_id = null;

            $request["warehouse_id"] = $warehouse_id;
            $request["productive_consume_warehouse_id"] = $productive_consume_warehouse_id;
            $request["productive_consume_warehouse_type_id"] = $productive_consume_warehouse_type_id;

            $request["sampling_consume_warehouse_type_id"] = $sampling_consume_warehouse_type_id;
            $request["sampling_consume_warehouse_id"] = $sampling_consume_warehouse_id;
            $request["warehouse_type_id"] = $warehouse_type_id;

        } else {
//            // مواد مصرفی انبارش دارند و باید انبارهای مصرف چک شود.
//            $warehouse_type_id = 1; // انبار کالا
//            // به دست آوردن انبار مصرف کالای تولیدی
//            if ($productive_consume_warehouse_id == -100) { // عددی که به صورت توافقی انتخاب کردیم در Option : warehouse_select_multiple
//                $productive_consume_warehouse_type_id = 2;
//                $productive_consume_warehouse_id = null;
//            } else {
//                $productive_consume_warehouse = Warehouse::where("id", $productive_consume_warehouse_id)->first();
//                if (!$productive_consume_warehouse) {
//                    return back()->withErrors("انبار مصرف کالای تولیدی نادرست است.");
//                }
//
//                $productive_consume_warehouse_type_id = $productive_consume_warehouse->warehouse_type_id;
//            }
//
//            // به دست آوردن انبار مصرف نمونه گیری
//            if ($sampling_consume_warehouse_id == -100) { // عددی که به صورت توافقی انتخاب کردیم در Option : warehouse_select_multiple
//                $sampling_consume_warehouse_type_id = 2;
//                $sampling_consume_warehouse_id = null;
//            } else {
//
//                $sampling_consume_warehouse = Warehouse::where("id", $sampling_consume_warehouse_id)->first();
//                if (!$sampling_consume_warehouse) {
//                    return back()->withErrors("انبار مصرف کالای تولیدی نادرست است.");
//                }
//
//                $sampling_consume_warehouse_type_id = $sampling_consume_warehouse->warehouse_type_id;
//
//            }
        }



        switch ($bom->product->supply_type_id) {
            case 1:
                $station = Station::find($request->station_id);
                $station_operation=StationOperation::find($request->station_operation_id);
                if (!$station_operation) {
                    return back()->withErrors("لطفا نوع عملیات در ایستگاه کاری را مشخص نمایید.");
                }

                if ((!$station || !$station_operation) && $bom->product->supply_type_id == 1) {
                    return back()->withErrors("عملیات به درستی انتخاب نشده است.");
                }
                $line_product_station = LineProductStation::where([
                    "product_id" => $bom->product->id,
                    "station_id" => $request->station_id,
                    "station_operation_id" => $request->station_operation_id,
                    "station_sub_operation_id" => $request->station_sub_operation_id
                ])->first();
                if (!$line_product_station) {
                    return back()->withErrors("مسیر محصول مرتبط برای کالا تعریف نشده است.");
                }
                $material = Product::find($request->material_id);
                $machine_type = $line_product_station->machine_type;
                $machine_type_input_band = MachineTypeInputBand::join("machine_type_input_band_goods_kind", "machine_type_input_band_id", "machine_type_input_bands.id")->
                where([
                    "goods_kind_id" => $material->goods_kind_id ?? 0,
                    "machine_type_id" => $machine_type->id ?? 0,
                ])->first();

                if (!$machine_type_input_band) {
                    return back()->withErrors("نوع رسته کالایی " . $material->goods_kind->caption . " برای ورودی های ماشین " . $machine_type->caption . " تعریف نشده است.");
                }

                if (isset($request->material_id) && isset($request->amount)) {

                    $bom_item->update($request->all());

// حذف درجه هایی که مربوط به کالای قبلی می باشد، اگر کالا در ویرایش اطلاعات عوض می شود.
                    BOMDegree::where([
                        "bill_of_material_item_id" => $bom_item->id,
                    ])->where("material_id", "!=", $bom_item->material_id)->delete();


                    // ذخیره لاگ BOM
                    Product\BOM\BOMLog::NewLog(BOM::find($bom->id),null,null,Auth::id());


                    return redirect()->route("line_product_station.product.bom_degree.index", [
                        $bom->product->id,
                        $request->material_id,
                        $bom_item,
                        $product_creation_process
                    ])->with(["success" => "BOM با موفقیت بروزرسانی شد، لطفا درجه های مجاز را اضافه کنید"]);

                }

                break;
            case 3:
                $contractor = Contractor::find($request->contractor_id);
                $contractor_operation = ContractorOperation::find($request->contractor_operation_id);

                if ((!$contractor || !$contractor_operation) && $bom->product->supply_type_id == 3) {
                    return back()->withErrors("عملیات به درستی انتخاب نشده است.");
                }
                if (isset($request->material_id) && isset($request->amount)) {


                    $bom_item->update($request->all());

                    // حذف درجه هایی که مربوط به کالای قبلی می باشد، اگر کالا در ویرایش اطلاعات عوض می شود.
                    BOMDegree::where([
                        "bill_of_material_item_id" => $bom_item->id,
                    ])->where("material_id", "!=", $bom_item->material_id)->delete();

                    // ذخیره لاگ BOM
                    Product\BOM\BOMLog::NewLog(BOM::find($bom->id),null,null,Auth::id());

                    return redirect()->route("line_product_station.product.bom_degree.index", [
                        $bom->product->id,
                        $request->material_id,
                        $bom_item,
                        $product_creation_process
                    ])->with(["success" => "BOM با موفقیت اضافه شد، لطفا درجه های مجاز را اضافه کنید"]);

                }
                break;
            default:
                return back()->withErrors("نوع تامین کالا برای تعریف BOM نامتعتبر است.");
                break;
        }




        // بازسازی کالاهای جایگزین برای کالا
        Product\BOM\BOMPermutation::CreateBOMMood($bom);

        return back()->withErrors("عملیات افزودن با خطا مواجه شد");
    }

    public function destroy(BOMItem $bom_item)
    {

        // حذف درجه
        Product\BOM\BOMDegree::where(["bill_of_material_item_id" => $bom_item->id])->delete();
        Product\MaterialFlow::where("bill_of_material_item_id", $bom_item->id)->delete();
        Product\BOM\BOMReplace::where("bill_of_material_item_id", $bom_item->id)->delete();
        Product\BOM\BOMFaultIllegal::where("bill_of_material_item_id", $bom_item->id)->delete();

        BOMPermutation::where([
            "bill_of_material_id" => $bom_item->bom->id,
            "product_id" => $bom_item->bom->product_id,
        ])->
        update(["active_status_id" => 1210]);

        $bom_item->delete();

        // بازسازی کالاهای جایگزین برای کالا
        Product\BOM\BOMPermutation::CreateBOMMood($bom_item->bom);

        return back()->with(["success" => "حذف با موفقیت انجام شد."]);
    }


}
