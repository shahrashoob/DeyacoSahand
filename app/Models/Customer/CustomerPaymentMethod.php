<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPaymentMethod extends Model {
    use HasFactory;

    protected $table = "customer_payment_method";
    protected $fillable = [ "customer_id", "payment_method_type_id", "min_percentage","max_percentage", "max_check_delivery_time_in_days", ];
}
