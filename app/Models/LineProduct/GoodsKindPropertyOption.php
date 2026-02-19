<?php

namespace App\Models\LineProduct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindPropertyOption extends Model
{
    use HasFactory;
    protected $table="goods_kind_property_options";
    public static function ExistsCode( $goods_kind_property_id, $caption, $id =false) {
        if ( $id ) {
            return GoodsKindPropertyOption::where( "caption", $caption )->where( "id", "!=", $id )->where("goods_kind_property_id",$goods_kind_property_id)->exists();
        }

        return GoodsKindPropertyOption::where( "caption", $caption )->where("goods_kind_property_id",$goods_kind_property_id)->exists();
    }
}
