<?php

namespace App\Models\LineProduct\Machine\RequestRawMaterialAlgorithm;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Product;
use App\Models\Production\ProductionFormItem;
use App\Models\HR\Shift\Shift;
use App\Models\Utility\Script\Script;
use App\Models\Warehouse\Warehouse;
use Carbon\Carbon;

class Algorithm4WithMinMaxCapacityByBatch extends Controller
{
    // این الگوریتم مثل الگوریتم شماره 2 است با این تفاوت که به ازای هر تخصیص جاری و رزرو فقط یک بار درخواست صادر می کند.
    public static function RequestForMachine(Machine $machine, Script $script, $special_goods_kind_ids, $user_id, $emergency_time, $production_type_id)
    {


        $log_data = [];
        $log_data["emergency_time"] = $emergency_time;
        $log_data["algorithm"] = "Algorithm4WithMinMaxCapacityByBatch";
        $log_data["machine"]["id"] = $machine->id;
        $log_data["machine"]["caption"] = $machine->caption;


        // محاسبه مقدار باقی مانده کارت تولید جاری
        $current_allocation = $machine->getCurrentAllocation();
        if($current_allocation) {
            $product_request_form = Product\ProductRequest\ProductRequestForm::where("allocation_id", $current_allocation->id)->first();
            if(!$product_request_form) {
                $other_data["allocation_id"] = $current_allocation->id;
                $result= Algorithm2WithMinMaxCapacity::RequestForMachine($machine, $script,  $special_goods_kind_ids, $user_id, $emergency_time, $production_type_id,$other_data);
                $result["log_data"]["algorithm"] = "Algorithm4WithMinMaxCapacityByBatch";
                return $result;
            }
        }

        // اگر تخصیص جاری درخواست داشت برای تخصیص های رزور تخصیص را چک می کنیم.
        foreach ($machine->ReserveAllocation()->get() as $reserve_allocation) {

            $product_request_form = Product\ProductRequest\ProductRequestForm::where("allocation_id", $reserve_allocation->id)->first();
            if(!$product_request_form) {
                $other_data["allocation_id"] = $reserve_allocation->id;
                $result= Algorithm2WithMinMaxCapacity::RequestForMachine($machine, $script,  $special_goods_kind_ids, $user_id, $emergency_time, $production_type_id,$other_data);
                $result["log_data"]["algorithm"] = "Algorithm4WithMinMaxCapacityByBatch";
                return $result;
            }

        }

        $result["material_list"] = [];
        $result["material_list_for_sampling"] = [];
        $result["degree_id_list"] = [];
        $result["warehouse_material_packing_count"] = [];
        $result["goods_kind_id_list"] = [];
        $result["reserve_allocation_priority"] = [];
        $result["log_data"] = $log_data;
        $result["result"] = true;

        return $result;

    }

}
