<?php

namespace App\Models\Form;

use App\Models\User;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormLog extends Model
{
    use HasFactory;

    public function get_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d');

    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function message()
    {
        return $this->belongsTo(Message::class);
    }
    public function worker()
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
