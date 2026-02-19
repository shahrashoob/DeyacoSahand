<?php

namespace App\Models\Utility\OfficeAutomation;

use App\Models\Utility\Event;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeAutomationLog extends Model
{
    use HasFactory;

    public function worker() {
        return $this->belongsTo( Worker::class, "user_id" );
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
    public function office_automation_work() {
        return $this->belongsTo( OfficeAutomationWork::class );
    }
    public function to_do_list() {
        return $this->belongsTo( OfficeAutomationToDoList::class );
    }
    public function office_automation_action() {
        return $this->belongsTo( OfficeAutomationAction::class );
    }

    public function files() {

        return $this->hasMany( OfficeAutomationFile::class );
    }

    public function get_create_date_and_time() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }
}
