<?php

namespace App\Models\Order;

use App\Models\Form\Form;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\User;
use App\Models\Utility\Event;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPackingForm extends Model
{
    use HasFactory;

    protected $table = "order_packing_form";
    protected $fillable = [
        "customer_id",
        "order_id",
        "order_list_id",
        "product_id",
        "material_id",
        "packing_form_code",
        "degree_id",
        "lot_number_code",
        "amount",
        "packing_form_id",
        "status_id",
        "packing_type_id",
        "sub_packing_form_number"
    ];

    public function degree(){
        return $this->belongsTo(Degree::class);
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function material(){
        return $this->belongsTo(Product::class,"material_id");
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function packing_form()
    {
        return $this->belongsTo(PackingForm::class);
    }
    public function packing_type()
    {
        return $this->belongsTo(PackingType::class);
    }

}