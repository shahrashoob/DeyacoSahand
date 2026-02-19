<?php

namespace App\Http\Controllers\Utility\Planing\Product;

use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorProductionChannelType;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Machine\MachineTypeInputBandPackingType;
use App\Models\LineProduct\Machine\MachineTypeOutputBandGoodsKind;
use App\Models\LineProduct\Machine\MachineTypeOutputBandPackingType;
use App\Models\LineProduct\Machine\ProductionChannel\MachineTypeProductionChannelType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLayer;
use App\Models\Production\ProductionChannelNextOne;
use App\Models\Production\ProductionChannelType;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use \Illuminate\Support\Facades\Auth;

class ProductionChannelTypeController extends Controller
{
    // line_product_station/packing/packing_type
    private $view_path = "utility.planing.product.production_channel_type.";
    private $route_path = "utility.planing.product.production_channel_type.";

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $production_channel_category_id = $request->production_channel_category_id;
        } else {
            $production_channel_category_id = session("production_channel_category_id");
        }
        session([
            "production_channel_category_id" => $production_channel_category_id,
        ]);

        $data_fixed = null;
        if ($request->delete_filter && $request->delete_filter == 2) {
            // حذف همه فیلتر های ثابت
            $data_fixed = [
                "production_channel_category_id" => ""
            ];
        } else {
            $data_fixed = [
                "production_channel_category_id" => $production_channel_category_id
            ];

        }
        if ($data_fixed != null) {
            JsonDataList::SetFilter(Auth::id(), 810, $data_fixed);
        }

        $data_fixed = JsonDataList::GetData(Auth::id(), 800);

        $production_channel_category_option = Option::get("production_channel_category", $production_channel_category_id);
        $list = ProductionChannelType::
        when($production_channel_category_id, function ($query) use ($production_channel_category_id) {
            return $query->where('production_channel_category_id', $production_channel_category_id);
        })->
        paginate(100);

        $max_category = 1;
        $max_in_the_way_amount = 1;
        $max_inventory = 1;
        $production_channel_type_ids = [];
        foreach ($list as $item) {
            $production_channel_type_ids[] = $item->id;
        }

        $production_channel_type_values = self::GetProductionChannelValues($production_channel_type_ids, $max_category, $max_inventory, $max_in_the_way_amount)["production_channel_type_values"];


        $route = $this->route_path . "index";
        return view($this->view_path . "index", compact("list", "max_category", "production_channel_category_option", "max_inventory", "max_in_the_way_amount", "production_channel_type_values", "production_channel_category_id", "route"));
    }


    public static function GetProductionChannelValues($production_channel_type_ids, $max_category, $max_inventory, $max_in_the_way_amount)
    {
        $list_channel_production_types = LineProductStation::whereIn("production_channel_type_id", $production_channel_type_ids)->
        join("products", "products.id", "product_id")->
        groupBy("production_channel_type_id", "product_id")->
        selectRaw("product_id , production_channel_type_id  , sum(remaining_order_amount) as remaining_order_amount , sum(in_the_way_amount) as in_the_way_amount, sum(current_inventory) as current_inventory, sum(current_order_needed_amount) as current_order_needed_amount, sum(all_order_amount) as all_order_amount")->
        get();

        $list_channel_production_types_ids = [];
        $production_channel_type_values = [];


        foreach ($list_channel_production_types as $item) {

            if (!isset($list_channel_production_types_ids[$item->production_channel_type_id][$item->product_id])) {
                $list_channel_production_types_ids[$item->production_channel_type_id][$item->product_id] = 1;

                if (!isset($production_channel_type_values[$item->production_channel_type_id])) {
                    $production_channel_type_values[$item->production_channel_type_id] = [
                        "remaining_order_amount" => 0,
                        "current_order_needed_amount" => 0,
                        "all_order_amount" => 0,
                        "current_inventory" => 0,
                        "in_the_way_amount" => 0,
                    ];
                }

                $production_channel_type_values[$item->production_channel_type_id]["remaining_order_amount"] += $item->remaining_order_amount;
                $production_channel_type_values[$item->production_channel_type_id]["current_order_needed_amount"] += $item->current_order_needed_amount;
                $production_channel_type_values[$item->production_channel_type_id]["all_order_amount"] += $item->all_order_amount;
                $production_channel_type_values[$item->production_channel_type_id]["current_inventory"] += $item->current_inventory;
                $production_channel_type_values[$item->production_channel_type_id]["in_the_way_amount"] += $item->in_the_way_amount;
                $max_in_the_way_amount = max($max_in_the_way_amount, $production_channel_type_values[$item->production_channel_type_id]["in_the_way_amount"]);
                $max_inventory = max($max_inventory, $production_channel_type_values[$item->production_channel_type_id]["current_inventory"]);
                $max_category = max($max_category, $production_channel_type_values[$item->production_channel_type_id]["current_order_needed_amount"]);

                //  return $production_channel_type_values;
            }

        }

        return [
            "result" => true,
            "production_channel_type_values" => $production_channel_type_values
        ];
    }

}