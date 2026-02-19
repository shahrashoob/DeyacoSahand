<?php

namespace App\Models\LineProduct\Machine;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\Fault\MachineFault;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Product\Fault\ProductFault;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineTypeMachineFault extends Model
{
    use HasFactory;
    protected $table="machine_type_machine_fault";
    protected $fillable=["machine_type_id","machine_fault_id"];

    public function machine_fault(){
        return $this->belongsTo(MachineFault::class);
    }
    public function machine_type(){
        return $this->belongsTo(MachineType::class);

    }
}
