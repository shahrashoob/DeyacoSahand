<?php

namespace App\Models\LineProduct\Import;

use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportProductPricing extends Model
{
    use HasFactory;
    protected $table = "import_product_pricing";

    public function product() {
        return $this->belongsTo( Product::class );
    }   public function packing_type() {
        return $this->belongsTo( PackingType::class );
    }
}
