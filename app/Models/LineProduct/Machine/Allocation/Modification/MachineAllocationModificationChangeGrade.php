<?php

namespace App\Models\LineProduct\Machine\Allocation\Modification;

use App\Models\Form\Form;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineAllocationModificationChangeGrade extends Model {
    use HasFactory;

    protected $table = "machine_allocation_modification_change_grade";
    protected $fillable = [
        "machine_allocation_modification_id",
        "product_id",
        "gross_weight",
        "weight",
        "amount",
        "degree_id",
        "lot_number_id",
        "sub_packing_form_number",
        "packing_form_id",
        "packing_type_id",
        "waste_id"
    ];

    public function product() {
        return $this->belongsTo( Product::class );
    }
    public function waste() {
        return $this->belongsTo( Product::class,"waste_id" );
    }

    public function degree() {
        return $this->belongsTo( Degree::class );
    }
    public function packing_type() {
        return $this->belongsTo( PackingType::class );
    }
    public function lot_number() {
        return $this->belongsTo( LotNumber::class );
    }

    public function packing_form() {
        return $this->belongsTo( PackingForm::class );
    }

}
