<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewAccountBalance extends Model {
    use HasFactory;

    protected $table = "new_account_balances";
    protected $fillable = [ "customer_id", "caption", "detailed_code", "debtor", "creditor", "error", "code" ];

    public function customer() {
        return $this->belongsTo( Customer::class );
    }
}
