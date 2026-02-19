<?php

namespace App\Models\LineProduct\Product\Waste;

use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WasteCollection extends Model {
    use HasFactory;

    protected $fillable=["user_id","product_id","gross_weight","weight","amount","packing_form_id"];

    public function product() {
        return $this->belongsTo( Product::class );
    }
    public function packing_form() {
        return $this->belongsTo( PackingForm::class);
    }

}
