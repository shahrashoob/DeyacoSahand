<?php

namespace App\Models\Warehouse;

use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use App\Models\Order\OppKind;
use App\Models\Order\TransKind;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddingExistingProduct extends Model {
    use HasFactory;

    protected $table = "new_add_existing_products";
    protected $fillable = [
        "trans_kind",
        "warehouse_id",
        "product_code",
        "product_id",
        "amount",
        "opp_kind",
        "message_text"
    ];


    public function warehouse() {
        return $this->belongsTo( Warehouse::class );
    }

    public function packing_form() {
        return $this->belongsTo( PackingForm::class );
    }

    public function trans_kind_item() {
        return $this->belongsTo( TransKind::class, "trans_kind", "id" );
    }

    public function opp_kind_item() {
        return $this->belongsTo( OppKind::class, "opp_kind", "id" );
    }
}

