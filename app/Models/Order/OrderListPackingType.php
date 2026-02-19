<?php

namespace App\Models\Order;

use App\Models\LineProduct\Packing\PackingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderListPackingType extends Model
{
    use HasFactory;
    protected $table="order_list_packing_type";
    protected $fillable=["order_id","order_list_id","packing_type_id"];

    public function packing_type() {
        return $this->belongsTo( PackingType::class);
    }
    public function order_list() {
        return $this->belongsTo( OrderList::class);
    }
}
