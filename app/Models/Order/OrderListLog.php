<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderListLog extends Model
{
    use HasFactory;
    protected  $table="order_list_logs";
    protected  $fillable=["erp_status_id","message_id","user_id","order_list_id"];
}
