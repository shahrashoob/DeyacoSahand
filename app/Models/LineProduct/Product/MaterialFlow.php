<?php

namespace App\Models\LineProduct\Product;

use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeOutputBand;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMItem;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialFlow extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = ["product_id", "material_id", "bill_of_material_id", "bill_of_material_item_id", "machine_type_id", "band_code"];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function bom()
    {
        return $this->belongsTo(Product\BOM\BOM::class, "bill_of_material_item_id");
    }

    public function bom_item()
    {
        return $this->belongsTo(BOMItem::class, "bill_of_material_item_id");
    }

    public static function AddOneToOneGraph(Product $product,Product\BOM\BOM $bom, MachineType $machineType)
    {
        $station_operation_ids = LineProductStation::
        where([
            "product_id" => $product->id,
            "machine_type_id" => $machineType->id
        ])->
        pluck("station_operation_id")->
        toArray();


        $items = Product\BOM\BOMItem::join("bill_of_materials", "bill_of_material_id", "bill_of_materials.id")->
        join("products", "material_id", "products.id")->
        orderBy("goods_kind_id")->
        where("bill_of_material_id", $bom->id)->
        whereIn("station_operation_id", $station_operation_ids)->
        select("bill_of_material_item.id", "material_id", "input_line_code", "goods_kind_id")->
        groupBy("material_id", "input_line_code")->
        get();


        // ایجاد نود به ازای باندهای خروجی
        $output_bands = MachineTypeOutputBand::where([
            "machine_type_id" => $machineType->id,
            "active_status_id"=>1200
        ])->get();
        if (count($output_bands) != 1) {
            return [
                "result" => false,
                "error" => "باندهای خروجی گروه ماشین به درستی ثبت نشده است، لطفا با پشیتبانی تماس بگیرید."
            ];
        }

        $number = $output_bands[0]->output_line_number;


        Product\MaterialFlow::where([
            "product_id" => $product->id,
            "bill_of_material_id" => $bom->id,
            "machine_type_id" => $machineType->id,
        ])->delete();


        // ایجاد نود به ازای هر ورودی
        foreach ($items as $bom_item) {
            for ($k = 1; $k <= $number; $k++) {

                Product\MaterialFlow::create([
                    "product_id" => $product->id,
                    "bill_of_material_id" => $bom->id,
                    "bill_of_material_item_id" => $bom_item->id,
                    "machine_type_id" => $machineType->id,
                    "material_id"=>$bom_item->material_id,
                    "band_code" => $k
                ]);
            }
        }
        return [
            "result" => true
        ];
    }
}
