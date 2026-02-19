<?php

namespace App\Models\LineProduct\Product;

use App\Models\LineProduct\Product;
use App\Models\LineProduct\Reservoir\Reservoir;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReservoir extends Model
{
    use HasFactory;
    protected $table="product_reservoir";
    protected $fillable=["product_id","reservoir_id"];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function reservoir(){
        return $this->belongsTo(Reservoir::class);
    }
}
