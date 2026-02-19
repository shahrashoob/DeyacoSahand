<?php

namespace App\Models\LineProduct\Product;

use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\Utility\Status;
use App\Models\Warehouse\WarehouseStorageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductWarehouseStorageType extends Model
{
    use HasFactory;

    protected $fillable = ["product_id", "warehouse_storage_type_id"];
    protected $table = "product_warehouse_storage_type";

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse_storage_type()
    {
        return $this->belongsTo(WarehouseStorageType::class);
    }

    public static function getValuesArray(Product $product)
    {

        if ($product->product_warehouse_storage_type()->count() == 0) {
            ProductWarehouseStorageType::create([
                "product_id" => $product->id,
                "warehouse_storage_type_id" => $product->warehouse_storage_type_id
            ]);
        }

        return $product->product_warehouse_storage_type()->pluck("warehouse_storage_type_id","warehouse_storage_type_id")->toArray();
    }
}
