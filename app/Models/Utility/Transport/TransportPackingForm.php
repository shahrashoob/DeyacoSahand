<?php

namespace App\Models\Utility\Transport;

use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportPackingForm extends Model
{
    use HasFactory;
    protected $table="transport_packing_form";
    protected $fillable=["code","transport_id","user_id","packing_form_id","transport_item_id","status_id","product_request_form_id"];

    public function transport(){
        return $this->belongsTo(Transport::class);
    }

    public function transport_item(){
        return $this->belongsTo(TransportItem::class);
    }
    public function packing_form(){
        return $this->belongsTo(PackingForm::class);
    }
    public function product_request_form(){
        return $this->belongsTo(ProductRequestForm::class);
    }

}
