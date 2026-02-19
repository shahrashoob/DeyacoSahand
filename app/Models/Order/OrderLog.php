<?php

namespace App\Models\Order;

use App\Models\Form\Form;
use App\Models\User;
use App\Models\Utility\Event;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    use HasFactory;
    protected  $table="order_logs";
    protected  $fillable=["status_id","event_id","form_id","exit_status_id","message_id","user_id","order_id","customer_message_id"];


    public function get_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('Y/m/d H:i');

    }
    public function get_date()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('Y/m/d');

    }

    public function getStatus(  ) {

        if ( $this->status && $this->status->status_type_id == 351 ) {
            return "در انتظار " . $this->status->caption;
        }
        return $this->status->caption??"***";
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public function message()
    {
        return $this->belongsTo(Message::class);
    }
    public function customer_message()
    {
        return $this->belongsTo(Message::class,"customer_message_id");
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
