<?php

namespace App\Models\LineProduct\Carrier;

use App\Models\Contractor\Contractor;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Utility\Event;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrierLog extends Model
{
    use HasFactory;
    protected $fillable=["carrier_id","status_id","user_id","machine_id","contractor_id","packing_form_id","event_id"];

    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function packing_form(){
        return $this->belongsTo(PackingForm::class);
    }
    public function user(){
        return $this->belongsTo(Worker::class);
    }
    public function machine(){
        return $this->belongsTo(Machine::class);
    }
    public function contractor(){
        return $this->belongsTo(Contractor::class);
    }
    public function event(){
        return $this->belongsTo(Event::class);
    }
    public function create_date() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }
}
