<?php

namespace App\Models\LineProduct\Product\BOM;

use App\Models\LineProduct\Degree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BOMDegree extends Model
{
    use HasFactory;
    protected $table = "bill_of_material_degree";
    protected $fillable = [
        "bill_of_material_item_id",
        "product_id",
        "material_id",
        "degree_id"
    ];

    public function degree() {
        return $this->belongsTo( Degree::class );
    }

    public function BOMItem() {
        return $this->belongsTo( BOMItem::class,"bill_of_material_item_id" );
    }
}

