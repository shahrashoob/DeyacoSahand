<?php

namespace App\Models\Contractor;

use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineEventType;
use App\Models\Utility\Event;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineAllocationLog extends Model {
    use HasFactory;

    protected $fillable = [
        "contractor_id",
        "order_id",
        "machine_id",
        "machine_allocation_id",
        "production_id",
        "message_id",
        "user_id",
        "production_status_id",
        "machine_allocation_status_id",
        "event_id",
        "line_product_station_id"
    ];

    public function get_datetime() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }

    public function worker() {
        return $this->belongsTo( Worker::class, "user_id", "id" );
    }

    public function machine_allocation() {
        return $this->belongsTo( MachineAllocation::class, "machine_allocation_id" );
    }

    public function event() {
        return $this->belongsTo( Event::class, "event_id" );
    }

    public function line_product_station() {
        return $this->belongsTo( LineProductStation::class );
    }
    public function machine_allocation_status() {
        return $this->belongsTo( Status::class, "machine_allocation_status_id" );
    }

    public function message() {
        return $this->belongsTo( Message::class );
    }
}
