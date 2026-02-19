<?php

namespace App\Models\Warehouse;

use App\Models\Warehouse\WarehouseHandling\WarehouseHandling;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LineProduct\Product;

class WarehouseProductBlock extends Model
{
    use HasFactory;

    protected $table = "warehouse_product_block";
    protected $fillable = [
        "warehouse_id",
        "product_id",
        "warehouse_handling_id",

    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse_handling()
    {
        return $this->belongsTo(WarehouseHandling::class);
    }

    public static function UpdateBlockedByWarehouseHandling(WarehouseHandling $warehouseHandling)
    {
        if (in_array($warehouseHandling->status_id, [524000322, 524000323])) { // تایید یا عدم تایید
            WarehouseProductBlock::where("warehouse_handling_id", $warehouseHandling->id)->delete();
        } else {
            if (count($warehouseHandling->products) > 0) {
                foreach ($warehouseHandling->products as $item) {
                    WarehouseProductBlock::create([
                        "warehouse_id" => $warehouseHandling->warehouse_id,
                        "product_id" => $item->product_id,
                        "warehouse_handling_id" => $warehouseHandling->id,
                    ]);
                }
            } else {
                WarehouseProductBlock::create([
                    "warehouse_id" => $warehouseHandling->warehouse_id,
                    "warehouse_handling_id" => $warehouseHandling->id,
                ]);
            }
        }
    }

    public static function CheckProduct($warehouse_ids, $product_ids)
    {
        $result = ["result" => false];

        if (count($product_ids) == 0) {
            $result["error"] = "لیست کالاهای ورودی برای بررسی وضعیت کالا جهت ثبت تراکنش در انبار نامعتبر است.";
            return $result;
        }

        $count = WarehouseProductBlock::whereIn("warehouse_id", $warehouse_ids)->
        where(function ($query) use ($product_ids) {
            return $query->whereNull("product_id")->
            orWhereIn("product_id", $product_ids);
        })->
        count();

        if ($count == 0) {
            $result["result"] = true;
            return $result;
        }

        $list = WarehouseProductBlock::whereIn("warehouse_id", $warehouse_ids)->
        where(function ($query) use ($product_ids) {
            return $query->whereNull("product_id")->
            orWhereIn("product_id", $product_ids);
        })->
        get();

        $error = "";
        $product_id_blocked = [];
        $warehouse_handling = [];
        foreach ($list as $item) {
            $warehouse_handling[$item->warehouse_handling_id] =
                "با توجه به اینکه انبارگردانی برای  " . $item->warehouse->caption . " در حال انجام است" . ", " .
                " ورود به انبار یا خروج از انبار تا زمان پایان انبار گردانی امکان پذیر نمی باشد.";
            if ($item->product_id) {
                $product_id_blocked[$item->product_id] = $item;
            }

        }

        $error = "";
        foreach ($warehouse_handling as $item) {
            $error .= $item . "<br/>";
        }
        if (count($product_id_blocked) > 0) {
            $error .= "لیست کالاهای غیر مجاز:" . "<br/>";
            foreach ($product_id_blocked as $item) {
                $error .= $item->product->fullCaption() . " (" . (isset($item->warehouse_handling->id) ?("انبارگردانی ".$item->warehouse_handling->id):"") . ")";
            }
        }
        $result["error"] = $error;
        return $result;

    }
}
