<?php

namespace App\Models\Order;

use App\Models\User;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestFromWarehouseLog extends Model
{
    use HasFactory;

    protected  $table="request_from_warehouse_logs";
    protected  $fillable=["rfw_id","status_id","message_id","user_id"];


    public function get_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('Y/m/d H:i');

    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function message()
    {
        return $this->belongsTo(Message::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
