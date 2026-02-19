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

class MachineAllocationModificationPackingForm extends Model
{
    use HasFactory;

    protected $table = "machine_allocation_modification_packing_form";
    protected $fillable = [
        "machine_allocation_modification_id",
        "product_id",
        "gross_weight",
        "weight",
        "amount",
        "sub_packing_form_number",
        "packing_form_id",
        "consumed_status_id",
        "before_gross_weight",
        "before_weight",
        "before_amount",
        "before_sub_packing_form_number",
    ];

    public function machine_allocation_modification()
    {
        return $this->belongsTo(MachineAllocationModification::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function consumed_status()
    {
        return $this->belongsTo(Status::class, "consumed_status_id");
    }

    public function degree()
    {
        return $this->belongsTo(Degree::class);
    }

    public function packing_form()
    {
        return $this->belongsTo(PackingForm::class);
    }

}
