<?php

namespace App\Models\Contractor;

use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\PackingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportContractorPackingForm extends Model {
    use HasFactory;

    protected $table = "import_contractor_packing_form";
    protected $fillable = [
        "machine_allocation_id",

        "parent_packing_form_row",
        "parent_packing_form_id",
        "parent_packing_type_id",

        "parent_packing_form_carrier",
        "parent_packing_form_carrier_id",

        "packing_form_row",
        "packing_form_id",
        "packing_type_id",

        "packing_form_carrier",
        "packing_form_carrier_id",

        "amount",
        "sub_amount",
        "sub_amount2",

        "degree_code",
        "degree_id",

        "lot_number_code",
        "lot_number_id",

        "error",
        "warning",
    ];

    public function degree() {
        return $this->belongsTo( Degree::class );
    }
    public function parent_carrier() {
        return $this->belongsTo( Carrier::class ,"parent_packing_form_carrier_id" );
    }
    public function carrier() {
        return $this->belongsTo( Carrier::class ,"packing_form_carrier_id" );
    }
    public function parent_packing_type() {
        return $this->belongsTo( PackingType::class ,"parent_packing_type_id" );
    }
}
