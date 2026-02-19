<?php

namespace App\Models\LineProduct\Machine\Fault;

use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\Maintenance\Maintenance;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentMachineFault extends Model {
    use HasFactory;

    protected $table = "current_machine_faults";
    protected $fillable = [ "machine_id", "user_id","machine_fault_id","active_status_id","status_id","maintenance_id" ];

    public function machine_fault() {
        return $this->belongsTo( MachineFault::class );
    }
    public function machine() {
        return $this->belongsTo( Machine::class );
    }
    public function worker() {
        return $this->belongsTo( Worker::class ,"user_id");
    }
    public function active_status() {
        return $this->belongsTo( Status::class ,"active_status_id");
    }
    public function status() {
        return $this->belongsTo( Status::class ,"status_id");
    }
    public function maintenance() {
        return $this->belongsTo( Maintenance::class ,"maintenance_id");
    }


}
