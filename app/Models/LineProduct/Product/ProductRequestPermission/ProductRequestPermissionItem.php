<?php

namespace App\Models\LineProduct\Product\ProductRequestPermission;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Order\Order;
use App\Models\Utility\Address\Address;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class ProductRequestPermissionItem extends Model
{

    use HasFactory;

    protected $table = "product_request_permission_items";
    protected $fillable = [
        "product_request_permission_id",
        "product_request_form_id",
        "order_id",
    ];
    public static $perfix_status_code = "7005";

    public function product_request_form()
    {
        return $this->belongsTo(ProductRequestForm::class);
    }
    public function product_request_permission()
    {
        return $this->belongsTo(ProductRequestPermission::class, 'product_request_permission_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
