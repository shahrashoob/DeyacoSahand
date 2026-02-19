<?php

namespace App\Models\Form\Packing;

use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Order\OppKind;
use App\Models\Order\TransKind;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewPackingFormHandling extends Model {
    use HasFactory;

    protected $table = "new_packing_form_handling";
    protected $fillable = [
        "warehouse_id",
        "amount",
        "sub_amount",
        "ic",
        "opp_kind",
        "product_code",
        "product_id",
        "lot_number_code",
        "lot_number_id",
        "degree_code",
        "degree_id",
        "carrier_code",
        "carrier_id",
        "packing_form_number",
        "packing_type_id",
        "packing_type_code",
        "message_text"
    ];


    public function warehouse() {
        return $this->belongsTo( Warehouse::class );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function carrier() {
        return $this->belongsTo( Carrier::class );
    }

    public function lot_number() {
        return $this->belongsTo( LotNumber::class );
    }

    public function degree() {
        return $this->belongsTo( Degree::class );
    }

    public function packing_type() {
        return $this->belongsTo( PackingType::class );
    }

    public function packing_form() {
        return $this->belongsTo( PackingForm::class );
    }


    public function opp_kind_item() {
        return $this->belongsTo( OppKind::class, "opp_kind", "id" );
    }
}
