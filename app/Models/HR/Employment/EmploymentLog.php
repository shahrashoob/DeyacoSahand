<?php

namespace App\Models\HR\Employment;

use App\Models\HR\Selection\SelectionType;
use App\Models\Utility\Event;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class EmploymentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        "employment_id",
        "status_id",
        "event_id",
        "user_id",
        "message_id",
        "employment_selection_id",
    ];
    protected $table = 'employment_logs';


    public function employment()
    {
        return $this->belongsTo(Employment::class);
    }
    public function status() {

        return $this->belongsTo(Status::class);
    }
    public function event() {

        return $this->belongsTo(Event::class);
    }
    public function worker() {

        return $this->belongsTo(Worker::class,"user_id");
    }
    public function message() {

        return $this->belongsTo(Message::class);
    }
    public function employment_selection() {

        return $this->belongsTo(EmploymentSelection::class);
    }
    public function get_datetime(){
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');
    }
}
