<?php

namespace App\Models\Modules\FabricRaw\ProductionCard;

use App\Models\LineProduct\Product;
use App\Models\Order\Order;
use App\Models\Order\OrderList;
use App\Models\Production\Production;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreateProductionCard extends Model
{
    use HasFactory;
    use Loggable;
    public static function call($new_order_list){

        $production=new Production();
        $production->order_list_id=$new_order_list->id;
        $production->customer_id=$new_order_list->customer_id;
        $production->product_id=$new_order_list->product_id;
        $production->number=$new_order_list->amount;
        $production->number_in_carton=$new_order_list->number_in_carton;
        $production->version	=1;
        $production->nth_in_day	=Production::where("created_at", ">",Carbon::yesterday()->format('Y-m-d'))->count()+1;
        $production->status_id=500;
        $production->waiting_status_id=500010;
        $production->prioriry_id=$request->priority_id;

        $production->save();
    }
}
