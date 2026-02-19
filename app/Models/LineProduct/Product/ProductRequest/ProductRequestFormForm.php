<?php

namespace App\Models\LineProduct\Product\ProductRequest;

use App\Models\Form\Form;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRequestFormForm extends Model {
    use HasFactory;

    protected $table = "product_request_form_form";
    protected $fillable = [ "product_request_form_id", "form_id" ];

    public function form() {
        return $this->belongsTo( Form::class );
    }
    public function product_request_form() {
        return $this->belongsTo(ProductRequestForm ::class );
    }

    public function worker() {
        return $this->belongsTo( Worker::class ,"warehouse_transaction_user_id");
    }

}
