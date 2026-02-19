<?php

namespace App\Models\LineProduct\Product\RejectProduct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectProductLog extends Model {

    use HasFactory;

    protected $fillable = [ "reject_product_form_id", "event_id", "status_id", "message_id", "user_id" ];
}
