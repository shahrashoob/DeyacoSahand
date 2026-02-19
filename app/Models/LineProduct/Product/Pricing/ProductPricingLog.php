<?php

namespace App\Models\LineProduct\Product\Pricing;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPricingLog extends Model
{
    use HasFactory;
    protected $table = "product_pricing_logs";
    protected $fillable = ["message_id", "user_id"];

    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id");
    }
}
