<?php

namespace App\Models\LineProduct\Product\BOM;

use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BOMDependencyType extends Model
{
    use HasFactory;

    protected $table = "bill_of_material_dependency_types";
}