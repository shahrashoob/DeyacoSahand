<?php

namespace App\Models\LineProduct\Machine\Allocation;

use App\Models\Form\FormGeneralItem;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Product;
use App\Models\Utility\Setting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class MachineAllocationActualCost extends Model
{
    use HasFactory;

    protected $fillable = [
        "allocation_id",
        "product_id",
        "packing_type_id",
        "total_price",
        "tax_price",
        "price",
        "transportation_fare_price",
        "amount",
        "cost_of_one_unit",
        "status_id"
    ];



//    public static function createFromFormGeneralItem(FormGeneralItem $formGeneralItem)
//    {
//        $actual_cost = MachineAllocationActualCost::create([
//            "allocation_id" => $formGeneralItem->machine_allocation->allocation_id,
//            "product_id" => $formGeneralItem->product_id,
//            "packing_type_id" => $formGeneralItem->packing_type_id,
//            "price" => $formGeneralItem->price,
//            "tax_price" => $formGeneralItem->tax_price,
//            "transportation_fare_price" => 0,
//            "amount" => $formGeneralItem->amount,
//            "cost_of_one_unit" => 0,
//            "status_id" => is_null($formGeneralItem->price) ?
//                5105200 //  در انتظار ثبت اطلاعات مالی
//                :
//                5105300 // اطلاعات مالی ثبت شده است
//        ]);
//        return $actual_cost;
//    }

//    /**
//     * محاسبه بهای تمام شده با توجه به مبلغ های ثبت شده برای آن
//     * @return array
//     */
//    public static function calculateCostOf(MachineAllocationActualCost $machineAllocationActualCost)
//    {
//        if ($machineAllocationActualCost->status_id == 5105300) { // اطلاعات مالی ثبت شده است.
//            $total_price = $machineAllocationActualCost->price + $machineAllocationActualCost->transportation_fare_price;
//            $cost_of_one_unit = ($total_price) / $machineAllocationActualCost->amount;
//            $machineAllocationActualCost->cost_of_one_unit = $cost_of_one_unit;
//            $machineAllocationActualCost->total_price = $total_price;
//            $machineAllocationActualCost->status_id = 5105100; // بهای تمام شده محاسبه شده است.
//            $machineAllocationActualCost->save();
//            return [
//                "result" => true
//            ];
//        }
//        return [
//            "result" => false,
//            "error" => "وضعیت رکورد جهت محاسبه بهای تمام شده معتبر نمی باشد."
//        ];
//    }

    /**
     * محاسبه بهای تمام شده بر اساس آخرین قیمت
     * @param $packing_type_id
     * @return void
     */
//    public function costBaseOnLatestPrice($packing_type_id = null, $number_format = "")
//    {
//        $last_actual_cost = self::where("product_id", $this->product_id)->
//        where("packing_type_id", $packing_type_id)->
//        where("status_id", 5105100)-> // بهای تمام شده محاسبه شده است.
//        orderByDesc("id")->first();
//
//        if (isset($last_actual_cost->cost_of_one_unit)) {
//            $number = round($last_actual_cost->cost_of_one_unit);
//            if ($number_format == "number_format") {
//                return number_format($number);
//            }
//            return $number;
//
//        }
//        return "";
//    }

//    /**
//     * محاسبه بهای تمام شده بر اساس میانگین قیمت در دوره مالی
//     * @param $packing_type_id
//     * @return float|int|string
//     */
//    public function costBaseOnAverageLatestPrice($packing_type_id,$number_format = "")
//    {
//
//        $financial_year = Setting::FinancialYear();
//        $start_date_time = $financial_year["start_date_time"];
//        $last_date_time = $financial_year["last_date_time"];
//
//        $avg_actual_cost = self::where("product_id", $this->product_id)->
//        where("packing_type_id", $packing_type_id)->
//        where("created_at", ">=", $start_date_time)->
//        where("created_at", "<", $last_date_time)->
//        where("status_id", 5105100)-> // بهای تمام شده محاسبه شده است.
//        selectRaw("sum(cost_of_one_unit * amount) as sum_cost_of_one_unit_amount, sum(amount) as sum_amount")->
//        first();
//
//        if (isset($avg_actual_cost->sum_amount)) {
//            $number = round($avg_actual_cost->sum_cost_of_one_unit_amount / $avg_actual_cost->sum_amount);
//            if ($number_format == "number_format") {
//                return number_format($number);
//            }
//            return $number;
//
//        }
//        return "";
//
//    }

//    /**
//     * بهای تمام شده بر اساس قیمت روز
//     * @return void
//     */
//    public function costBaseOnCurrentDay($packing_type_id,$number_format = "")
//    {
//        $actual_cost = Product\ProductActualCost::where([
//            "product_id" => $this->product_id,
//            "packing_type_id" => $packing_type_id
//        ])->first();
//
//        if (isset($actual_cost->price)) {
//            $number = round($actual_cost->price);
//            if ($number_format == "number_format") {
//                return number_format($number);
//            }
//            return $number;
//
//        }
//        return "";
//        return isset($actual_cost->price) ? round($actual_cost->price) : "";
//    }

//    public function costOfOneUnit($number_format = ""){
//        if (isset($this->cost_of_one_unit)) {
//            $number = round($this->cost_of_one_unit);
//            if ($number_format == "number_format") {
//                return number_format($number);
//            }
//            return $number;
//
//        }
//        return "";
//    }
}
