<?php

namespace App\Models\LineProduct\Machine\Maintenance;

use App\Models\User;
use App\Models\Utility\Event;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model {
    use HasFactory;
    protected $table = "maintenance_logs";
    protected $fillable = [
        "allocation_id",
        "maintenance_id",
        "status_id",
        "event_id",
        "machine_id",
        "user_id",
        "message_id"
    ];
    public function get_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d');

    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public function message()
    {
        return $this->belongsTo(Message::class);
    }
    public function worker()
    {
        return $this->belongsTo(User::class,"user_id");
    }
}
