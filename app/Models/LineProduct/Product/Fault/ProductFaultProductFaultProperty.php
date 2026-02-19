<?php

namespace App\Models\LineProduct\Product\Fault;

use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\LineProduct\Station;
use App\Models\LineProduct\StationOperation;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductFaultProductFaultProperty extends Model
{
    use HasFactory;

    protected $fillable = ["product_fault_id", "product_fault_property_id", "value"];
    protected $table = "product_fault_product_fault_properties";


}