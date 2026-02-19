<?php

namespace App\Models\LineProduct\GoodsKind;

use App\Models\LineProduct\GoodsKindProperty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindDisplayProperty extends Model {
    use HasFactory;

    protected $fillable = [ "goods_kind_id", "goods_kind_property_id" ];

    public function goods_kind_property() {
        return $this->belongsTo( GoodsKindProperty::class );
    }
}
