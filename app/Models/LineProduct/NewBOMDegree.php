<?php

namespace App\Models\LineProduct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewBOMDegree extends Model {
    use HasFactory;
    protected $table = "new_bill_of_material_degree";
    protected $fillable =
        [
            "product_id",
            "material_id",
            "degree_id",
            "new_bill_of_material_id"
        ];
}
