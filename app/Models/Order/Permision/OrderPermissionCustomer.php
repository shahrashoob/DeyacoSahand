<?php

namespace App\Models\Order\Permision;

use App\Models\Post\Post;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPermissionCustomer extends Model
{
    use HasFactory;
    use Loggable;
    protected $table="order_permission_customer";
    protected $fillable=["order_permission_type_id","customer_id","send_sms_for_post_id"];


    public function post()
    {
        return $this->belongsTo(Post::class,'send_sms_for_post_id');
    }
    public function order_permission_type()
    {
        return $this->belongsTo(OrderPermissionType::class);
    }
}
