<?php

namespace App\Models\LineProduct\Product\TypeOfSaleProduct;

use App\Models\LineProduct\Product;
use App\Models\Utility\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;

class TypeOfSaleProductProduct extends Model {
    // به ازای هر نوع فروش، مشخص شود که کالا آن نوع فروش را دارد یا خیر
    use HasFactory;

    protected $table="type_of_sale_product_product";
    protected $fillable = [ "product_id", "type_of_sale_of_product_id", "service_id" ];
    public function service()
    {
        return $this->belongsTo(Product::class,'service_id');
    }
}
