<?php

namespace App\Models\Production;

use App\Models\Utility\ChangeStatusEvent;
use App\Models\Utility\Event;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionFormLog extends Model
{
    use HasFactory;
    public function get_datetime() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }

    public function worker() {
        return $this->belongsTo( Worker::class, "user_id", "id" );
    }
    public function status() {
        return $this->belongsTo( Status::class );
    }
    public function event() {
        return $this->belongsTo( Event::class );
    }
    public function message() {
        return $this->belongsTo( Message::class );
    }

}
