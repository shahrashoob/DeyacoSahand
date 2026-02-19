<?php

namespace App\Models\Warehouse\WarehouseHandling;

use App\Models\Utility\Event;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseHandlingLog extends Model
{
    use HasFactory;
    protected $table = "warehouse_handling_logs";
    protected $fillable = ["warehouse_handling_id", "status_id", "event_id","user_id","message_id"];

    public function warehouse_handling()
    {
        return $this->belongsTo(WarehouseHandling::class,"warehouse_handling_id");
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function worker(){
        return $this->belongsTo(Worker::class,"user_id");
    }
    public function message(){
        return $this->belongsTo(Message::class);
    }
    public function get_datetime(){
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');
    }
}
