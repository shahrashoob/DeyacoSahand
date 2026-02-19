<?php

namespace App\Models\LineProduct\Machine\Allocation\Modification;

use App\Models\Form\Form;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineAllocationModification extends Model {
    use HasFactory;

    protected $table = "machine_allocation_modifications";
    protected $fillable = [
        "warehouse_id",
        "machine_id",
        "machine_log_id",
        "status_id",
        "allocation_id",
        "machine_allocation_modification_type_id"
    ];

    public function fullCaption() {
        return "برگشت کالا از انبارک " . $this->machine->caption;
    }

    public function worker() {
        return $this->belongsTo( Worker::class ,"user_id");
    }
    public function machine_allocation_modification_type() {
        return $this->belongsTo( MachineAllocationModificationType::class);
    }

    public function machine() {
        return $this->belongsTo( Machine::class );
    }
    public function warehouse() {
        return $this->belongsTo( Warehouse::class );
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }
    public function allocation() {
        return $this->belongsTo( Allocation::class );
    }

    public function change_grades() {
        return $this->hasMany( MachineAllocationModificationChangeGrade::class )->whereNull("waste_id");
    }

    public function change_wastes() {
        return $this->hasMany( MachineAllocationModificationChangeGrade::class )->whereNotNull("waste_id");
    }

    public function packing_forms() {
        return $this->hasMany( MachineAllocationModificationPackingForm::class );
    }

    public function forms() {
        return $this->hasMany( MachineAllocationModificationForm::class );
    }

    public function create_date() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'Y/m/d h:i:s' );

    }

}
