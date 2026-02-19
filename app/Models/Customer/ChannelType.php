<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelType extends Model
{
    use HasFactory;
    protected $table="channel_types";
    public static function GetIdFromCaption($caption){

        $channel=ChannelType::where("caption","like","%".$caption."%")
        ->first();
        return  isset($channel)?$channel->id : -100;

    }
}
