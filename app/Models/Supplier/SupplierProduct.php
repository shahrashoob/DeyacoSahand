<?php

namespace App\Models\Supplier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierProduct extends Model {
    use HasFactory;
    protected $table = "product_supplier";
    protected $fillable = [
        "supplier_id",
        "product_id"
    ];
}
