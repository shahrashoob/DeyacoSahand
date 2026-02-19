<?php

namespace App\Models\LineProduct\Carrier;

use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrierProduct extends Model
{
    use HasFactory;
    protected $table="carrier_product";
    protected $fillable=["product_id","carrier_id"];
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
