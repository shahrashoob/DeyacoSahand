<?php

namespace App\Models\Accounting\Tariff;

use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTariffLog extends Model
{

    use HasFactory;

    protected $table = "product_tariff_logs";
    protected $fillable = [
        "tariff_log_id",
        "tariff_id",
        "product_id",
        "service_id",
        "fea",
        "min_buy",
        "max_buy",
        "tax",
        "fare",
        "consumer_price",
        "increase_percentage_deadline_per_day",
        "packing_type",
        "customer_product_caption",
        "customer_product_code"
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function service()
    {
        return $this->belongsTo(Product::class, "service_id");
    }

    public function getCaption()
    {
        if ($this->service_id) {
            return $this->service->caption . " (برای " . $this->product->caption . ")";
        }
        return $this->product->caption;
    }

    public function degree()
    {
        return $this->belongsTo(Degree::class);
    }

    public function packing_type()
    {
        return $this->belongsTo(PackingType::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
