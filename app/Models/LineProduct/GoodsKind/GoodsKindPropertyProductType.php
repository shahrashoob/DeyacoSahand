<?php

namespace App\Models\LineProduct\GoodsKind;

use App\Models\LineProduct\GoodsKindPropertyOption;
use App\Models\LineProduct\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindPropertyProductType extends Model
{
    use HasFactory;
    protected $table="goods_kind_property_product_type";
    protected $fillable=["goods_kind_id","goods_kind_property_id","product_type_id"];
    public function product_type(){
        return $this->belongsTo(ProductType::class);
    }
    public function property_option(){
        return $this->belongsTo(GoodsKindPropertyOption::class);
    }
}
