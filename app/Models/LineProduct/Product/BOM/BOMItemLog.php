<?php

namespace App\Models\LineProduct\Product\BOM;


use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BOMItemLog extends Model {

    use HasFactory;

    protected $table = "bill_of_material_item_logs";
    protected $fillable = [
        "bill_of_material_log_id",
        "bill_of_material_id",
        "bill_of_material_item_id",
        "product_id",
        "material_id",
        "amount",
        "number",
        "percent_of_use",
    ];

    public function bom() {
        return $this->belongsTo( BOM::class, "bill_of_material_id" );
    }

    public function bom_item() {
        return $this->belongsTo( BOMItem::class, "bill_of_material_item_id" );
    }

    public function material() {
        return $this->belongsTo( Product::class, "material_id", "id" );
    }
    public function product() {
        return $this->belongsTo( Product::class );
    }

    public static function CreateLogFromItems(BOM $bom,$bom_item_list)
    {

    }

}
