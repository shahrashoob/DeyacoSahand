<?php

namespace App\Models\LineProduct\Product;

use App\Models\LineProduct\Packing\PackingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPackingType extends Model {
    use HasFactory;

    protected $table = "packing_type_product";

    protected $fillable = [ "product_id", "packing_type_id", "is_it_salable" ];

    public function packing_type() {
        return $this->belongsTo( PackingType::class );
    }
}
