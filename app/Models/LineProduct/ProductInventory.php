<?php

namespace App\Models\LineProduct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    use HasFactory;
    protected $table="product_inventory";

    protected $fillable=["product_id","end_enventory"];
}
