<?php

namespace App\Models\LineProduct\Product\ProductRequest;

use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestFormPackingType;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Production\Production;
use App\Models\Utility\Transport\TransportItem;
use App\Models\Utility\Transport\TransportPackingForm;
use App\Models\Utility\Unit;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRequestFormDelivery extends Model {
    use HasFactory;

    protected $table = "product_request_form_delivery";
    protected $fillable = [
        "product_request_form_id",
        "product_request_form_item_id",
        "packing_form_id",
        "master_packing_form_id",
        "packing_type_id",
        "degree_id",
        "carrier_id",
        "lot_number_id",
        "unit_id",
        "product_id",
        "transport_item_id",

        "final_amount",
        "sub_packing_form_number",
        "sub_packing_form_number_selected",
        "count_item",
        "count_product_id"
    ];

    public function product() {
        return $this->belongsTo( Product::class );
    }
    public function unit() {
        return $this->belongsTo( Unit::class );
    }
    public function packing_type() {
        return $this->belongsTo( PackingType::class );
    }
    public function transport_item() {
        return $this->belongsTo( TransportItem::class );
    }

    public function packing_form() {
        return $this->belongsTo( PackingForm::class );
    }
    public function carrier() {
        return $this->belongsTo( Carrier::class );
    }


}
