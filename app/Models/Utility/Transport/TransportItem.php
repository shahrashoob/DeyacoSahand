<?php

namespace App\Models\Utility\Transport;

use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TransportItem extends Model
{
    use HasFactory;

    protected $table = "transport_item";
    protected $fillable = ["code", "transport_id", "user_id", "status_id", "product_request_form_id"];

    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }

    public function product_request_form()
    {
        return $this->belongsTo(ProductRequestForm::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function transport_packing_list()
    {
        return $this->hasMany(TransportPackingForm::class);
    }

    /**
     * جمع کل مقدار بسته بندی هایی که داخل بسته بندی حمل و نقل قرار دارد.
     * @return int
     */
    public function transport_packing_list_sum_amount()
    {
        $sum_amount = 0;
        foreach ($this->transport_packing_list as $transport_packing_form) {
            $sum_amount += $transport_packing_form->packing_form->getAmount();
        }
        return $sum_amount;
    }

    public function code()
    {
        $this->getRandom();

        if ($this->code == null) {
            $this->code = "DCLP/" . (1000 + $this->id);// Bill of Loading
            $this->save();
        }

        return $this->code;
    }

    public function codeNumber()
    {
        return (1000 + $this->id);//Loading Packing
    }

    public function getRandom()
    {
        if ($this->random == null) {
            $this->random = Str::random(6);
            $this->save();
        }

        return $this->random;
    }

    public function create_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }

    public function getAmount($type)
    {
        $packing_form_ids = $this->transport_packing_list()->pluck("packing_form_id");

        return PackingFormItem::whereIn("packing_form_id", $packing_form_ids)->sum($type);
    }

    public function confirm()
    {
        if ($this->transport_packing_list()->count() == 0) {
            return ["result" => false, "message" => "بسته بندی " . $this->code() . " خالی می باشد."];
        }
        if ($this->status_id != 6010001) {
            return ["result" => false, "message" => "این بسته بندی هنوز تایید موقت نشده است."];

        }

        $this->status_id = 6010002; // ثبت نهایی
        $this->save();

        return ["result" => true,];
    }
}
