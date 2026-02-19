<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderType extends Model
{
    use HasFactory;
    public static function GetIdFromCaption($caption){

        $order=OrderType::where("caption","like","%".$caption."%")->first();
        return  isset($order)?$order->id : -100;

    }
}
