<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderListImportantStatus extends Model
{
    use HasFactory;
    protected $table="order_list_important_status";
    protected $fillable=[
        "order_id",
        "customer_id",
        "production_serial",
        "event_304010_at",
        "event_304020_at",
        "event_304030_at",
        "event_304040_at",
        "event_304060_at",
        "event_304070_at",
        "event_304075_at",
        "event_304080_at"
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }
    public function customer(){
        return $this->belongsTo(Order::class);
    }

}
