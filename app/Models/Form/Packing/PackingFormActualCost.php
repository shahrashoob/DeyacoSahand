<?php

namespace App\Models\Form\Packing;

use App\Models\Accounting\ActualCostType;
use App\Models\Form\Form;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\Pricing\ProductPricing;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingFormActualCost extends Model
{
    use HasFactory;

    protected $fillable = [
        "packing_form_id",
        "product_id",
        "actual_cost_type_id",
        "status_id",
        "cost_of_one_unit",
        "packing_type_id"
    ];

    public function actual_cost_type()
    {
        return $this->belongsTo(ActualCostType::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function get_cost_of_one_unit($number_format = "")
    {
        if ($this->status_id == 6060001) {
            if ($number_format == "number_format") {
                return number_format($this->cost_of_one_unit);
            }
            return $this->cost_of_one_unit;
        }
        return $this->status->caption ?? "";
    }

    public static function UpdateActualCostForBuyAllocation(Allocation $allocation, Form $form)
    {
        if ($form->allocation_id != $allocation->id) {
            // اطلاعات ورودی نادرست است.
            1 / 0;
        }
        if ($allocation->supplier) {

            $supply_type_id = 2; // خرید کالا
            $actual_cost_type_id = 1;// قیمت خرید
        } else {
            $supply_type_id = 3; // تولید کالا توسط پیانکار
            $actual_cost_type_id = 7;// هزینه پیمانکاری
        }

        // محاسبه قیمت خرید برای کالاهایی خریدنی

        $price_status = [];

        $production_form_item = ProductionFormItem::where("allocation_id", $allocation->id)->pluck("id", "product_id");
        foreach ($form->general_items as $form_general_item) {
            if ($form_general_item->price_registration_status_id == 5105200) {
                // در انتظار ثبت اطلاعات مالی، لازم نیست ادامه بدهید چون قیمت هنوز ثبت نشده است.
                continue;
            }
            $production_form_item_id = isset($production_form_item[$form_general_item->product_id]) ? $production_form_item[$form_general_item->product_id] : 0;
            $packing_form_ids = PackingFormItem::where("production_form_item_id", $production_form_item_id)->pluck("packing_form_id");
            $cost_of_one_unit = null;
            if (!is_null($form_general_item->price)) {
                $total_price = $form_general_item->price + $form_general_item->transportation_fare_price;
                $cost_of_one_unit = ($total_price) / $form_general_item->amount;
            }

            foreach ($packing_form_ids as $packing_form_id) {
                self::CreateActualCostTypeItem($packing_form_id, $form_general_item->product_id, $supply_type_id, $form_general_item->packing_type_id);
                self::UpdateFullPrice($packing_form_id, $form_general_item->product_id, $actual_cost_type_id, $cost_of_one_unit);
            }
        }
    }

    public static function UpdatePackingForms($packing_forms_id)
    {

    }

    public static function GetActualCostTypeIds($supply_type_id)
    {
        switch ($supply_type_id) {
            case 1: // تولید داخل
                $actual_cost_type_ids = [2, 1000];//3و4و
                break;
            case 2: // سفارش خرید
                $actual_cost_type_ids = [1, 1000];
                break;
            case 3: // تولید توسط پیمانکار
                $actual_cost_type_ids = [7, 1000];
                break;
            default:
                1 / 0;
        }
        return $actual_cost_type_ids;
    }

    public static function CreateActualCostTypeItem($packing_form_id, $product_id, $supply_type_id, $packing_type_id)
    {

        $actual_cost_type_ids = self::GetActualCostTypeIds($supply_type_id);
        foreach ($actual_cost_type_ids as $actual_cost_type_id) {
            PackingFormActualCost::firstOrCreate(
                [
                    "packing_form_id" => $packing_form_id,
                    "product_id" => $product_id,
                    "actual_cost_type_id" => $actual_cost_type_id,
                ],
                [
                    "status_id" => 6060003,
                    "cost_of_one_unit" => null,
                    "packing_type_id" => $packing_type_id
                ]
            );
        }

    }

    public static function UpdateFullPrice($packing_form_id, $product_id, $actual_cost_type_id = null, $cost_of_one_unit = null)
    {
        if ($actual_cost_type_id) {

            PackingFormActualCost::
            where(["packing_form_id" => $packing_form_id, "product_id" => $product_id])->
            where("actual_cost_type_id", $actual_cost_type_id)->
            update([
                "cost_of_one_unit" => $cost_of_one_unit,
                "status_id" => 6060001  // محاسبه شده
            ]);
        }
        $list = PackingFormActualCost::
        where(["packing_form_id" => $packing_form_id, "product_id" => $product_id])->
        get();

        if ($list->where("actual_cost_type_id", "!=", 1000)->where("status_id", "!=", 6060001)->count() > 0) {
            // اگر حداقل یکی از مواردی که لازم است هنوز محاسبه نشده است.
            $cost_of_one_unit = null;

        } else {
            $cost_of_one_unit = $list->where("actual_cost_type_id", "!=", 1000)->sum("cost_of_one_unit");
        }

        PackingFormActualCost::
        where(["packing_form_id" => $packing_form_id, "product_id" => $product_id])->
        where("actual_cost_type_id", 1000)->
        update([
            "cost_of_one_unit" => $cost_of_one_unit,
            "status_id" => is_null($cost_of_one_unit) ? 6060002 : 6060001  //عدم امکان محاسبه
        ]);
        if (!is_null($cost_of_one_unit)) {
            // اگر قیمت تمام شده بروز شد، پس باید قیمت تمام شده بسته بندی های تغییر داده شده و تولید شده از آن هم بروز شوند.
            QueueOfLargeOperation::AddToQueue(
                [
                    "packing_form_id" => $packing_form_id,
                    "product_id" => $product_id,
                    "cost_of_one_unit" => $cost_of_one_unit,
                    "type" => "packing_cost_change"
                ], 500);
        }
        return [
            "result" => true,
            "allocation__must_update_cost" => is_null($cost_of_one_unit) ? 0 : 1,
            "cost_of_one_unit" => $cost_of_one_unit
        ];

    }


    /******************************/
    public function costBaseOnLatestPrice($number_format = "", $actual_cost_type_id = 1000,$packing_type_id=null)
    {

        if (!$packing_type_id) {
            $this->packing_type_id = $this->packing_type_id;
        }
        $last_actual_cost = self::
        where("product_id", $this->product_id)->
        where("packing_type_id",$packing_type_id)->
        where("packing_form_actual_costs.status_id", 6060001)-> // بهای تمام شده محاسبه شده است.
        where("packing_form_actual_costs.actual_cost_type_id", $actual_cost_type_id)-> // بهای تمام شده
        orderByDesc("id")->first();

        if (isset($last_actual_cost->cost_of_one_unit)) {
            $number = round($last_actual_cost->cost_of_one_unit);
            if ($number_format == "number_format") {
                return number_format($number);
            }
            return $number;

        }
        return "";
    }

    /**
     * محاسبه بهای تمام شده بر اساس میانگین قیمت در دوره مالی
     * @param $packing_type_id
     * @return float|int|string
     */
    public function costBaseOnAverageLatestPrice($number_format = "", $actual_cost_type_id = 1000, $packing_type_id = null)
    {

        $financial_year = Setting::FinancialYear();
        $start_date_time = $financial_year["start_date_time"];
        $last_date_time = $financial_year["last_date_time"];
        if (!$packing_type_id) {
            $this->packing_type_id = $this->packing_type_id;
        }
        $avg_actual_cost = self::
        join("packing_form_item", "packing_form_item.packing_form_id", "packing_form_actual_costs.packing_form_id")->
        where("packing_form_item.product_id", $this->product_id)->
        where("packing_form_actual_costs.packing_type_id", $packing_type_id)->
        where("packing_form_actual_costs.created_at", ">=", $start_date_time)->
        where("packing_form_actual_costs.created_at", "<", $last_date_time)->
        where("packing_form_actual_costs.status_id", 6060001)-> // بهای تمام شده محاسبه شده است.
        where("packing_form_actual_costs.actual_cost_type_id", $actual_cost_type_id)-> // بهای تمام شده
        selectRaw("sum(cost_of_one_unit * amount) as sum_cost_of_one_unit_amount, sum(amount) as sum_amount")->
        first();

        if (isset($avg_actual_cost->sum_amount)) {
            $number = round($avg_actual_cost->sum_cost_of_one_unit_amount / $avg_actual_cost->sum_amount);
            if ($number_format == "number_format") {
                return number_format($number);
            }
            return $number;

        }
        return "";

    }

    /**
     * بهای تمام شده بر اساس قیمت روز
     * @return void
     */
    public function costBaseOnCurrentDay($number_format = "")
    {
        return Product::GetCostBaseOnCurrentDay($this->product_id, $this->packing_type_id, $number_format);

    }
}
