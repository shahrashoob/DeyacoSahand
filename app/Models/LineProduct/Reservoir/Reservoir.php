<?php

namespace App\Models\LineProduct\Reservoir;

use App\Events\Form\PackingLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Models\Form\FormGeneralItem;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductReservoir;
use App\Models\Utility\Status;
use App\Models\Utility\Unit;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservoir extends Model
{
    use HasFactory;

    protected $fillable = ["caption", "reservoir_type_id", "unit_id", "capacity", "active_status_id", "warehouse_id", "packing_form_id"];

    public function reservoir_type()
    {
        return $this->belongsTo(ReservoirType::class);
    }

    public function products()
    {
        return $this->hasMany(Product\ProductReservoir::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function active_status()
    {
        return $this->belongsTo(Status::class);
    }

    public static function ExistsCode($caption, $id = false)
    {
        if ($id) {
            return ReservoirType::where("caption", $caption)->where("id", "!=", $id)->exists();
        }

        return ReservoirType::where("caption", $caption)->exists();
    }

    public function getAmount()
    {
        return $this->amount;
    }
    public function getRandom()
    {

        if ($this->random == null) {
            $this->random = Str::random(6);

            $this->save();
        }

        // ایجاد بسته بندی مخزن
        if (!$this->packing_form && $this->warehouse) {
            $packing_form_new = PackingForm::create([
                "packing_type_id" => 0,
                "carrier_id" => $carrier->id ?? null, //
                "status_id" => 7007002, // تحویل شده به انبار
            ]);

            $packing_form_new->warehouse_id = $this->warehouse_id;
            $packing_form_new->warehouse_status_id = 4201;
            $packing_form_new->save();

            $packing_form_new->getCode("DCRC/");
            $this->packing_form_id = $packing_form_new->id;
            $this->save();
        }
        return $this->random;
    }

    public function packing_form()
    {
        return $this->belongsTo(PackingForm::class);
    }


    public static function InjectToReservoir(Reservoir $reservoir, FormGeneralItem $form_general_item, $user_id, $new_amount, $sub_amount)
    {

        if (!$reservoir->warehouse_id) {
            return [
                "result" => false,
                "error" => "انبار مخزن مشخص نشده است."
            ];
        }


        // چک کردن کالای مجاز
        $exist_product = ProductReservoir::
        where("product_id", $form_general_item->product->id)->
        where("reservoir_id", $reservoir->id)->
        exists();
        if (!$exist_product) {

            return [
                "result" => false,
                "error" => "کالای " . $form_general_item->product->fullCaption() . " جزء کالاهای مجاز جهت ورود به مخزن نمی باشد."
            ];
        }

        // چک کردن لات و درجه
        if ($reservoir->packing_form) {
            $packing_form_item_reservoir = $reservoir->packing_form->items()->where("product_id", $form_general_item->product->id)->first();
            if (
                $packing_form_item_reservoir &&
                $packing_form_item_reservoir->degree_id != $form_general_item->degree_id
            ) {
                return [
                    "result" => false,
                    "error" => "درجه کالای در حال تزریق با درجه کالای موجود در مخزن متفاوت است و امکان تزریق برای " . $form_general_item->product->fullCaption() . " وجود ندارد."
                ];
            }
            if (
                $packing_form_item_reservoir &&
                $packing_form_item_reservoir->lot_number_id != $form_general_item->lot_number_id
            ) {
                return [
                    "result" => false,
                    "error" => "لات(همبافت) کالای در حال تزریق با لات(همبافت) کالای موجود در مخزن متفاوت است و امکان تزریق برای " . $form_general_item->product->fullCaption() . " وجود ندارد."
                ];
            }

            if (!$packing_form_item_reservoir) {
                $packing_form_item_reservoir = PackingFormItem::create([
                    "packing_form_id" => $reservoir->packing_form->id,
                    "product_id" => $form_general_item->product_id,
                    "lot_number_id" => $form_general_item->lot_number_id,
                    "degree_id" => $form_general_item->degree_id,
                    "amount" => 0,
                    "amount_after_control" => 0,
                    "final_amount" => 0,
                    "sub_amount" => 0,
                    "init_sub_amount" => 0,
                    "status_id" => 7006003, // بسته بندی شده
                    "band_code" => 1
                ]);
                $packing_form_item_reservoir->getCode(1, 1);
            }
        }


        // چک کردن ظرفیت مخزن
        $current_capacity = $reservoir->packing_form ? $reservoir->packing_form->getAmount() : 0;
        $capacity = ($current_capacity) + $new_amount;

        if ($capacity > $reservoir->capacity) {
            return [
                "result" => false,
                "error" => "با توجه به ظرفیت مخزن امکان تزریق بسته بندی به مخزن وجود ندارد." .
                    "<br/>موجودی مخزن:" . $current_capacity . " " . $form_general_item->product->unit->caption .
                    "<br/>" . "ظرفیت مخزن:" . $reservoir->capacity . " " . $form_general_item->product->unit->caption];
        }

        // ثبت ورود

        // به ازای بسته بندی اصلی یک ردیف در فرم ورود به انبار اضافه می کنیم.
        FormItem::create([
            "form_id" => $form_general_item->form->id,
            "general_form_item_id" => $form_general_item->id,
            "packing_form_item_id" => $packing_form_item_reservoir->id,
            "packing_type_id" => null,
            "product_id" => $form_general_item->product_id,
            "amount" => $new_amount,
            "sub_amount" => $sub_amount,
            "carrier_id" => null,
            "degree_id" => $form_general_item->degree_id,
            "lot_number_id" => $form_general_item->lot_number_id,
            "description" => "تزریق به مخزن " . " - " . ($packing_form_item_reservoir->code ?? ""),
        ]);

        $packing_form_item_reservoir->final_amount += $new_amount;
        $packing_form_item_reservoir->sub_amount += $sub_amount;
        $packing_form_item_reservoir->save();

        PackingForm::UpdateWeight($reservoir->packing_form);
        // لاگ
        event(new PackingLogEvent($reservoir->packing_form, 7007036, null, $new_amount, $form_general_item->form->id, $user_id));

        event(new PutInWarehouseEvent($form_general_item->form, null, null, true, $user_id));

        return [
            "retult" => true
        ];

    }

}
