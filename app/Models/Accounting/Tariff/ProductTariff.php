<?php

namespace App\Models\Accounting\Tariff;

use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTariff extends Model
{
    use HasFactory;

    protected $table = "product_tariff";
    protected $fillable = [
        "tariff_id",
        "product_id",
        "service_id",
        "degree_id",
        "warehouse_id",
        "fea",
        "min_buy",
        "max_buy",
        "tax",
        "fare",
        "consumer_price",
        "increase_percentage_deadline_per_day",
        "packing_type_id",
        "type_of_sale_of_product_id",
        "customer_product_caption",
        "customer_product_code",
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, "product_id", "id");
    }

    public function service()
    {
        return $this->belongsTo(Product::class, "service_id");
    }

    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }

    public function degree()
    {
        return $this->belongsTo(Degree::class);
    }

    public function packing_type()
    {
        return $this->belongsTo(PackingType::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function type_of_sale_of_product()
    {
        return $this->belongsTo(Product\TypeOfSaleProduct\TypeOfSaleOfProduct::class, "type_of_sale_of_product_id");
    }

    public function getCaption()
    {
        if ($this->service_id) {
            return $this->service->caption . " (برای " . $this->product->caption . ")";
        }
        return $this->product->caption;
    }

    public static function GroupItemList()
    {
        return [
            "fea",
            "degree_id",
            "type_of_sale_of_product_id",
            "tax",
            "fare",
            "consumer_price",
            "warehouse_id",
        ];
    }

    public function getOption($id)
    {

        $type["product_id"] = $this->product_id;
        $type["tariff_id"] = $this->tariff_id;
        $type["tariff_product_id"] = $this->id;
        $type["selected_list"] = [];
        foreach (ProductTariff::GroupItemList() as $item) {
            $type[$item] = $this->$item;
        }

        return Option::get("tariff_product_group_by_packing_type", $id, $type);
    }

}
