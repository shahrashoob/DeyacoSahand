<?php

namespace App\Models\Warehouse\WarehouseHandling;

use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseHandlingProduct extends Model
{
    use HasFactory;
    protected $table="warehouse_handling_product";
    protected $fillable=["warehouse_handling_id","product_id"];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function warehouse_handling(){
        return $this->belongsTo(WarehouseHandling::class);
    }
}
