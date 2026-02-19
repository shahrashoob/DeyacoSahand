<?php

namespace App\Models\LineProduct\GoodsKind;

use App\Models\LineProduct\GoodsKind;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindClassification extends Model
{
    use HasFactory;
    protected $fillable=["caption","goods_kind_id","goods_kind_classification_type_id"];
    public function classification_type(){
        return $this->belongsTo(GoodsKindClassificationType::class,"goods_kind_classification_type_id");
    }
    public function classification_option(){
        return $this->hasMany(GoodsKindClassificationOption::class,"goods_kind_classification_id");
    }
    public function goods_kind(){
        return $this->belongsTo(GoodsKind::class);

    }
    public static function ExistsCaption( $caption, $goods_kind_id, $id = false ){
        if ( $id ) {
            return GoodsKindClassification::where( [
                "caption"       => $caption,
                "goods_kind_id" => $goods_kind_id
            ] )->where( "id", "!=", $id )->exists();
        }

        return GoodsKindClassification::where( [ "caption" => $caption, "goods_kind_id" => $goods_kind_id ] )->exists();
    }
}
