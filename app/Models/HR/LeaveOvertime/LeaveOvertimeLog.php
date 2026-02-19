<?php

namespace App\Models\HR\LeaveOvertime;

use App\Models\Post\Post;
use App\Models\Utility\Event;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function jdate;

class LeaveOvertimeLog extends Model {
    use HasFactory;

    protected $fillable = [ "leave_overtime_id", "status_id", "event_id", "post_id", "user_id" ];

    public function leave_overtime() {
        return $this->belongsTo( LeaveOvertime::class );
    }
    public function post() {
        return $this->belongsTo( Post::class );
    }

    public function worker() {
        return $this->belongsTo( Worker::class, "user_id" );
    }

    public function leave_overtime_type() {
        return $this->belongsTo( LeaveOvertimeType::class );
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }
    public function getStatus(){
        return $this->status->caption??"";
    }

    public function event() {
        return $this->belongsTo( Event::class );
    }
    public function message() {
        return $this->belongsTo( Message::class );
    }

    public function get_created_at() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( " Y/m/d - %A - H:i:s " );
    }
}
