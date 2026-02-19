<?php

namespace App\Models\Utility\Address;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvincePost extends Model
{
    use HasFactory;
    use Loggable;
    protected $table="post_province";

}
