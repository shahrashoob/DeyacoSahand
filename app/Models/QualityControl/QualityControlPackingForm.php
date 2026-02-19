<?php

namespace App\Models\QualityControl;

use App\Models\Accounting\CostCenter;
use App\Models\Form\Packing\PackingForm;
use App\Models\HR\Company\Company;
use App\Models\File\File;
use App\Models\HR\Personal\PersonalType;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\SupplyType;
use App\Models\Post\Post;
use App\Models\Utility\Address\Address;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QualityControlPackingForm extends Model
{
    use HasFactory;

    protected $table = "quality_control_packing_forms";
    protected $fillable = [
        'packing_form_id',
        'packing_form_item_id',
        'production_id',
        'production_form_id',
        'production_form_item_id',
        'product_id',
        'degree_id',
        'lot_number_id',
        'band_code',
        'start_point',
        'end_point',
        'amount',
        'amount_after_control',
        'final_amount',
        'sub_amount',
    ];


    public static function GetOperator(PackingForm $packingForm, $amount)
    {
        $packing_form_item = $packingForm->items()->first();

        $production_form_item = $packing_form_item->production_form_item;
        if (!$production_form_item) {
            return [
                "result" => false,
                "error" => "فرم تولید مرتبط با بسته بندی " . $packing_form_item->code . " وجود ندارد."
            ];
        }


        $start_machine_log = $production_form_item->start_machine_log;
        $end_of_machine_log = $production_form_item->end_of_machine_log;

        if (!$start_machine_log) {
            return [
                "result" => false,
                "error" => "لاگ شروع برای آیتم فرم تولید " . $production_form_item->code() . "با بسته بندی " . $packing_form_item->code . " وجود ندارد."
            ];
        }
        if (!$end_of_machine_log) {
            return [
                "result" => false,
                "error" => "لاگ پایان برای آیتم فرم تولید " . $production_form_item->code() . "با بسته بندی " . $packing_form_item->code . " وجود ندارد."
            ];
        }

        $final_shrinkage_percent = $packingForm->final_shrinkage_percent();

        if ($final_shrinkage_percent > 1000 || $final_shrinkage_percent < -1000) {
            return [
                "result" => false,
                "error" => "درصد جمع شدگی به دست آمده برای بسته بندی " . $packing_form_item->code . " نا معتبر است."
            ];
        }


        $init_amount = $amount * (1 + $final_shrinkage_percent / 100);
        $tarakom_pod = GoodsKindPropertyValue::where("product_id", $packing_form_item->product_id)->whereIn("goods_kind_property_id", [
            220381 // تراکم نهایی پود (تئوری)
        ])->sum("value");

        $pics = $init_amount * $tarakom_pod * 100;

        if ($pics + $start_machine_log->contour_sum_value > $end_of_machine_log->contour_sum_value) {
            return [
                "result" => false,
                "با توجه به مقدار وارد شده جهت به دست آوردن اپراتور مقدار $amount برای بسته بندی " . $packing_form_item->code . " نامعتبر است."
            ];
        }

        $operator_piks = $pics + $start_machine_log->contour_sum_value;


        // پیدا کردن پیک ماشین از روی لاگ

        $machine_log = MachineLog::
        where("contour_sum_value", "<=", $operator_piks)->
        where("contour_sum_value", ">=", $start_machine_log->contour_sum_value)->
        where("contour_sum_value", "<=", $end_of_machine_log->contour_sum_value)->
        where("machine_id", $production_form_item->production_form->machine_id)->
        orderByDesc("contour_sum_value")->first();

        if($machine_log && $machine_log->operator) {
            return [
                "result" => true,
                "machine_log" => $machine_log,
                "operator_id" => $machine_log->operator_id ?? null,
                "operator_fullname" => $machine_log->operator->fullname()
            ];
        }
        else{
            return [
                "result" => false,
                "error"=>"اپراتور ماشین مشخص نشده است."
            ];
        }


    }

}