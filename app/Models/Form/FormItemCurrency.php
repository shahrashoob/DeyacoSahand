<?php

namespace App\Models\Form;

use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LineProduct\Product;

class FormItemCurrency extends Model {
    use HasFactory;
    protected $table = "form_item_currency";
    protected $fillable = [
        "form_id",
        "form_item_id",
        "product_id",
        "amount",
        "price",
        "fea",
        "total_off_price",
        "total_price",
        "tax_percent",
        "tax_price",
        "total_price_with_tax"
    ];


    public function product() {
        return $this->belongsTo( Product::class, "product_id", "id" );
    }
    public function form(){
        return $this->belongsTo(Form::class);
    }
}
