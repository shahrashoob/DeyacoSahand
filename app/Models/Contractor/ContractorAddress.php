<?php

namespace App\Models\Contractor;

use App\Models\Customer\Customer;
use App\Models\Utility\Address\Address;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractorAddress extends Model
{
    use HasFactory;
    use Loggable;
    protected $table="contractor_address";
    protected $fillable = [
        'contractor_id',
        'address_id',
        'is_default'
    ];
    public function address() {
        return $this->belongsTo( Address::class );
    }
    public function contractor() {
        return $this->belongsTo( Contractor::class );
    }

}
