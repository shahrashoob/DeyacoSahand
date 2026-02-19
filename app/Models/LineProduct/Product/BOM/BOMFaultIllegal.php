<?php

namespace App\Models\LineProduct\Product\BOM;


use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BOMFaultIllegal extends Model {

    use HasFactory;

    protected $table = "bill_of_material_fault_illegal";
    protected $fillable = [
        "bill_of_material_id",
        "bill_of_material_item_id",
        "product_id",
        "material_id",
        "product_fault_id",
    ];

    public function bom() {
        return $this->belongsTo( BOM::class, "bill_of_material_id" );
    }
    public function material() {
        return $this->belongsTo( Product::class, "material_id", "id" );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function product_fault() {
        return $this->belongsTo( Product\Fault\ProductFault::class, "product_fault_id" );
    }

}
