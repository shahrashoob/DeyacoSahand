<?php

namespace App\Models\LineProduct\Import;

use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportProductPurchase extends Model {
    use HasFactory;
    protected $table = "import_product_purchase";
    public function product() {
        return $this->belongsTo( Product::class );
    }
}
