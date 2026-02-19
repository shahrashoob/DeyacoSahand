<?php

namespace App\Models\Supplier;

use App\Models\Utility\Address\Address;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        "supplier_id","address_id","is_default"];
    protected $table="supplier_address";

    public function address() {
        return $this->belongsTo( Address::class );
    }
}
