<?php

namespace App\Models\LineProduct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewProductInventory extends Model
{
    use HasFactory;
    protected $table="new_product_inventory";

    protected $fillable=["product_code","product_id","end_inventory","error"];
}
