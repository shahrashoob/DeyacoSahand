<?php

namespace App\Models\Utility\Script;

use App\Models\LineProduct\Machine\Machine;
use App\Models\Utility\Event;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScriptLog extends Model
{
    use HasFactory;
    protected $fillable=["script_id","other_id","run_status_id","event_id","user_id","contour","data","machine_id"];

    public function script(){
        return $this->belongsTo(Script::class);
    }
    public function machine(){
        return $this->belongsTo(Machine::class);
    }
    public function run_status(){
        return $this->belongsTo(Status::class,"run_status_id");
    }
    public function event(){
        return $this->belongsTo(Event::class);
    }
    public function worker(){
        return $this->belongsTo(Worker::class,"user_id");
    }

    public function datetime() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i:s Y/m/d ' );

    }
}
