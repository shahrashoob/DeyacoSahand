<?php

namespace App\Models\GoodsKindProcess\Warps\RequestForm;

use App\Models\LineProduct\Packing\PackingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarpsRequestFormPackingType extends Model
{
    use HasFactory;
    protected $table = "product_request_form_packing_type";
    protected $fillable = [
        "product_request_form_id",
        "product_request_form_item_id",
        "product_id",
        "packing_type_id",
    ];

    public function packing_type() {
        return $this->belongsTo( PackingType::class );
    }
}
