<?php

namespace App\Models\Post;

use App\Models\Utility\SmartObject;
use Illuminate\Database\Eloquent\Model;


class PostSmartObject extends Model
{
    protected $table = "post_smart_object";
    protected $fillable = [
        "post_id",
        "smart_object_id",
    ];

    public function smart_object()
    {
        return $this->belongsTo(SmartObject::class);
    }
}
