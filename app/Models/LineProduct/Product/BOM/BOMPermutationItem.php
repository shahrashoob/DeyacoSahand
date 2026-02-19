<?php

namespace App\Models\LineProduct\Product\BOM;

use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BOMPermutationItem extends Model
{
    use HasFactory;
    protected $table = "bill_of_material_permutation_item";
    protected $fillable = [
        "bill_of_material_permutation_id",
        "bill_of_material_id",
        "product_id",
        "material_id",
        "bill_of_material_item_id",
        "bill_of_material_replace_id"
    ];

    public function bill_of_material_permutation() {
        return $this->belongsTo( BOMPermutation::class );
    }

    public function bom() {
        return $this->belongsTo( BOM::class );
    }
    public function product() {
        return $this->belongsTo( Product::class );
    }
    public function material() {
        return $this->belongsTo( Product::class,"material_id" );
    }
    public function bom_item() {
        return $this->belongsTo( BOMItem::class,"bill_of_material_item_id" );
    }
    public function bom_replace() {
        return $this->belongsTo( BOMReplace::class,"bill_of_material_replace_id" );
    }
}
