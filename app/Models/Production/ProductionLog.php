<?php

namespace App\Models\Production;

use App\Models\Utility\Event;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  App\Models\Worker;
use App\Models\Utility\Status;
use App\Models\Utility\Message;
use Carbon\Carbon;

class ProductionLog extends Model
{
    use HasFactory;
    protected  $table="production_logs";
    protected  $fillable=["status_id","message_id","user_id","production_id","waiting_status_id"];

    public function get_datetime(){
        return jdate( Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }

    public function worker(){
        return $this->belongsTo(Worker::class,"user_id","id");
    }


    public function status(){
        return $this->belongsTo( Status::class,"status_id","id" );
    }
    public function event(){
        return $this->belongsTo( Event::class );
    }

    public function waiting_status(){
        return $this->belongsTo( Status::class,"waiting_status_id","id" );
    }

    public function message(){
        return $this->belongsTo( Message::class,"message_id","id" );
    }


    public function getStatus()
    {
        if ($this->status_id == 500) {
            return  $this->waiting_status->caption ?? "*تولید*";
        } else {
            return $this->status->caption;
        }
    }

}
