<?php

namespace App\Models\Form;

use App\Models\Form\Packing\PackingForm;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormGeneralItemPackingForm extends Model
{
    use HasFactory;

    protected $table = 'form_general_item_packing_forms';

    protected $fillable = ['packing_form_id', 'form_id', 'form_general_item_id', 'check_quality_status_id'];

    public function packing_form()
    {
        return $this->belongsTo(PackingForm::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function form_general_item()

    {
        return $this->belongsTo(FormGeneralItem::class);

    }

    public function check_quality_status()
    {
        return $this->belongsTo(Status::class);
    }

    public static function CheckQualityStatus(FormGeneralItem $form_general_item)
    {
        $product = $form_general_item->product;

        // واحد اصلی: غیر وزنی
        // واحد فرعی ندارد یا واحد فرعی غیر وزنی است
        if ($product->unit->weight_conversion_rate != 0 || !$product->sub_unit || ($product->sub_unit && $product->sub_unit->weight_conversion_rate == 0)) {
            return [
                "result" => false,
                "error" => "الگوریتم کنترل کیفی با توجه به واحد اصلی و واحد فرعی کالا قابلیت اجرا ندارد، امکان محاسبه گزماژ کالا وجود ندارد."
            ];
        }

        $number_check_of_quality_control = $product->goods_kind->number_check_of_quality_control_input_warehouse;
        $percent_check_of_quality_control = $product->goods_kind->percent_check_of_quality_control_input_warehouse;
        $kgInMPackingForm = [];
        foreach ($form_general_item->form_general_item_packing_form as $item) {

            $amount = $item->packing_form->getFinalAmount();
            $sub_amount = $item->packing_form->getSubAmount();

            $kgInM = $sub_amount / $amount; // گزماژ پارچه
            $kgInMPackingForm[$item->id] = $kgInM;
        }
        if (count($kgInMPackingForm) == 0) {
            return [
                'result' => true,
                "mean" => 0,
                "number_check_of_quality_control" => $number_check_of_quality_control,
                "number_checked" => 0,
                "quality_confirmed" =>false
            ];
        } else {
            $mean = round(array_sum($kgInMPackingForm) / count($kgInMPackingForm), 4);
        }
        $number_checked = 0;
        foreach ($form_general_item->form_general_item_packing_form as $item) {

            $kgInM = $kgInMPackingForm[$item->id];

            if (abs(($kgInM - $mean) / $mean * 100) <= $percent_check_of_quality_control) {
                $item->check_quality_status_id = 5003002; // تایید
                $number_checked++;
            } else {
                $item->check_quality_status_id = 5003003; // عدم تایید
            }

            $item->save();
        }

        return [
            'result' => true,
            "mean" => $mean,
            "number_check_of_quality_control" => $number_check_of_quality_control,
            "number_checked" => $number_checked,
            "quality_confirmed" => $number_checked >= $number_check_of_quality_control
        ];
    }
}
