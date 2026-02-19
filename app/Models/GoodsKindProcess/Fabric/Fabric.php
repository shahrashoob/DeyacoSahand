<?php

namespace App\Models\GoodsKindProcess\Fabric;

use App\Events\Machine\MachineLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestFormItem;

use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineMaterialFlow;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineLotTmps;
use App\Models\Production\Production;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionFormItemLotNumber;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Fabric extends Model
{
    use HasFactory;

    public static function getCurrentLot()
    {
        return "01";
    }

    public static function ChangeLot(Machine $machine, $product_id)
    {
        $new_lot_code = "01";
        $exists_lot_number = LotNumber::where(["product_id" => $product_id, "code" => $new_lot_code])->first();

        if ($exists_lot_number) {
            return [
                "result" => true,
                "product_id" => $product_id,
                "lot_number" => $exists_lot_number,
                "is_new_lot" => false
            ];
        }
        $machine_effective_lot_code = $machine->machine_type->lot_effective_code;

        $new_lot_number = new LotNumber();
        $new_lot_number->product_id = $product_id;
        $new_lot_number->machine_lot_effective_code = $machine_effective_lot_code;
        $new_lot_number->code = $new_lot_code;
        $new_lot_number->user_id = Auth::user()->id;
        $new_lot_number->machine_id = $machine->id;


        $new_lot_number->save();

        return [
            "result" => true,
            "lot_number" => $new_lot_number,
            "is_new_lot" => true
        ];
    }

    public static function GetProductionStatus(Production $production)
    {
        if ($production->waiting_status_id == 7301004) {
            return 7301004;
        } // خاتمه یافته)


        $amount = $production->number; // مقدار کارت تولید
        $allocation_amount = MachineAllocation::where("production_id", $production->id)->
        whereNotIn("status_id", [5310005, 5310030])->
        groupBy("status_id")->
        selectRaw("sum(allocation_amount) as allocation_amount , status_id")->
        pluck("allocation_amount", "status_id");

        $production_amount=$production->get_production_amount();

        if (!isset($allocation_amount[5310010])) {
            $allocation_amount[5310010] = 0;
        }
        if (!isset($allocation_amount[5310020])) {
            $allocation_amount[5310020] = 0;
        }
        if (!isset($allocation_amount[5310040])) {
            $allocation_amount[5310040] = 0;
        }
        if (!isset($allocation_amount[5310050])) {
            $allocation_amount[5310050] = 0;
        }

        $new_status = $production->waiting_status_id;
        $new_status = 0;

        $percent = $production->product->goods_kind->min_diff_of_production_and_allocation_in_the_end_of_production;

        $amount -= $amount * $percent / 100;



        if ($allocation_amount[5310050] > 0 && $allocation_amount[5310010] == 0 && $allocation_amount[5310040] == 0) {
            $new_status = 7301005; // تخصیص مجدد
        }  else if ($allocation_amount[5310010] > 0) { // در حال تولید
            $new_status = 7301003; // در حال تولید
        } else if ($allocation_amount[5310040] > 0) { // تخصیص رزرو
            $new_status = 7301002; // نصب و راه اندازی
        } else if ($allocation_amount[5310050] > 0) { // تخصیص رزرو
            $new_status = 7301005; // تخصیص مجدد
        }
        else if ($production_amount >= $amount) { // خاتمه یافته
            $new_status = 7301004; // خاتمه یافته
        }
        else if ($allocation_amount[5310020] >= 0) { // خاتمه یافته
            $new_status = 7301005; // تخصیص مجدد
        }
        else {
            $new_status = 7301001; // نصب و راه اندازی
        }

        return $new_status;
    }
}