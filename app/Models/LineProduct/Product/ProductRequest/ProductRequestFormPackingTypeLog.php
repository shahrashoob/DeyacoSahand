<?php

namespace App\Models\LineProduct\Product\ProductRequest;

use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRequestFormPackingTypeLog extends Model
{
    use HasFactory;

    protected $table = "product_request_form_packing_type_log";
    protected $fillable = [
        "product_request_form_id",
        "product_request_form_item_id",
        "product_id",
        "packing_type_id",
        "degree_id"
    ];

    public $timestamps = false;

    public function packing_type()
    {
        return $this->belongsTo(PackingType::class);
    }

    public function degree()
    {
        return $this->belongsTo(Degree::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function product_request_form_item()
    {
        return $this->belongsTo(ProductRequestFormItem::class);
    }
}