<?php

namespace App\Models\LineProduct\Product\ConsumedProduct;

use App\Models\LineProduct\Product;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class   ConsumedProduct extends Model {
    use HasFactory;

    protected $fillable = [ "product_id", "material_id","in_ordering_customer_can_choose","product_creation_process_id","status_id" ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function material(){
        return $this->belongsTo(Product::class,"material_id");
    }
    public function product_creation_process(){
        return $this->belongsTo(Product\ProductCreation\ProductCreationProcess::class);
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
}
