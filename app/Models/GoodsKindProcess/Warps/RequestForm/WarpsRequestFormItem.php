<?php

namespace App\Models\GoodsKindProcess\Warps\RequestForm;

use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Production\Production;
use App\Models\Warehouse\WarehouseProduct;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarpsRequestFormItem extends Model {
    use HasFactory;
    use Loggable;
    protected $table = "product_request_form_item";
    protected $fillable = [
        "product_request_form_id",
        "production_id",
        "product_id",
        "warehouse_product_id",
        "band_code",
        "current_machine_input_output_band_id",
        "input_line_code"
    ];

    public function product() {
        return $this->belongsTo( Product::class );
    }
    public function lot_number() {
        return $this->belongsTo( LotNumber::class );
    }
    public function carrier() {
        return $this->belongsTo( Carrier::class );
    }
    public function production() {
        return $this->belongsTo( Production::class );
    }

    public function warehouse_product() {
        return $this->belongsTo( WarehouseProduct::class ,"warehouse_product_id");
    }
    public function warps_request_form_packing_types() {
        return $this->hasMany( WarpsRequestFormPackingType::class ,"product_request_form_item_id");
    }
}
