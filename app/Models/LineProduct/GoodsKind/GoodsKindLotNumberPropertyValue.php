<?php

namespace App\Models\LineProduct\GoodsKind;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindLotNumberPropertyValue extends Model {
    use HasFactory;

    protected $fillable = [ "product_id", "lot_number_id", "goods_kind_lot_number_property_id", "value" ];
    protected $table = "goods_kind_lot_number_property_value";

}
