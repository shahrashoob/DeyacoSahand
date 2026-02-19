<?php

namespace App\Models\LineProduct\Import;

use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\DocBlock\Tags\Property;

class ImportProductProperty extends Model {
    use HasFactory;
    protected $table = "import_product_property";
    protected $fillable = [ "product_code", "goods_kind_id", "error" ];

    public function product() {
        return $this->belongsTo( Product::class );
    }
    public function property() {
        return $this->belongsTo( GoodsKindProperty::class ,"property_id","id");
    }
}
