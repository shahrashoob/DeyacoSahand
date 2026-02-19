<?php

namespace App\Models\LineProduct\GoodsKind;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindPackingType extends Model
{
    use HasFactory;
    protected $table="goods_kind_packing_type";
    protected $fillable=["goods_kind_id","packing_type_id"];
}
