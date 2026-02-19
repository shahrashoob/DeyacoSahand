<?php

namespace App\Http\Controllers\LineProductStation\MachineType;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeOutputBand;
use App\Models\LineProduct\Machine\MachineTypeOutputBandGoodsKind;
use App\Models\LineProduct\Machine\MachineTypeOutputBandPackingType;
use App\Models\LineProduct\Machine\MachineTypeOutputBandWarehouse;
use App\Models\Utility\Option;
use App\Models\Warehouse\Warehouse;
use http\Exception\BadConversionException;
use Illuminate\Http\Request;

class OutputBandController extends Controller
{
    var $view_path = "line_product_station.machine_type.output_band.";
    var $route_path = "line_product_station.machine_type.output_band.";

    public function index(MachineType $machine_type)
    {
        return view($this->view_path . "index", compact("machine_type"));
    }

    public function create(MachineType $machine_type)
    {

        $goods_kind_option = Option::get("goods_kind");

        return view($this->view_path . "create", compact("machine_type", "goods_kind_option"));

    }

    public function store(Request $request, MachineType $machine_type)
    {

        if ($request->output_line_number < 0 || $request->output_line_number > 100000) {
            return back()->withErrors("تعداد خط باند خروجی نادرست است.");
        }
        $goods_kind = GoodsKind::find($request->goods_kind_id);
        if (!isset($goods_kind)) {
            return back()->withErrors("لطفا جنس کالا را انتخاب کنید.");
        }

        $output_band_count = MachineTypeOutputBand::where("machine_type_id", $machine_type->id)->count();


        $output_band = MachineTypeOutputBand::create([
            "machine_type_id" => $machine_type->id,
            "code" => $output_band_count + 1,
            "caption" => "باند خروجی " . ($output_band_count + 1),
            "active_status_id" => 1200,
            "output_line_number" => $request->output_line_number
        ]);

        MachineTypeOutputBandGoodsKind::create([
            "machine_type_output_band_id" => $output_band->id,
            "goods_kind_id" => $goods_kind->id
        ]);

        return redirect()->
        route($this->route_path . "index", $machine_type)->
        with(["success" => "یک باند باند خروجی جدید اضافه گردید."]);
    }

    public function edit(MachineType $machine_type, MachineTypeOutputBand $machine_type_output_band)
    {

        $status_option = Option::get("active_status", $machine_type_output_band->active_status_id);
         $algorithm_type_option=Option::get("algorithm",$machine_type_output_band->doff_algorithm_id,600,[],0);
        return view($this->view_path . "edit", compact("status_option","algorithm_type_option", "machine_type", "machine_type_output_band"));
    }

    public function update(Request $request, MachineTypeOutputBand $machine_type_output_band)
    {

        $machine_type_output_band->update($request->all());

        return redirect()->route($this->route_path . "index", $machine_type_output_band->machine_type_id)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    public function add_goods_kind(Request $request, MachineTypeOutputBand $machine_type_output_band)
    {
        $goods_kind_option = Option::get("goods_kind");

        $machine_type = $machine_type_output_band->machine_type;

        return view($this->view_path . "add_goods_kind", compact("machine_type", "machine_type_output_band", "goods_kind_option"));

    }

    public function add_goods_kind_submit_step1(Request $request, MachineTypeOutputBand $machine_type_output_band)
    {

        $goods_kind = GoodsKind::find($request->goods_kind_id);
        if (!isset($goods_kind)) {
            return redirect()->route($this->route_path . "index", $machine_type_output_band->machine_type_id)->
            withErrors("لطفا جنس کالا را انتخاب کنید.");
        }


        return redirect()->
        route($this->route_path . "add_goods_kind_step2", [
            $machine_type_output_band,
            $request->goods_kind_id
        ]);

    }

    public function add_goods_kind_step2(Request $request, MachineTypeOutputBand $machine_type_output_band, GoodsKind $goods_kind)
    {


        $exists = MachineTypeOutputBandGoodsKind::where([
            "machine_type_output_band_id" => $machine_type_output_band->id,
            "goods_kind_id" => $goods_kind->id,
        ])->exists();
        if ($exists) {
            return redirect()->route($this->route_path . "input_band.index", $machine_type_output_band->machine_type_id)->
            withErrors("رسته کالایی مورد نظر قبلا انتخاب شده است.");
        }
        if ($goods_kind->packing_type()->count() == 0) {
            return back()->withErrors("برای رسته کالایی انتخاب شده، بسته بندی تعریف نشده است، لطفا با پشتیبانی تماس بگیرید.");
        }
        $machine_type = $machine_type_output_band->machine_type;

        return view($this->view_path . "add_goods_kind_step2", compact("machine_type", "machine_type_output_band", "goods_kind"));

    }

    public function add_goods_kind_submit_step2(Request $request, MachineTypeOutputBand $machine_type_output_band, GoodsKind $goods_kind)
    {

        $exists = MachineTypeOutputBandGoodsKind::where([
            "machine_type_output_band_id" => $machine_type_output_band->id,
            "goods_kind_id" => $goods_kind->id,
        ])->exists();
        if ($exists) {
            return redirect()->route($this->route_path . "index", $machine_type_output_band->machine_type_id)->
            withErrors("رسته کالایی مورد نظر قبلا انتخاب شده است.");
        }


        $data = $request["data"];;
        if (isset($data)) {

            MachineTypeOutputBandGoodsKind::firstOrCreate([
                "machine_type_output_band_id" => $machine_type_output_band->id,
                "goods_kind_id" => $goods_kind->id
            ]);

            foreach ($data as $goods_kind_id => $packing_type_list) {
                foreach ($packing_type_list as $packing_type_id => $value) {
                    MachineTypeOutputBandPackingType::create([
                        "machine_type_output_band_id" => $machine_type_output_band->id,
                        "goods_kind_id" => $goods_kind_id,
                        "machine_type_id" => $machine_type_output_band->machine_type_id,
                        "packing_type_id" => $packing_type_id
                    ]);
                }

            }
        } else {
            return back()->withErrors("لطفا حداقل یک بسته بندی را انتخاب کنید.");
        }


        return redirect()->
        route($this->route_path . "index", [
            $machine_type_output_band->machine_type_id,
        ])->
        with(["success" => "یک جنس کالا اضافه گردید."]);
    }

    public function delete_goods_kind(MachineTypeOutputBand $machine_type_output_band, GoodsKind $goods_kind)
    {

        if (MachineTypeOutputBandGoodsKind::where([
                "machine_type_output_band_id" => $machine_type_output_band->id,
            ])->count() == 1) {
            return back()->withErrors("حداقل یک جنس کالا باید برای باند خروجی انتخاب شده باشد.");
        }
        MachineTypeOutputBandGoodsKind::where([
            "machine_type_output_band_id" => $machine_type_output_band->id,
            "goods_kind_id" => $goods_kind->id
        ])->delete();

        MachineTypeOutputBandPackingType::where([
            "machine_type_output_band_id" => $machine_type_output_band->id,
            "goods_kind_id" => $goods_kind->id,

        ])->delete();


        return redirect()->
        route($this->route_path . "edit", [
            $machine_type_output_band->machine_type_id,
            $machine_type_output_band
        ])->
        with(["success" => "یک جنس کالا حذف گردید."]);
    }

    public function goods_kind_update(Request $request, MachineTypeOutputBand $machine_type_output_band)
    {


        $data = $request["data"];
        if (!isset($data)) {
            return back()->withErrors("برای هر رسته های کالایی باید حداقل یک بسته بندی انتخاب گردد.");
        } else {

            $list_packing_type = MachineTypeOutputBandGoodsKind::where("machine_type_output_band_id", $machine_type_output_band->id)->get();

            foreach ($list_packing_type as $item) {
                if (!isset($data[$item->goods_kind_id])) {
                    return back()->withErrors("برای هر رسته های کالایی باید حداقل یک بسته بندی انتخاب گردد.");
                }
            }
        }

        MachineTypeOutputBandPackingType::where("machine_type_output_band_id", $machine_type_output_band->id)->delete();

        foreach ($data as $goods_kind_id => $packing_type_list) {
            foreach ($packing_type_list as $packing_type_id => $value) {
                MachineTypeOutputBandPackingType::create([
                    "machine_type_output_band_id" => $machine_type_output_band->id,
                    "goods_kind_id" => $goods_kind_id,
                    "machine_type_id" => $machine_type_output_band->machine_type_id,
                    "packing_type_id" => $packing_type_id
                ]);
            }

        }

        return redirect()->
        route($this->route_path . "index", [
            $machine_type_output_band->machine_type_id
        ])->
        with(["success" => "بسته بندی های مجاز برای همه رسته های کالایی بروز رسانی شد."]);

    }

    public function calculation_method(MachineType $machine_type, MachineTypeOutputBand $machine_type_output_band, $mtob_goods_kind_id)
    {

        $machine_type_output_band_goods_kind = MachineTypeOutputBandGoodsKind::find($mtob_goods_kind_id);
        if (!$machine_type_output_band_goods_kind) {
            return back()->withErrors("اطلاعات خروجی گروه ماشین برای رسته کالایی یافت نشد.");
        }

        $machine_type_calculation_method_for_unit_option = Option::get("machine_type_calculation_method", $machine_type_output_band_goods_kind->machine_type_calculation_method_for_unit_id);
        $machine_type_calculation_method_for_sub_unit_option = Option::get("machine_type_calculation_method", $machine_type_output_band_goods_kind->machine_type_calculation_method_for_sub_unit_id);
        $machine_type_calculation_method_for_sub_unit2_option = Option::get("machine_type_calculation_method", $machine_type_output_band_goods_kind->machine_type_calculation_method_for_sub_unit2_id);

        $smart_object_for_unit_option = Option::get("smart_object", $machine_type_output_band_goods_kind->smart_object_id_for_unit, 0, [2]);
        $smart_object_for_sub_unit_option = Option::get("smart_object", $machine_type_output_band_goods_kind->smart_object_id_for_sub_unit, 0, [2]);
        $smart_object_for_sub_unit2_option = Option::get("smart_object", $machine_type_output_band_goods_kind->smart_object_id_for_sub_unit2, 0, [2]);


        // اطلاعات درجه و انبار برای رسته کالایی
        $degrees = Degree::where("goods_kind_id", $machine_type_output_band_goods_kind->goods_kind_id)->get();
        $warehouse_option_default = Option::get("warehouse", 0, 0, [1]);

        $output_band_warehouse = MachineTypeOutputBandWarehouse::where([
            "machine_type_id" => $machine_type->id,
            "goods_kind_id" => $machine_type_output_band_goods_kind->goods_kind_id,
        ])->pluck("warehouse_id", "degree_id");
        $output_band_quality_warehouse = MachineTypeOutputBandWarehouse::where([
            "machine_type_id" => $machine_type->id,
            "goods_kind_id" => $machine_type_output_band_goods_kind->goods_kind_id,
        ])->pluck("quality_control_warehouse_id", "degree_id");

        $warehouse_caption = Warehouse::whereIn("id", $output_band_warehouse)->pluck("caption", "id");

        $warehouse_option = [];
        $quality_control_warehouse_option = [];
        foreach ($degrees as $degree) {
            $warehouse_option[$degree->id] = $warehouse_option_default;
            $quality_control_warehouse_option[$degree->id] = $warehouse_option_default;
            if (isset($output_band_warehouse[$degree->id])) {
                $warehouse_option[$degree->id]["value"] = $output_band_warehouse[$degree->id];
                $warehouse_option[$degree->id]["text"] = $warehouse_caption[$output_band_warehouse[$degree->id]];
                foreach ($warehouse_option[$degree->id]["items"] as &$item) {
                    if ($item["value"] == $output_band_warehouse[$degree->id]) {
                        $item["selected"] = true;
                    }
                }
            }

            if (isset($output_band_quality_warehouse[$degree->id])) {
                $quality_control_warehouse_option[$degree->id]["value"] = $output_band_warehouse[$degree->id];
                $quality_control_warehouse_option[$degree->id]["text"] = $warehouse_caption[$output_band_warehouse[$degree->id]];
                foreach ($quality_control_warehouse_option[$degree->id]["items"] as &$item) {
                    if ($item["value"] == $output_band_quality_warehouse[$degree->id]) {
                        $item["selected"] = true;
                    }
                }
            }


        }


        return view($this->view_path . "calculation_method", compact(
                "machine_type",
                "machine_type_output_band",
                "machine_type_output_band_goods_kind",
                "machine_type_calculation_method_for_unit_option",
                "machine_type_calculation_method_for_sub_unit2_option",
                "machine_type_calculation_method_for_sub_unit_option",
                "smart_object_for_unit_option",
                "smart_object_for_sub_unit_option",
                "smart_object_for_sub_unit2_option",
                "degrees", "warehouse_option",
                "output_band_warehouse",
                "quality_control_warehouse_option"


            )
        );
    }

    public function submit_calculation_method(Request $request, MachineType $machine_type, MachineTypeOutputBand $machine_type_output_band, $mtob_goods_kind_id)
    {

        $machine_type_output_band_goods_kind = MachineTypeOutputBandGoodsKind::find($mtob_goods_kind_id);
        if (!$machine_type_output_band_goods_kind) {
            return back()->withErrors("اطلاعات خروجی گروه ماشین برای رسته کالایی یافت نشد.");
        }
        $machine_type_output_band_goods_kind->update($request->all());

        return redirect()->route("line_product_station.machine_type.output_band.index", $machine_type)->
        with(["success" => "اطلاعات با موفقیت ذخیر شد"]);


    }

    public function submit_output_band_warehouse(Request $request, MachineType $machine_type, $mtob_goods_kind_id)
    {
        $machine_type_output_band_goods_kind = MachineTypeOutputBandGoodsKind::find($mtob_goods_kind_id);
        if (!$machine_type_output_band_goods_kind) {
            return back()->withErrors("اطلاعات خروجی گروه ماشین برای رسته کالایی یافت نشد.");
        }
        $degrees = Degree::where("goods_kind_id", $machine_type_output_band_goods_kind->goods_kind_id)->get();
        foreach ($degrees as $item) {
            $key = "warehouse_" . $item->id;
            $quality_key = "quality_control_warehouse_" . $item->id;
            if (isset($request->$key) && !isset($request->$quality_key)) {
                return back()->withErrors("لطفا انبار کنترل کیفیت را برای درجه ها مشخص نمایید.");
            }
        }

        MachineTypeOutputBandWarehouse::where([
            "machine_type_id" => $machine_type->id,
            "goods_kind_id" => $machine_type_output_band_goods_kind->goods_kind_id,
        ])->delete();


        foreach ($degrees as $item) {
            $key = "warehouse_" . $item->id;
            $quality_key = "quality_control_warehouse_" . $item->id;
            if (isset($request->$key)) {
                MachineTypeOutputBandWarehouse::create([
                    "machine_type_id" => $machine_type->id,
                    "goods_kind_id" => $machine_type_output_band_goods_kind->goods_kind_id,
                    "degree_id" => $item->id,
                    "warehouse_id" => $request->$key,
                    "quality_control_warehouse_id" => $request->$quality_key,
                ]);
            }
        }

        return back()->with(["success" => "اطلاعات انبار رسته کالایی به ازای هر درجه با موفقیت ذخیره گردید."]);
    }
}
