<?php

namespace App\Models\Production;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionDetailsReport extends Model
{
    use HasFactory;

    protected $fillable = ["user_id", "production_id", "product_id", "parent_production_id"];
    protected $table = "production_details_reports";
    public static $max = 200;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function parent_production()
    {
        return $this->belongsTo(Production::class, "parent_production_id");
    }

    public function parent_product()
    {
        return $this->belongsTo(Product::class, "parent_product_id");
    }

    public static function CompleteData($user_id)
    {
        $list = self::where("product_id", 0)->where("user_id", $user_id)->take(self::$max)->with("production")->get();
        $production_ids = [];
        foreach ($list as $production_details_report) {
            $production = $production_details_report->production;
            $production_details_report->product_id = $production->product_id;

            $production_ids[] = $production_details_report->production_id;

            $production_details_report->parent_production_id = $production->parent_production_id ?? null;
            $production_details_report->parent_product_id = $production->parent_production->product_id ?? null;
            if ($production_details_report->parent_production_id) {
                $production_ids[] = $production_details_report->parent_production_id;

                $production_details_report->parent_production_amount = $production->parent_production->number;
            }

            $order_list_carton =
                isset($production->parent_production->order_list) ?
                    $production->parent_production->order_list->carton :
                    (isset($production->order_list) ? $production->order_list->carton : $production->number);

            $production_details_report->order_amount = $order_list_carton;
            $production_details_report->production_amount = $production->number;

            $production_details_report->save();
        }

        $production_ids[] = -1;
        $allocation_amounts = self::GetAllocationList($production_ids);
        $production_form_amounts = self::GetProductionAmountList($production_ids);

        foreach ($list as $production_details_report) {

            $production_details_report->allocation_amount = isset($allocation_amounts[$production_details_report->production_id]) ?
                $allocation_amounts[$production_details_report->production_id] : 0;

            $production_details_report->production_form_amount = isset($production_form_amounts[$production_details_report->production_id]) ?
                $production_form_amounts[$production_details_report->production_id]["amount"] : 0;

            if ($production_details_report->parent_production_id) {
                $production_details_report->parent_allocation_amount = isset($allocation_amounts[$production_details_report->parent_production_id]) ?
                    $allocation_amounts[$production_details_report->parent_production_id] : 0;;

                $production_details_report->parent_production_form_amount = isset($production_form_amounts[$production_details_report->parent_production_id]) ?
                    $production_form_amounts[$production_details_report->parent_production_id]["amount"] : 0;
            }

            $production_details_report->save();

        }

        return [
            "result" => true,
            "count" => count($list),
        ];
    }

    public static function GetAllocationList($production_ids)
    {
        return MachineAllocation::whereIn("production_id", $production_ids)->
        whereIn("status_id", MachineAllocation::GetAllAllocationList())->
        whereNull("parent_allocation_id")->
        selectRaw("production_id, sum(allocation_amount) as allocation_amount")->
        groupBy("production_id")->
        pluck("allocation_amount", "production_id")->
        toArray();
    }

    public static function GetProductionAmountList($production_ids)
    {
       return $list= ProductionFormItem::whereIn("production_id", $production_ids)->
        join("production_forms", "production_forms.id", "=", "production_form_id")->
        join("machines", "machines.id", "=", "machine_id")->
        join("machine_types", "machine_types.id", "=", "machine_type_id")->
        selectRaw("production_id, sum(round(amount,2)) as amount")->
        whereNull("source_production_form_item_id")-> // اولین فرمی است که برای کارت ایجاد شده است.
        whereNull("packing_form_item_id")-> // کد آیتم بسته بندی که معادل آن آیتم فرم تولید ایجاد شده است.
        whereNotIn("production_form_item.status_id", ProductionForm::StatusNotValidForProductionAmount())-> // تزریق شده به ماشین باید حذف شود
        groupBy("production_id","machine_type_id")->
        select("amount", "production_id","machine_type_id","machine_types.caption")->

        get()->keyBy("production_id")->toArray();

    }
}
