<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind\GoodsKindSettingValue;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\StationOperation;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ProductStationController extends Controller
{

    private $route_path = "line_product_station.product.product_station.";
    private $view_path = "line_product_station.product.product_station.";

    public function create(Product $product, Product\ProductRoute $product_route, $product_creation_process = null)
    {

        $list_default = GoodsKindSettingValue::getArrayValue($product->goods_kind_id, "default_line_ids");
        $line_option = Option::get("line", 0, 0, $list_default);
        $station_option = Option::get("station");
        $station_operation_option = Option::get("station_operation");
        $machine_type_option = Option::get("machine_type");

        $contractor_option = Option::get("contractor");
        $contractor_operation_option = Option::get("contractor_operation");
        $status_option = Option::get("status", 0, 1100);
        $production_channel_type_option = Option::get("machine_type_production_channel_type", 0, -1);
        $contractor_channel_type_option=Option::get("contractor_production_channel_type", 0, -1);
        $station_sub_operation_option = Option::get("station_sub_operation_bom", 0, 0);
        $station_operation_list = StationOperation::pluck("station_operation_type_id", "id")->toArray();
        $supplier_option = Option::get("supplier");
        $customer_option = Option::get("customer");
        $applicant_warehouse_option = Option::get("warehouse",0,0,[1]);
        $line_product_start_status_option = Option::get("status", 3410001, 3410);
        $production_methods_option = Option::get("production_methods", 0);


        $material_is_null = Product\ConsumedProduct\ConsumedProduct::where("product_id", $product->id)->whereNull("material_id")->first();

        if ($material_is_null) {
            return back()->withErrors("با توجه به اینکه درخواست طراحی کالا (" . $material_is_null->product_creation_process->code . ") تکمیل نشده است، امکان ثبت مسیر محصول وجود ندارد.");
        }

        $material_id_dependent_to_batch_option = Option::get("consumed_product", 0, $product->id);
        $material_unit_type_id_dependent_to_batch_option = Option::get("unit_type", 0, 0);;


        // بسته بندی های مجاز کالا در زمانی که وابسته به بچ است.
        $material_packing_type_id_dependent_to_batch_option = Option::get("packing_type_product", 1, 0);

        //  شامل سوالات شروع عملیات و پایان عملیات در تعریف مسیر محصول کالا است؟
        $include_questions_about_start_and_end_of_operation =
            MachineModuleType::join("machine_types", "machine_module_types.id", "machine_module_type_id")->
            pluck("include_questions_about_start_and_end_of_operation", "machine_types.id")->
            toArray();
        $include_questions_about_start_and_end_of_operation[0] = 0;

        return view("line_product_station.product.product_station.create",
            compact("product_route",
                "machine_type_option",
                "status_option",
                "station_operation_option",
                "product", "line_option",
                "station_option", "contractor_option", "contractor_operation_option",
                "production_channel_type_option",
                "contractor_channel_type_option",
                "station_sub_operation_option",
                "station_operation_list",
                "supplier_option",
                "product_creation_process",
                "material_id_dependent_to_batch_option",
                "material_unit_type_id_dependent_to_batch_option",
                "material_packing_type_id_dependent_to_batch_option",
                "customer_option", "include_questions_about_start_and_end_of_operation",
            "line_product_start_status_option","applicant_warehouse_option","production_methods_option"
            )
        );

    }

    public function store(Request $request, Product $product, Product\ProductRoute $product_route, $product_creation_process = null)
    {
        $exists_line_product_station=LineProductStation::exist(
            null,
            $product->id,
            $product_route->id,
            $product->supply_type_id,
            $request->line_id,
            $request->station_id,
            $request->machine_type_id,
            $request->station_operation_id,
            $request->contractor_operation_id,
            $request->station_sub_operation_id,
            $request->supplier_id,
            $request->customer_id
        );
        if ($exists_line_product_station
        ) {
            return back()->withErrors("مسیر - محصول مشابه با نام ".($exists_line_product_station->route->caption??"---")."  وجود دارد");
        }

        $priority_number_line_product_station = LineProductStation::where([
            "product_id" => $product->id,
            "product_route_id" => $product_route->id,
            "priority_number" => $request->priority_number
        ])->first();
        if ($priority_number_line_product_station) {
            return back()->withErrors("اولویت " . $request->priority_number . " در مسیر محصول تکراری می باشد.");
        }

        $result = $this->checkProductionChannel($product, $request->production_channel_type_id, $request->machine_type_id, $request->station_operation_id, null, $request->contractor_id, $request->contractor_operation_id);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $request["product_route_id"] = $product_route->id;
        $request["product_id"] = $product_route->product_id;

        if ($request->material_id_dependent_to_batch) {
            $material_id_dependent_to_batch = Product::find($request->material_id_dependent_to_batch);
            if (!$material_id_dependent_to_batch) {
                return back()->withErrors("لطفا مواد اولیه وابسته به بچ را انتخاب نمایید.");
            }

            if ($request->material_unit_type_id_dependent_to_batch == 2 && !$material_id_dependent_to_batch->sub_unit) {
                return back()->withErrors("واحد مواد اولیه که مقدار آن در بچ محاسبه می شود، به درستی انتخاب نشده است.");
            }

            if ($request->material_unit_type_id_dependent_to_batch == 4 && !$request->material_packing_type_id_dependent_to_batch) {
                return back()->withErrors("با توجه به اینکه واحد مواد اولیه وابسته به بچ تعداد بسته بندی است، لطفا نوع بسته بندی مجاز را انتخاب نمایید");
            }
        }

        LineProductStation::create($request->all());

        if ($product_creation_process) {
            return redirect()->route("line_product_station.product.product_creation.route.index", $product_creation_process)->with(["success" => "یک مسیر - محصول با موفقیت اضافه شد."]);

        } else {
            return redirect()->route("line_product_station.product.route.index", $product)->with(["success" => "یک مسیر - محصول با موفقیت اضافه شد."]);
        }
    }

    public function edit(Product $product, LineProductStation $line_product_station, $product_creation_process = null)
    {

        $list_default = GoodsKindSettingValue::getArrayValue($product->goods_kind_id, "default_line_ids");

        $line_option = Option::get("line", $line_product_station->line_id, 0, $list_default);
        $station_option = Option::get("station", $line_product_station->station_id, $line_product_station->line_id);
        $station_operation_option = Option::get("station_operation", $line_product_station->station_operation_id, $line_product_station->station_id);
        $machine_type_option = Option::get("machine_type", $line_product_station->machine_type_id, $line_product_station->station_id);
        $status_option = Option::get("status", $line_product_station->status_id, 1100);
        $contractor_option = Option::get("contractor", $line_product_station->contractor_id);
        $contractor_operation_option = Option::get("contractor_operation", $line_product_station->contractor_operation_id, $line_product_station->contractor_id);

        $production_channel_type_option = Option::get("machine_type_production_channel_type", $line_product_station->production_channel_type_id, $line_product_station->machine_type_id);
        $contractor_channel_type_option=Option::get("contractor_production_channel_type", $line_product_station->production_channel_type_id,  $line_product_station->contractor_id);

        $station_sub_operation_option = Option::get("station_sub_operation_bom", $line_product_station->station_sub_operation_id, $line_product_station->station_operation_id);
        $product_route = $line_product_station->route;
        $supplier_option = Option::get("supplier", $line_product_station->supplier_id);
        $customer_option = Option::get("customer", $line_product_station->customer_id);
        $station_operation_list = StationOperation::pluck("station_operation_type_id", "id")->toArray();
        $line_product_start_status_option = Option::get("status", $line_product_station->line_product_start_status_id, 3410);

        $applicant_warehouse_option = Option::get("warehouse",$line_product_station->applicant_warehouse_id,0,[1]);

        $material_id_dependent_to_batch_option = Option::get("consumed_product", $line_product_station->material_id_dependent_to_batch, $product->id);
        $material_unit_type_id_dependent_to_batch_option = Option::get("unit_type", $line_product_station->material_unit_type_id_dependent_to_batch, 0, [1, 2, 4]);;
        $material_packing_type_id_dependent_to_batch_option = Option::get("packing_type_product", $line_product_station->material_packing_type_id_dependent_to_batch, $line_product_station->material_id_dependent_to_batch);
        $production_methods_option = Option::get("production_methods", $line_product_station->production_method_id);

        //  شامل سوالات شروع عملیات و پایان عملیات در تعریف مسیر محصول کالا است؟
        $include_questions_about_start_and_end_of_operation =
            MachineModuleType::join("machine_types", "machine_module_types.id", "machine_module_type_id")->
            pluck("include_questions_about_start_and_end_of_operation", "machine_types.id")->
            toArray();
        $include_questions_about_start_and_end_of_operation[0] = 0;


        return view("line_product_station.product.product_station.edit",
            compact(
                "line_product_station",
                "machine_type_option",
                "status_option",
                "station_operation_option",
                "station_sub_operation_option",
                "product",
                "line_option",
                "station_option",
                "contractor_option",
                "contractor_operation_option",
                "product_route",
                "production_channel_type_option",
                "contractor_channel_type_option",
                "station_operation_list",
                "supplier_option",
                "product_creation_process",
                "material_unit_type_id_dependent_to_batch_option",
                "material_packing_type_id_dependent_to_batch_option",
                "material_id_dependent_to_batch_option",
                "customer_option",
                "line_product_start_status_option","production_methods_option",
                "include_questions_about_start_and_end_of_operation","applicant_warehouse_option"
            )
        );
    }

    public function update(Request $request, Product $product, LineProductStation $line_product_station, $product_creation_process = null)
    {

        if (LineProductStation::exist(
            $line_product_station->id,
            $product->id,
            $line_product_station->route->id,
            $product->supply_type_id,
            $request->line_id,
            $request->station_id,
            $request->machine_type_id,
            $request->station_operation_id,
            $request->contractor_operation_id,
            $request->station_sub_operation_id,
            $request->supplier_id,
            $request->customer_id
        )
        ) {
            return back()->withErrors("مسیر - محصول مشابه وجود دارد");
        }

        $result = $this->checkProductionChannel($product, $request->production_channel_type_id, $request->machine_type_id, $request->station_operation_id, $line_product_station, $request->contractor_id, $request->contractor_operation_id,true);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        if ($request->material_id_dependent_to_batch) {
            $material_id_dependent_to_batch = Product::find($request->material_id_dependent_to_batch);
            if (!$material_id_dependent_to_batch) {
                return back()->withErrors("لطفا مواد اولیه وابسته به بچ را انتخاب نمایید.");
            }
            if ($request->material_unit_type_id_dependent_to_batch == 2 && !$material_id_dependent_to_batch->sub_unit) {
                return back()->withErrors("واحد مواد اولیه که مقدار آن در بچ محاسبه می شود، به درستی انتخاب نشده است.");
            }


            if ($request->material_unit_type_id_dependent_to_batch == 4 && !$request->material_packing_type_id_dependent_to_batch) {
                return back()->withErrors("با توجه به اینکه واحد مواد اولیه وابسته به بچ تعداد بسته بندی است، لطفا نوع بسته بندی مجاز را انتخاب نمایید");
            }
        }


        // تنظمات مربوط به بچ
        // اگر بچ نبود، همه اطلاعات بچ حذف می شود

        if (!$request->batch) {
            $request["batch"] = null;
            $request["material_id_dependent_to_batch"] = null;
            $request["material_unit_type_id_dependent_to_batch"] = null;
            $request["material_packing_type_id_dependent_to_batch"] = null;
            $request["batch_error_percentage"] = null;

        }
// اگر واحد وابسته به بچ تعداد بسته بندی نبود، نوع بسته بندی را حذف می کند
        if ($request["material_unit_type_id_dependent_to_batch"] && $request["material_unit_type_id_dependent_to_batch"] != 4) {

            $request["material_packing_type_id_dependent_to_batch"] = null;
        }
//return $request["material_unit_type_id_dependent_to_batch"];

        $line_product_station->update($request->all());

        if ($product_creation_process) {
            return redirect()->route("line_product_station.product.product_creation.route.index", $product_creation_process)->with(["success" => "اطلاعات مسیر - محصول با موفقیت ذخیره شد."]);

        } else {
            return redirect()->route("line_product_station.product.route.index", $product)->with(["success" => "اطلاعات مسیر - محصول با موفقیت ذخیره شد."]);
        }
    }

    public function destroy(Request $request, Product $product, LineProductStation $line_product_station, $product_creation_process = null)
    {

        $line_product_station->delete();
        if ($product_creation_process) {
            return redirect()->route("line_product_station.product.product_creation.route.index", $product_creation_process)->with(["success" => "یک مسیر - محصول با موفقیت حذف شد."]);

        } else {
            return redirect()->route("line_product_station.product.route.index", $product)->with(["success" => "یک مسیر - محصول با موفقیت حذف شد."]);
        }

    }

    public function checkProductionChannel($product, $production_channel_type_id, $machine_type_id, $station_operation_id, $line_product_station_current = null, $contractor_id = null, $contractor_operation_id = null,$is_edit=false)
    {
        if (in_array($product->supply_type_id, [4, 2])) {
            return ["result" => true]; // خرید لازم نیست که نوع عملیات مشخص گردد.
        }
        if ($contractor_id) {
            // عملیات پیمانکاری
            $line_product_station =
                LineProductStation::
                where([
                    "product_id" => $product->id,
                    "contractor_id" => $contractor_id,
                ])->
                where("contractor_operation_id", "!=", $contractor_operation_id ?? 0)->
                where("id", "!=", $line_product_station_current->id ?? 0)->
                first();

            if ($line_product_station) {
                return [
                    "result" => false,
                    "error" => "نوع عملیات - پیمانکار در مسیر محصول تکراری می باشد."
                ];
            }

            return ["result" => true];

        }

        $station_operation = StationOperation::find($station_operation_id);
        if (!$station_operation) {
            return [
                "result" => false,
                "error" => "نوع عملیات مشخص نشده است."
            ];
        }

        if (!$station_operation->station_operation_category_id) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه دسته عملیات برای " . $station_operation->caption . " مشخص نشده است، امکان ثبت مسیر محصول وجود ندارد، لطفا با واحد پشتیبانی تماس بگیرید."
            ];
        }

        // عملیات هایی که در یک دسته عملیات هستند را استخراج می کنیم.
        $station_operation_ids = StationOperation::
        where("station_operation_category_id", $station_operation->station_operation_category_id)->
        pluck("id")->
        toArray();
        $station_operation_ids[] = -1;

        $line_product_station =
            LineProductStation::
            where([
                "product_id" => $product->id,
                "machine_type_id" => $machine_type_id,
            ])->
            whereIn("station_operation_id", $station_operation_ids)->
            where("id", "!=", $line_product_station_current->id ?? 0)->
            where("production_channel_type_id", ">", 0)->
            first();


        if (!$line_product_station) {
            return ["result" => true];
        }

        $line_product_station_count =
            LineProductStation::
            leftJoin("station_operations", "station_operation_id", "station_operations.id")->
            where([
                "product_id" => $product->id,
                "machine_type_id" => $machine_type_id,
            ])->
            whereIn("station_operation_id", $station_operation_ids)->
            groupBy("production_channel_type_id")->
            where("production_channel_type_id", "!=", $production_channel_type_id)->
            select("line_product_station.*")->
            get();

// برای ویرایش چک نکند، چون دیگر نمی تواند کانال ها را ویرایش کند، تا کل کانال ها تغییر کنند.
        if (count($line_product_station_count) > 0 && !$is_edit) {
            return ["result" => false, "error" => "کانال تولید در مسیر محصول های کالا نمی تواند در یک گروه ماشین - عملیات (به ازای هر دسته عملیات) متفاوت باشد."];

        }

        return ["result" => true];

    }


}
