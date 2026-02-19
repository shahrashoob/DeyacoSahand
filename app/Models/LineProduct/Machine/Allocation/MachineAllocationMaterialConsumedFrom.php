<?php

namespace App\Models\LineProduct\Machine\Allocation;

use App\Models\Form\Form;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineAllocationMaterialConsumedFrom extends Model {
    use HasFactory;

    protected $table = "machine_allocation_material_consumed_form";
    protected $fillable = [
        "machine_allocation_material_consumed_id",
        "form_id",
    ];

    public function machine_allocation_material_consumed() {
        return $this->belongsTo( MachineAllocationMaterialConsumedFrom::class );
    }

    public function form() {
        return $this->belongsTo( Form::class );
    }

}
