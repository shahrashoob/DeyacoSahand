<?php

namespace App\Models\LineProduct\Product\BOM;

use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BOMReplace extends Model {
    use HasFactory;

    protected $table = "bill_of_material_replace";
    protected $fillable = [
        "bill_of_material_id",
        "bill_of_material_item_id",
        "product_id",
        "material_id",
        "replace_product_id",
        "amount",
        "number",
        "percent_of_use",
        "priority_number",
        "consumption_correction_factor",
        "consumption_correction_factor_prediction",
        "waste_prediction"
    ];

    public function replace_product() {
        return $this->belongsTo( Product::class, "replace_product_id" );
    }

    public function bom_item() {
        return $this->belongsTo( BOMItem::class, "bill_of_material_item_id" );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }

//    public function bill_of_material_item() {
//        return $this->belongsTo( BOMItem::class,"bill_of_material_item_id" );
//    }

    //تعداد کالاهای جایگزین تولید که با این جایگزین مصرف تولید می شود.
    public function product_permutation_count() {
        return BOMPermutationItem::join("bill_of_material_permutation","bill_of_material_permutation.id","bill_of_material_permutation_id")->
        where( [ "bill_of_material_permutation.product_id"                  => $this->product_id,
                                            "bill_of_material_replace_id" => $this->id,
            "active_status_id"=>1200
        ] )->

        count();
    }
}
