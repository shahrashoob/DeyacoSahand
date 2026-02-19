<?php

namespace App\Models\Order;

use App\Models\Contractor\ContractorSupplyType;
use App\Models\Form\FormGeneralItem;
use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderConsumedProduct extends Model
{
    use HasFactory;

    protected $table = "order_consumed_product";
    protected $fillable = [
        "order_id",
        "order_list_id",
        "product_id",
        "material_id",
        "contractor_supply_type_id",
        "form_general_item_id"
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function order_list()
    {
        return $this->belongsTo(OrderList::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function form_general_item()
    {
        return $this->belongsTo(FormGeneralItem::class,"form_general_item_id");
    }

    public function material()
    {
        return $this->belongsTo(Product::class, "material_id");
    }

    public function contractor_supply_type()
    {
        return $this->belongsTo(ContractorSupplyType::class);
    }

    public function get_order_packing_forms()
    {
        return OrderPackingForm::where([
            "order_id" => $this->order_id,
            "product_id" => $this->product_id,
            "material_id" => $this->material_id
        ])->get();
    }
}
