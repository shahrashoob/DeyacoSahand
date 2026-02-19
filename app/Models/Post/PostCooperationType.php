<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCooperationType extends Model {
    use HasFactory;

    protected $table = "post_cooperation_type";
    protected $fillable = [ "post_id", "cooperation_type_id" ];
}
