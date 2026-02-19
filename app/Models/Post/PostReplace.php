<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostReplace extends Model
{
    use HasFactory;
    protected $table="post_replace";
    protected $fillable=["post_id","replace_post_id"];
    public $timestamps=false;

    public function replace_post(){
        return $this->belongsTo(Post::class,"replace_post_id");
    }
}
