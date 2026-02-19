<?php

namespace App\Models\LineProduct\GoodsKind;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindClassificationOption extends Model
{
    use HasFactory;
    protected $fillable=["caption","goods_kind_classification_id"];

    public function goods_kind_classification(){
        return $this->belongsTo(GoodsKindClassification::class,"goods_kind_classification_id");
    }
    public static function ExistsCaption( $caption, $goods_kind_classification, $id = false ){
        if ( $id ) {
            return GoodsKindClassificationOption::where( [
                "caption"       => $caption,
                "goods_kind_classification_id" => $goods_kind_classification->id
            ] )->where( "id", "!=", $id )->exists();
        }

        return GoodsKindClassificationOption::where( [ "caption" => $caption, "goods_kind_classification_id" => $goods_kind_classification->id ] )->exists();
    }
}
