<?php

namespace App\Models\Order;

use App\Models\Customer\Customer;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFactor extends Model
{
    use HasFactory;
    use Loggable;

    protected $table = "order_factor";
    protected $fillable = [
        "order_id",
        "order_list_id",
        "carton",
        "product_id",
        "service_id",
        "customer_id",
        "number_in_carton",
        "degree_id",
        "packing_type_id",
        "increase_deadline_per_day",
        "increase_percentage_deadline_per_day"
    ];


    public function order()
    {
        return $this->belongsTo(Order::class, "order_id", "id");

    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, "customer_id", "id");

    }

    public function product()
    {
        return $this->belongsTo(Product::class, "product_id", "id");
    }

    public function service()
    {
        return $this->belongsTo(Product::class, "service_id");
    }

    public function degree()
    {
        return $this->belongsTo(Degree::class);
    }

    public function orderListItem()
    {
        return $this->belongsTo(OrderList::class, "order_list_id", "id");
    }

    public function getCaption()
    {
        if ($this->service_id) {
            return $this->service->caption . " (برای " . $this->product->caption . ")";
        }
        return $this->product->caption;
    }

    public function getPackingType($type = "caption")
    {
        if (!$this->orderListItem) {
            return "***";
        }

        return $this->orderListItem->getPackingType($type);
    }


}
