<?php

namespace App\Models\LineProduct\GoodsKind;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindClassificationProduct extends Model
{
    use HasFactory;
    protected $table="goods_kind_classification_product";
    protected $fillable=["product_id","goods_kind_classification_id","goods_kind_classification_option_id"];

    public function goods_kind_classification(){
        return $this->belongsTo(GoodsKindClassification::class,"goods_kind_classification_id");
    }

    public function goods_kind_classification_option(){
        return $this->belongsTo(GoodsKindClassificationOption::class,"goods_kind_classification_option_id");
    }
}
