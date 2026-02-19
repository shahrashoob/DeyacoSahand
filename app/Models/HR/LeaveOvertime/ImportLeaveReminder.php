<?php

namespace App\Models\HR\LeaveOvertime;


use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLeaveReminder extends Model
{
    use HasFactory;

    protected $table = 'import_leave_reminders';
    protected $fillable = [
        "user_id",
        "national_code",
        "leave_type_id",
        "start_date",
        "end_date",
        "leave_in_start",
        "leave_in_end",
        "leave_remainder",
        "start_date_jalali",
        "end_date_jalali"
    ];

    public function worker()
    {
     return   $this->belongsTo(Worker::class, "user_id");
    }

}
