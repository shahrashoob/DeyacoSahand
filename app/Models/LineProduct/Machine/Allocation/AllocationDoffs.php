<?php

namespace App\Models\LineProduct\Machine\Allocation;

use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Packing\PackingType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocationDoffs extends Model
{
    use HasFactory;

    protected $table = "allocation_doffs";
    protected $fillable = [
        "allocation_id",
        "packing_type_id",
        "max_number_of_doffs",
        "amount_of_each_doffs",
        "number_of_doffs_done",
        "number_of_brand_done",
        "number_of_brand",
    ];

    public function packing_type()
    {
        return $this->belongsTo(PackingType::class);
    }


    public function allocation_brands()
    {
        return $this->hasMany(AllocationBrand::class,"allocation_doff_id");
    }

    public static function AddList($allocation_id, $list)
    {
        $doff_number = 0;
        AllocationBrand::where("allocation_id", $allocation_id)->delete();
        foreach ($list as $item) {
            $item["allocation_id"] = $allocation_id;
            $brand_info = isset($item["brand_info"]) ? $item["brand_info"] : null;
            unset($item["brand_info"]);
            $allocation_doff = AllocationDoffs::create($item);


            $doff_number++;
            if ($brand_info) {
                foreach ($brand_info["brand_amount_list"] as $brand_amount) {
                    AllocationBrand::create([
                        "allocation_id" => $allocation_id,
                        "allocation_doff_id" => $allocation_doff->id,
                        "amount_of_brand" => $brand_amount,
                        "doff_number" => $doff_number,
                    ]);
                }
            }
        }

    }

    public static function HasAnyDoff($allocation_id, $packing_type_id)
    {
        $doff = AllocationDoffs::where([
            "allocation_id" => $allocation_id,
            "packing_type_id" => $packing_type_id,
        ])->first();

        if (!$doff) {
            return [
                "result" => false,
                "error" => "اطلاعات داف های تخصیص یافت نشد، لطفا با پشتیبانی تماس بگیرید."
            ];
        }
        if ($doff->max_number_of_doffs - $doff->number_of_doffs_done >= 1) {
            return [
                "result" => true,
                "allocation_doff" => $doff
            ];
        } else {
            return [
                "result" => false,
                "error" => "تعداد داف های انجام شده برای " . $doff->packing_type->caption . " برابر با " . $doff->max_number_of_doffs . " داف می باشد و همه این داف ها انجام شده است."
            ];
        }

    }


    public static function DoffDownAndGetNextDoff($allocation_id, $max_number_of_doffs = 1, $number_of_doffs_done = 0)
    {
        $list = AllocationDoffs::where(
            ["allocation_id" => $allocation_id,
                "max_number_of_doffs" => $max_number_of_doffs,
                "number_of_doffs_done" => $number_of_doffs_done
            ])->
        orderBy("id")->
        get();

        if ($list->count() > 0) {
            $first_item = $list->get(0);

            $first_item->number_of_doffs_done = 1;
            $first_item->save();
        }
        // اولین داف را یکی اضافه می کنیم و دومین داف را بر می گردانیم.

        if ($list->count() > 1) {
            $secondItem = $list->get(1);
        } else {
            $secondItem = null; // or handle differently
        }

        return [
            "result" => true,
            "new_amount_of_each_doffs" => $secondItem->amount_of_each_doffs ?? null,
        ];
    }

}
