<?php

namespace App\Models\LineProduct\GoodsKind;

use App\Models\LineProduct\GoodsKindPropertyOption;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindPropertyDependentValue extends Model
{
    use HasFactory;
    protected $table="goods_kind_property_dependent_values";
    protected $fillable=["goods_kind_property_id","goods_kind_property_parent_id","parent_value","compare"];

    public function option(){
        return $this->belongsTo(GoodsKindPropertyOption::class,"parent_value");
    }
}
