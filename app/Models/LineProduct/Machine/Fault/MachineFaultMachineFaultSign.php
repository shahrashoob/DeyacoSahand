<?php

namespace App\Models\LineProduct\Machine\Fault;

use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\LineProduct\Station;
use App\Models\LineProduct\StationOperation;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MachineFaultMachineFaultSign extends Model {
    use HasFactory;

    protected $table = "machine_fault_machine_fault_sign";
    protected $fillable = [ "machine_fault_id", "machine_fault_sign_id" ];

    public function machine_fault() {
        return $this->belongsTo( MachineFault::class );
    }

    public function machine_fault_sign() {
        return $this->belongsTo( MachineFaultSign::class );
    }

}
