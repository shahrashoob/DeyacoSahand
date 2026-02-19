<?php

namespace App\Models\LineProduct\GoodsKind;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindPropertyPost extends Model {
    use HasFactory;
    public $timestamps = false;
    protected $table = "goods_kind_property_post";
    protected $fillable = [ "post_id", "goods_kind_id", "goods_kind_property_id" ];

}
