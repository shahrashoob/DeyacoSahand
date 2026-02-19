<?php

namespace App\Models\Warehouse\WarehouseShelving;

use App\Models\LineProduct\Product;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WarehouseShelvingProduct extends Model
{
    use HasFactory;

    protected $table = 'warehouse_shelving_product';
    protected $fillable = ["warehouse_id", "warehouse_shelving_id", "warehouse_shelving_type_id", "product_id"];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function warehouse_shelving()
    {
        return $this->belongsTo(WarehouseShelving::class);
    }
    public function warehouse_shelving_type()
    {
        return $this->belongsTo(WarehouseShelvingType::class);
    }

    /*
     * چک کردن اینکه قرار گرفتن کالا در این جایگاه مجاز است یا خیر
     */
    public static function CheckForProduct(WarehouseShelving $warehouseShelving, $product_id,$product_have_specific_location)
    {

        if(!$product_have_specific_location){ // کالا جایگاه مشخصی ندارد
            return [
                "result" => true
            ];
        }
        $exists = WarehouseShelvingProduct::where("product_id", $product_id)->
        whereNull("warehouse_shelving_type_id")->
        where("warehouse_shelving_id", $warehouseShelving->id)->
        where("warehouse_id", $warehouseShelving->warehouse_id)->
        first();

        if ($exists) {
            return [
                "result" => true
            ];
        }

        $exists = WarehouseShelvingProduct::where("product_id", $product_id)->
        whereNull("warehouse_shelving_id")->
        where("warehouse_shelving_type_id", $warehouseShelving->warehouse_shelving_type_id)->
        where("warehouse_id", $warehouseShelving->warehouse_id)->
        first();

        if ($exists) {
            return [
                "result" => true
            ];
        }

        $product=Product::find($product_id);
        return [
            "result" => false,
            "error" => "قرار گرفتن کالای(".
                ($product?$product->fullCaption():"***")
                .") در هیچ کدام از محل های  " . $warehouseShelving->warehouse->caption . " مجاز نیست"
        ];
    }

}