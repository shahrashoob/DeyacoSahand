<?php

namespace App\Models\LineProduct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplaceProduct extends Model {
    use HasFactory;

    protected $fillable = [ "product_id", "replace_product_id" ];

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function replace_product() {
        return $this->belongsTo( Product::class, "replace_product_id" );
    }

    public function replace_type() {
        return $this->belongsTo( ReplaceType::class );
    }
}
