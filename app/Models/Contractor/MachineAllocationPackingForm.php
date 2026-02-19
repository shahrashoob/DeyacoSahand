<?php

namespace App\Models\Contractor;

use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineAllocationPackingForm extends Model
{
    use HasFactory;
    protected $table="machine_allocation_packing_form";
    protected $fillable=["contractor_id","order_id","status_id","machine_allocation_id","packing_form_id","machine_id" ,"need_to_complete_information"];
    public function contractor() {
        return $this->belongsTo( Contractor::class );
    }
    public function order() {
        return $this->belongsTo( Order::class );
    }
    public function contractor_allocation() {
        return $this->belongsTo( ContractorAllocation::class,"machine_allocation_id" );
    }
    public function packing_form() {
        return $this->belongsTo( PackingForm::class );
    }
    public function machine_allocation() {
        return $this->belongsTo( MachineAllocation::class,"machine_allocation_id" );

    }
}
