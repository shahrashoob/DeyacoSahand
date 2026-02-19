<?php

namespace App\Models\LineProduct\Product\ProductRequestPermission;

use App\Models\Utility\Address\Address;
use App\Models\Utility\Car\CarType;
use App\Models\Utility\Car\DeliveryPointType;
use App\Models\Utility\Car\ShippingMethod;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductRequestPermission extends Model
{

    use HasFactory;
    protected $table="product_request_permissions";
    protected $fillable = [
        "code",
        "status_id",
        "address_id",
        "shipping_method_id",
        "car_type_id",
        "insurance_amount",
        "delivery_point_type_id",
        "shipping_cost", // هزینه ارسال بار
    ];

    public function items()
    {
        return $this->hasMany(ProductRequestPermissionItem::class);
    }


    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function shipping_method()
    {
        return $this->belongsTo(ShippingMethod::class);
    }
    public function car_type()
    {
        return $this->belongsTo(CarType::class);
    }

    public function delivery_point_type()
    {
        return $this->belongsTo(DeliveryPointType::class);
    }
    public function create_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }
    public function getCode()
    {
        if (isset($this->code)) {
            return $this->code;
        }

// Request Product
        $this->code = "DCDL/" . (1000 + $this->id); // Download License
        $this->save();

        return $this->code;
    }

    public function address()
    {
        return $this->belongsTo(Address::class,"address_id");
    }
}
