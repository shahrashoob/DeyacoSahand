<?php

namespace App\Models\LineProduct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductType extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable=["caption","goods_kind_id"];
    public $timestamps = false;
    public static function GetIdFromCaption($caption){

        $pt=ProductType::where("caption","like","%".$caption."%")->first();
        return  isset($pr)?$pt->id : NULL;
    }

    public function goods_kind()
    {
        return $this->belongsTo(GoodsKind::class,"goods_kind_id","id");
    }

    public static function ExistsCode($caption,GoodsKind $goods_kind,$id=false){
        if($id){
            return ProductType::where(["caption"=>$caption,"goods_kind_id"=>$goods_kind->id])->where("id","!=",$id)->exists();
        }
        return ProductType::where(["caption"=>$caption,"goods_kind_id"=>$goods_kind->id])->exists();
    }
}
