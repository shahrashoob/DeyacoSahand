<?php

namespace App\Models\LineProduct\Product\Waste;

use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductWaste extends Model {
    use HasFactory;
    protected $table="product_waste";
    protected $fillable=["product_id","waste_id","waste_type_id","product_route_id","number","percent"];

    public function product() {
        return $this->belongsTo( Product::class );
    }
    public function waste() {
        return $this->belongsTo( Product::class,"waste_id");
    }
    public function waste_type() {
        return $this->belongsTo( WasteType::class);
    }
    public function product_route() {
        return $this->belongsTo( ProductRoute::class);
    }

}
