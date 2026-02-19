<?php

namespace App\Models\Utility\Menu;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ButtonPost extends Model
{
    use HasFactory;
    use Loggable;
    protected $table="button_post";
    protected $fillable=["post_id","button_id","permission_type_id","other_id" ];

}
