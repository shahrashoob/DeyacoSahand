<?php

namespace App\Models\Customer;

use App\Models\Utility\Address\Address;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model {
    use HasFactory;
    protected $table="customer_address";
    protected $fillable = [
        'customer_id',
        'address_id',
        'is_default'
    ];
    public function address() {
        return $this->belongsTo( Address::class );
    }
    public function customer() {
        return $this->belongsTo( Customer::class );
    }
}
