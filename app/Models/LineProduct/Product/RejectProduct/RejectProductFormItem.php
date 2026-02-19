<?php

namespace App\Models\LineProduct\Product\RejectProduct;

use App\Models\Form\Packing\PackingForm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectProductFormItem extends Model
{
    use HasFactory;
    protected $fillable=["reject_product_form_id","packing_form_id","packing_is_safe","amount_remaining"];

    public function reject_product_form(){
        return $this->belongsTo(RejectProductForm::class);
    }
    public function packing_form(){
        return $this->belongsTo(PackingForm::class);
    }
}
