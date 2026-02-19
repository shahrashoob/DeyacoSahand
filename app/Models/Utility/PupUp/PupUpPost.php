<?php

namespace App\Models\Utility\PupUp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PupUpPost extends Model
{
    use HasFactory;
    protected $table="pup_up_post";
    protected $fillable=["post_id","pup_up_id","user_id"];
}
