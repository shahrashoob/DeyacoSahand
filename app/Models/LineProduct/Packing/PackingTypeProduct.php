<?php

namespace App\Models\LineProduct\Packing;

use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\Product;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PackingTypeProduct extends Model
{
    use HasFactory;

    protected $table="packing_type_product";
}
