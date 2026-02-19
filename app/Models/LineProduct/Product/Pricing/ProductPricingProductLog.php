<?php

namespace App\Models\LineProduct\Product\Pricing;

use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPricingProductLog extends Model
{
    use HasFactory;

    protected $table = "product_pricing_product_logs";
    protected $fillable = ["product_pricing_log_id", "product_id", "packing_type_id", "cost_of_one_unit"];

    public function product_pricing_log()
    {
        return $this->belongsTo(ProductPricingLog::class, "product_pricing_log_id");
    }

    public function get_datetime()
    {
        return jdate(Carbon::parse($this->order_datetime)->timestamp)->format('H:i Y/m/d ');

    }
}
