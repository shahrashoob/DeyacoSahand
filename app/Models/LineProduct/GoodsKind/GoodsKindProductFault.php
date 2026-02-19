<?php

namespace App\Models\LineProduct\GoodsKind;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Product\Fault\ProductFault;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindProductFault extends Model
{
    use HasFactory;
    protected $table="goods_kind_product_fault";
    protected $fillable=["goods_kind_id","product_fault_id"];

    public function product_fault(){
        return $this->belongsTo(ProductFault::class);
    }
    public function goods_kind(){
        return $this->belongsTo(GoodsKind::class);

    }
}
