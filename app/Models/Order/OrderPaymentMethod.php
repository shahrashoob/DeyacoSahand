<?php

namespace App\Models\Order;

use App\Models\Accounting\Payment\PaymentMethodType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPaymentMethod extends Model
{
    use HasFactory;
    protected $table="order_payment_method";
    protected $fillable=["order_id","customer_id","payment_method_type_id","amount","check_delivery_days"];
    public function payment_method_type(){
        return $this->belongsTo(PaymentMethodType::class);
    }
}
