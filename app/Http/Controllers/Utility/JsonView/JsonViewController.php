<?php

namespace App\Http\Controllers\Utility\JsonView;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\AllocationData;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product;
use App\Models\Production\Production;
use App\Models\Utility\JsonDataList;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;

class JsonViewController extends Controller
{
    //
    var $view_path = "utility.json_view.";

    public function actual_consumption_view(JsonDataList $json_data_list)
    {

        $json_data = json_decode($json_data_list->data);
        $product_id = isset($json_data->product_id) ? $json_data->product_id : 0;
        if ($json_data) {
            $json_data->product = Product::find($product_id);
        }

//return $json_data_list;
        return view($this->view_path . "actual_consumption_view", compact("json_data"));
    }

    public function allocation_data_type_200($production_id, $machine_id, $allocation_id = null)
    {

        if ($allocation_id) {
            $machine_allocation = MachineAllocation::where([
                "allocation_id" => $allocation_id,
            ])->first();
            $production = $machine_allocation->production;
        } else {
            // اگر allocation_id نال است، پس با توجه به ماشین، کارت و وضعیت تخصیص، وضعیت آن را تشخیص می دهیم.
            $machine_allocation = MachineAllocation::where([
                "production_id" => $production_id,
                "machine_id" => $machine_id,
                "status_id" => 5310005
            ])->first();
            $production = $machine_allocation->production ?? null;
            $allocation_id = $machine_allocation->allocation_id ?? 0;
        }
        $allocation_data = AllocationData::where([
            "allocation_id" => $allocation_id,
            "allocation_data_type_id" => 200, //  اطلاعات تخصیص
        ])->first();

        if (!$allocation_data) {
            return back()->withErrors("اطلاعات تخصیص یافت نشد، لطفا دوباره تلاش کنید.");
        }

        $json_data = json_decode($allocation_data->data);

        $bom = null;
        $all_material_ids = [];
        // به دست آوردن SP به ازای هر لیست
        foreach ($json_data->end_result as $item) {

            if (isset($item->bom_id)) {
                $bom = Product\BOM\BOM::find($item->bom_id);
            }
            $material_ids = [];
            foreach ($item->material as $material_info) {
                $material_ids[] = $material_info->material_id;
                $all_material_ids[$material_info->material_id] = $material_info->material_id;
            }
            if ($bom) {

                $result = Product\BOM\BOMPermutation::GetSPCodeFromMaterialId($bom, $material_ids);
                if ($result["result"]) {
                    $item->bom_permutation = $result["bill_of_material_permutation"];
                }
            }

        }
        $all_material_ids[-1] = -1;

        $all_material = Product::whereIn("id", $all_material_ids)->selectRaw("concat(code,' - ',caption) as caption,id")->pluck("caption", "id");

        // موجودی انبار در زمان تخصیص
        $TRUNCATE = 6;
        $current_inventory = WarehouseProduct::
        whereIn("product_id", $all_material_ids)->
        groupBy("product_id")->
        groupBy("warehouse_id")->
        selectRaw("round( TRUNCATE(sum(input),$TRUNCATE) - TRUNCATE(sum(output),$TRUNCATE) ,3) as value,warehouse_id,product_id")->
        havingRaw('ROUND(value,2) > 0')->
        orHavingRaw('ROUND(value,2) < 0')->
        get()->
        keyBy(function ($key) {
            return $key->product_id . "_" . $key->warehouse_id;
        });

        $before_inventory = WarehouseProduct::
        whereIn("product_id", $all_material_ids)->
        where("created_at", "<=", $allocation_data->created_at)->
        groupBy("product_id")->
        groupBy("warehouse_id")->
        havingRaw('ROUND(value,2) > 0')->
        orHavingRaw('ROUND(value,2) < 0')->
        selectRaw("round( TRUNCATE(sum(input),$TRUNCATE) - TRUNCATE(sum(output),$TRUNCATE) ,3) as value,warehouse_id,product_id")->
        get()->
        keyBy(function ($key) {
            return $key->product_id . "_" . $key->warehouse_id;
        });

        $warehouse_list = Warehouse::pluck("caption", "id");

        // گرفتن اطلاعات همه تخصیص های رزرو و جاری
        $all_allocation_ids = [];
        if (isset($json_data->current_reserve_allocation_list)) {
            foreach ($json_data->current_reserve_allocation_list as $current_reserve_allocation_item) {
                $all_allocation_ids[] = $current_reserve_allocation_item->allocation_id;
            }
        }
        $all_allocation_ids[] = -1;
        $all_allocation = Allocation::whereIn("id", $all_allocation_ids)->get()->keyBy("id");
        return view($this->view_path . "allocation_data", compact("json_data", "production", "allocation_id", "all_material", "all_allocation", "warehouse_list", "before_inventory", "current_inventory"));
        return $data;
    }
}
