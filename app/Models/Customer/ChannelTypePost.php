<?php

namespace App\Models\Customer;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelTypePost extends Model
{
    use HasFactory;
    use Loggable;
    protected $table="channel_type_post";
}
