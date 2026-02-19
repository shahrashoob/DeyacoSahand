<?php

namespace App\Models\Form;

use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LineProduct\Product;

class FormItem extends Model {
    use HasFactory;

    protected $table = "form_item";
    protected $fillable = [
        "form_id",
        "general_form_item_id",
        "product_id",
        "amount",
        "order_list_id",
        "sub_amount",
        "packing_type_id",
        "carrier_id",
        "degree_id",
        "lot_number_id",
        "packing_form_item_id",
        "io_line_code",
        "product_request_form_item_id",
        "description"
    ];

    public function product() {
        return $this->belongsTo( Product::class, "product_id", "id" );
    }

    public function carrier() {
        return $this->belongsTo( Carrier::class );
    }

    public function packing_form_item() {
        return $this->belongsTo( PackingFormItem::class );
    }

    public function packing_type() {
        return $this->belongsTo( PackingType::class );
    }

    public function lot_number() {
        return $this->belongsTo( LotNumber::class );
    }

    public function degree() {
        return $this->belongsTo( Degree::class );
    }

    public function form() {
        return $this->belongsTo( Form::class );
    }

    public function product_request_form_item() {
        return $this->belongsTo( ProductRequestFormItem::class );
    }
}
