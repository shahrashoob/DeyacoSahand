<?php

namespace App\Http\Controllers\LineProductStation\GoodsKind;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\SupplyType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class GoodsKindSettingController extends Controller
{
    private $view_path = "line_product_station.goods_kind.setting.";
    private $route_path = "line_product_station.goods_kind.setting.";

    public function index(GoodsKind $goods_kind, $tab_index = "edit")
    {
        $values = GoodsKind\GoodsKindSettingValue::getValues($goods_kind);
        $unit_option = Option::get("unit_select_multiple", $values["default_unit_ids"]);
        $sub_unit_option = Option::get("unit_select_multiple", $values["default_sub_unit_ids"]);
        $sub_unit2_option = Option::get("unit_select_multiple", $values["default_sub_unit2_ids"]);
        $goods_kind_option = Option::get("goods_kind_select_multiple", $values["default_consumed_goods_kinds"]);
        $goods_type_option = Option::get("goods_type_select_multiple", $values["default_goods_type_ids"]);

        $unit_of_measure_type_in_production_option = Option::get("unit_of_measure_type_multiple", $values["default_unit_of_measure_type_ids_in_production"]);
        $unit_of_measure_type_in_sale_option = Option::get("unit_of_measure_type_multiple", $values["default_unit_of_measure_type_ids_in_sale"]);

        $line_option = Option::get("line_select_multiple", $values["default_line_ids"]);
        $bom_productive_consume_warehouse_option = [];
        $bom_warehouse_ids_option = [];
        $bom_sampling_consume_warehouse_option = [];
        foreach ($goods_kind_option["items"] as $goods_kind_select_item) {
            if (isset($goods_kind_select_item["selected"])) {
                $bom_warehouse_ids_option[$goods_kind_select_item["value"]] =
                    Option::get("warehouse_select_multiple", $values["default_bom_warehouse_ids_" . $goods_kind_select_item["value"]], 0, [1]);
                $bom_productive_consume_warehouse_option[$goods_kind_select_item["value"]] =
                    Option::get("warehouse_select_multiple", $values["default_productive_consume_warehouse_ids_" . $goods_kind_select_item["value"]], 0, [3, 4, 5]);
                $bom_sampling_consume_warehouse_option[$goods_kind_select_item["value"]] =
                    Option::get("warehouse_select_multiple", $values["default_sampling_consume_warehouse_ids_" . $goods_kind_select_item["value"]], 0, [3, 4, 5]);
            }
        }

        // به ازای هر نوع تامین الگوریتم تخصیص کارت تولید و الگوریتم صدور کارت تولید داریم.
        $supply_type_list = SupplyType::all();
        $list_goods_kind = GoodsKind\GoodsKindAlgorithm::
        where("goods_kind_id", $goods_kind->id)->
        get()->
        keyBy("supply_type_id");
        $list_option = [];
        foreach ($supply_type_list as $item) {

            $id=isset($list_goods_kind[$item->id]->production_card_allocation_algorithm_id)?$list_goods_kind[$item->id]->production_card_allocation_algorithm_id:0;
            $list_option[$item->id]["production_card_allocation"] = Option::get("algorithm",$id , 200);

            $id=isset($list_goods_kind[$item->id]->production_card_create_algorithm_id)?$list_goods_kind[$item->id]->production_card_create_algorithm_id:0;
            $list_option[$item->id]["production_card_create"] = Option::get("algorithm", $id, 300);
        }


        return view($this->view_path . "index", compact("goods_kind", "unit_option", "sub_unit_option", "sub_unit2_option",
            "goods_kind_option", "goods_kind_option", "goods_type_option", "line_option", "bom_productive_consume_warehouse_option", "bom_sampling_consume_warehouse_option",
            "bom_warehouse_ids_option", "tab_index", "list_option", "supply_type_list",'unit_of_measure_type_in_sale_option',"unit_of_measure_type_in_production_option"));
    }

    public function init_info_store(Request $request, GoodsKind $goods_kind)
    {

        $values = GoodsKind\GoodsKindSettingValue::getValues($goods_kind);
        foreach ($values as $key => $value) {

            if ($request->$key) {
                GoodsKind\GoodsKindSettingValue::setValues($goods_kind, $key, $request->$key);
            }

        }
        return redirect()->route($this->route_path . "index", [$goods_kind, $request->tab_index])->with(["success" => "تنظیمات با موفقیت ذخیره شد."]);
    }

    public function submit_algorithm_info(Request $request, GoodsKind $goods_kind)
    {

        $supply_type_list = SupplyType::all();

        $list_goods_kind = GoodsKind\GoodsKindAlgorithm::
        where("goods_kind_id", $goods_kind->id)->
        get()->
        keyBy("supply_type_id");

        foreach ($supply_type_list as $supply_type) {

            $algorithm_key = "production_card_allocation_" . $supply_type->id;
            $production_card_allocation_algorithm_id = $request->$algorithm_key;


            $algorithm_key = "production_card_create_" . $supply_type->id;
            $production_card_create_algorithm_id = $request->$algorithm_key;

            if (isset($list_goods_kind[$supply_type->id])) {

                $list_goods_kind[$supply_type->id]->production_card_allocation_algorithm_id = $production_card_allocation_algorithm_id;
                $list_goods_kind[$supply_type->id]->production_card_create_algorithm_id = $production_card_create_algorithm_id;

                $list_goods_kind[$supply_type->id]->save();
            } else {
                GoodsKind\GoodsKindAlgorithm::create([
                    "goods_kind_id" => $goods_kind->id,
                    "production_card_allocation_algorithm_id" => $production_card_allocation_algorithm_id,
                    "production_card_create_algorithm_id" => $production_card_create_algorithm_id,
                    "supply_type_id" => $supply_type->id
                ]);
            }
        }

        return redirect()->route($this->route_path . "index", [$goods_kind, $request->tab_index])->with(["success" => "تنظیمات با موفقیت ذخیره شد."]);


    }
}
