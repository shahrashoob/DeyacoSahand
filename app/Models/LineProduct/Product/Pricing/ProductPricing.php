<?php

namespace App\Models\LineProduct\Product\Pricing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPricing extends Model
{
    use HasFactory;
    protected $table = "product_pricing";
    protected $fillable = ["product_id", "packing_type_id", "cost_of_one_unit"];
}
