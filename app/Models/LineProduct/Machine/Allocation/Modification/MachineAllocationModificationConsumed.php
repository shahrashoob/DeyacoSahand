<?php

namespace App\Models\LineProduct\Machine\Allocation\Modification;

use App\Models\Form\Form;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineAllocationModificationConsumed extends Model
{
    use HasFactory;

    protected $table = "machine_allocation_modification_consumed";
    protected $fillable = [
        "machine_allocation_modification_form_id",
        "allocation_id",
        "product_id",
        "consumed_amount",
    ];

    public function machine_allocation_modification()
    {
        return $this->belongsTo(MachineAllocationModification::class);
    }

}