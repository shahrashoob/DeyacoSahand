<?php

namespace App\Models\LineProduct\GoodsKind;

use App\Models\Post\PostUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\CollectionModify;

class GoodsKindPost extends Model
{
    use HasFactory;
    protected $table="goods_kind_post";
    protected $fillable=["post_id","goods_kind_id"];

    public static function getAllowedGoodsKindId(){
       // $post_ids    = \Auth::user()->posts->pluck( "post_id" );
        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime( "post_ids");
        $goods_kind_ids   =
            GoodsKindPost::
            whereIn( "post_id", $post_ids )->
            pluck( "goods_kind_id" )->
            toArray();
        $goods_kind_ids[] = - 1;

        return $goods_kind_ids;
    }
}
