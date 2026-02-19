<?php

namespace App\Http\Controllers\LineProductStation\MachineType;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBand;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Machine\MachineTypeInputBandPackingType;
use App\Models\Utility\Option;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandlingGoodsKindTimeLimit;
use Illuminate\Http\Request;
use function back;
use function redirect;
use function view;

class InputBandController extends Controller
{

    var $view_path = "line_product_station.machine_type.input_band.";
    var $route_path = "line_product_station.machine_type.input_band.";

    public function index(MachineType $machine_type)
    {


        MachineType\MachineTypeInputAlgorithm::UpdateMachineTypeInputAlgorithm($machine_type);
        $list = $machine_type->machine_type_input_algorhtim()->get();
        $raw_material_request_algorithm_type_option=[];
        $raw_material_request_sampling_algorithm_type_option=[];
        $dependency_to_other_goods_kind=[];
        foreach ($list as $item) {
            $raw_material_request_algorithm_type_option[$item->goods_kind_id] = Option::get("raw_material_request_algorithm_type", $item->raw_material_request_algorithm_type_id);
            $raw_material_request_sampling_algorithm_type_option[$item->goods_kind_id] = Option::get("raw_material_request_algorithm_type", $item->raw_material_request_sampling_algorithm_type_id);
            $dependency_to_other_goods_kind[$item->goods_kind_id] = $item->dependency_to_other_goods_kind;

        }

//return $raw_material_request_sampling_algorithm_type_option;
        return view($this->view_path . "index", compact("machine_type","dependency_to_other_goods_kind", "raw_material_request_algorithm_type_option", "raw_material_request_sampling_algorithm_type_option"));
    }


    public function create(MachineType $machine_type)
    {

        $goods_kind_option = Option::get("goods_kind");

        return view($this->view_path . "create", compact("machine_type", "goods_kind_option"));

    }

    public function store(Request $request, MachineType $machine_type)
    {

        if ($request->line_input_number < 0 || $request->line_input_number > 100000) {
            return back()->withErrors("تعداد خط ورودی نادرست است.");
        }
        $goods_kind = GoodsKind::find($request->goods_kind_id);
        if (!isset($goods_kind)) {
            return back()->withErrors("لطفا جنس کالا را انتخاب کنید.");
        }

        $input_band_count = MachineTypeInputBand::where("machine_type_id", $machine_type->id)->count();

        $input_band = MachineTypeInputBand::create([
            "machine_type_id" => $machine_type->id,
            "code" => $input_band_count + 1,
            "caption" => "ورودی " . ($input_band_count + 1),
            "active_status_id" => 1200,
            "input_line_number" => $request->input_line_number,
            "can_used_material_with_different_lot_per_production_card" => isset($request->can_used_material_with_different_lot_per_production_card)
        ]);
        MachineTypeInputBandGoodsKind::create([
            "machine_type_input_band_id" => $input_band->id,
            "goods_kind_id" => $goods_kind->id,
        ]);

        return redirect()->
        route($this->route_path . "index", $machine_type)->
        with(["success" => "یک باند ورودی جدید اضافه گردید."]);
    }

    public function edit(MachineType $machine_type, MachineTypeInputBand $machine_type_input_band)
    {

        $status_option = Option::get("active_status", $machine_type_input_band->active_status_id);
        WarehouseHandlingGoodsKindTimeLimit::where([
            "warehouse_type_id"=>3
        ])->get();

        return view($this->view_path . "edit", compact("status_option", "machine_type", "machine_type_input_band"));
    }

    public function update(Request $request, MachineTypeInputBand $machine_type_input_band)
    {

        $request["can_used_material_with_different_lot_per_production_card"] = isset($request->can_used_material_with_different_lot_per_production_card);

        $machine_type_input_band->update($request->all());

        return redirect()->route($this->route_path . "index", $machine_type_input_band->machine_type_id)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    public function update_goods_kind_algorithm(Request $request, MachineType $machine_type)
    {

        $list = $machine_type->machine_type_input_algorhtim()->get();
        foreach ($list as $item) {
            $key = "algorithm_type_id_" . $item->goods_kind_id;
            $key_sampling = "sampling_algorithm_type_id_" . $item->goods_kind_id;
            $dependency_to_other_goods_kind = "dependency_to_other_goods_kind_" . $item->goods_kind_id;


            if (!$request->$key || !$request->$key_sampling) {
                return back()->withErrors("لطفا نوع الگوریتم های درخواست مواد اولیه را به درستی وارد نمایید.");
            }

            $machine_type_input_algorithm = MachineType\MachineTypeInputAlgorithm::
            where("machine_type_id", $machine_type->id)->
            where("goods_kind_id", $item->goods_kind_id)->
            first();

            if ($machine_type_input_algorithm) {
                $machine_type_input_algorithm->raw_material_request_algorithm_type_id = $request->$key;
                $machine_type_input_algorithm->raw_material_request_sampling_algorithm_type_id = $request->$key_sampling;
                $machine_type_input_algorithm->dependency_to_other_goods_kind = $request->$dependency_to_other_goods_kind;
                $machine_type_input_algorithm->save();
            } else {
                return back()->withErrors("لطفا نوع الگوریتم های درخواست مواد اولیه در رسته کالایی ".$machine_type_input_algorithm->goods_kinc->caption." را به درستی وارد نمایید.");

            }


        }

        return back()->with(["success" => "اطلاعات الگوریتم ها با موفقیت ثبت گردید."]);
    }

    public function add_goods_kind(Request $request, MachineTypeInputBand $machine_type_input_band)
    {
        $goods_kind_option = Option::get("goods_kind");

        $machine_type = $machine_type_input_band->machine_type;

        return view($this->view_path . "add_goods_kind", compact("machine_type", "machine_type_input_band", "goods_kind_option"));

    }

    public function add_goods_kind_submit_step1(Request $request, MachineTypeInputBand $machine_type_input_band)
    {

        $goods_kind = GoodsKind::find($request->goods_kind_id);
        if (!isset($goods_kind)) {
            return redirect()->route($this->route_path . "index", $machine_type_input_band->machine_type_id)->
            withErrors("لطفا جنس کالا را انتخاب کنید.");
        }


        return redirect()->
        route($this->route_path . "add_goods_kind_step2", [
            $machine_type_input_band,
            $request->goods_kind_id,
            0
        ]);

    }

    public function add_goods_kind_step2(Request $request, MachineTypeInputBand $machine_type_input_band, GoodsKind $goods_kind, $effect_is_shared)
    {


        $exists = MachineTypeInputBandGoodsKind::where([
            "machine_type_input_band_id" => $machine_type_input_band->id,
            "goods_kind_id" => $goods_kind->id,
        ])->exists();
        if ($exists) {
            return redirect()->route($this->route_path . "nput_band.index", $machine_type_input_band->machine_type_id)->
            withErrors("رسته کالایی مورد نظر قبلا انتخاب شده است.");
        }
        if ($goods_kind->packing_type()->count() == 0) {
            return back()->withErrors("برای رسته کالایی انتخاب شده، بسته بندی تعریف نشده است، لطفا با پشتیبانی تماس بگیرید.");
        }
        $machine_type = $machine_type_input_band->machine_type;

        return view($this->view_path . "add_goods_kind_step2", compact("machine_type", "machine_type_input_band", "goods_kind", "effect_is_shared"));

    }

    public function add_goods_kind_submit_step2(Request $request, MachineTypeInputBand $machine_type_input_band, GoodsKind $goods_kind, $effect_is_shared)
    {

        $exists = MachineTypeInputBandGoodsKind::where([
            "machine_type_input_band_id" => $machine_type_input_band->id,
            "goods_kind_id" => $goods_kind->id,
        ])->exists();
        if ($exists) {
            return redirect()->route($this->route_path . "index", $machine_type_input_band->machine_type_id)->
            withErrors("رسته کالایی مورد نظر قبلا انتخاب شده است.");
        }


        $data = $request["data"];;
        if (isset($data)) {

            MachineTypeInputBandGoodsKind::firstOrCreate([
                "machine_type_input_band_id" => $machine_type_input_band->id,
                "goods_kind_id" => $goods_kind->id,
            ]);

            foreach ($data as $goods_kind_id => $packing_type_list) {
                foreach ($packing_type_list as $packing_type_id => $value) {
                    MachineTypeInputBandPackingType::create([
                        "machine_type_input_band_id" => $machine_type_input_band->id,
                        "goods_kind_id" => $goods_kind_id,
                        "machine_type_id" => $machine_type_input_band->machine_type_id,
                        "packing_type_id" => $packing_type_id
                    ]);
                }

            }
        } else {
            return back()->withErrors("لطفا حداقل یک بسته بندی را انتخاب کنید.");
        }


        return redirect()->
        route($this->route_path . "index", [
            $machine_type_input_band->machine_type_id,
        ])->
        with(["success" => "یک جنس کالا اضافه گردید."]);
    }

    public function delete_goods_kind(MachineTypeInputBand $machine_type_input_band, GoodsKind $goods_kind)
    {

        if (MachineTypeInputBandGoodsKind::where([
                "machine_type_input_band_id" => $machine_type_input_band->id,
            ])->count() == 1) {
            return back()->withErrors("حداقل یک جنس کالا باید برای ورودی انتخاب شده باشد.");
        }
        MachineTypeInputBandGoodsKind::where([
            "machine_type_input_band_id" => $machine_type_input_band->id,
            "goods_kind_id" => $goods_kind->id
        ])->delete();
        MachineTypeInputBandPackingType::where([
            "machine_type_input_band_id" => $machine_type_input_band->id,
            "goods_kind_id" => $goods_kind->id
        ])->delete();

        return redirect()->
        route($this->route_path . "input_band.edit", [
            $machine_type_input_band->machine_type_id,
            $machine_type_input_band
        ])->
        with(["success" => "یک جنس کالا حذف گردید."]);
    }

    public function goods_kind_update(Request $request, MachineTypeInputBand $machine_type_input_band)
    {

        //  return $request->all();
        $data = $request["data"];
        if (!isset($data)) {
            return back()->withErrors("برای هر رسته های کالایی باید حداقل یک بسته بندی انتخاب گردد.");
        } else {

            $list_packing_type = MachineTypeInputBandGoodsKind::where("machine_type_input_band_id", $machine_type_input_band->id)->get();

            foreach ($list_packing_type as $item) {
                if (!isset($data[$item->goods_kind_id])) {
                    return back()->withErrors("برای هر رسته های کالایی باید حداقل یک بسته بندی انتخاب گردد.");
                }
            }
        }

        MachineTypeInputBandPackingType::where("machine_type_input_band_id", $machine_type_input_band->id)->delete();

        foreach ($data as $goods_kind_id => $packing_type_list) {
            foreach ($packing_type_list as $packing_type_id => $value) {
                MachineTypeInputBandPackingType::create([
                    "machine_type_input_band_id" => $machine_type_input_band->id,
                    "goods_kind_id" => $goods_kind_id,
                    "machine_type_id" => $machine_type_input_band->machine_type_id,
                    "packing_type_id" => $packing_type_id
                ]);
            }

            MachineTypeInputBandGoodsKind::where("machine_type_input_band_id", $machine_type_input_band->id)->
            where("goods_kind_id", $goods_kind_id)->update([
                // بروز رسانی فیلد، آیا درخواست کالا از انبارک ماشین توسط دستیار دیجیتال ارسال شود؟
                "send_product_request_form_by_robot" => $request->send_product_request_form_by_robot[$goods_kind_id],
// بروز رسانی فیلد، آیا درخواست کالا از انبارک ماشین توسط دستیار دیجیتال فعال باشد؟
                "product_request_form_by_robot_is_enabled" => $request->product_request_form_by_robot_is_enabled[$goods_kind_id],
// امکان تزریق مواد اولیه به صورت دستی برای رسته کالایی وجود دارد؟
                "allowing_raw_materials_to_be_injected_manually" => $request->allowing_raw_materials_to_be_injected_manually[$goods_kind_id],
                // آیا بسته بندی های این رسته کالایی در لیست برگشت مواد اولیه قرار گیرد؟
                "packing_form_in_return_raw_material_list" => $request->packing_form_in_return_raw_material_list[$goods_kind_id],
                // آیا تایید فرم های ورود به انبار به صورت تجمیعی باشد؟
                "warehouse_entry_confirmation_in_altogether" => $request->warehouse_entry_confirmation_in_altogether[$goods_kind_id],


            ]);

        }

        return redirect()->
        route($this->route_path . "index", [
            $machine_type_input_band->machine_type_id
        ])->
        with(["success" => "بسته بندی های مجاز برای همه رسته های کالایی بروز رسانی شد."]);

    }

}
