<?php

namespace App\Models\HR\User;

use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserEntryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "user_status_id",
        "entry_register_user_id",
        "entry_permit_status_id",
        "exit_register_user_id",
        "exit_permit_status_id",
        "exit_datetime",
        "entry_datetime"
    ];

    public function worker(){
        return $this->belongsTo(Worker::class, "user_id");
    }
    public function entry_register_worker()
    {
        return $this->belongsTo(Worker::class, "entry_register_user_id");
    }

    public function exit_register_worker()
    {
        return $this->belongsTo(Worker::class, "exit_register_user_id");
    }

    public function entry_datetime($format = 'H:i:s Y/m/d ', $type = "jdate")
    {
        if ($this->entry_datetime) {
            if ($type == "jdate")
                return jdate(Carbon::parse($this->entry_datetime)->timestamp)->format($format);
            else
                return Carbon::parse($this->entry_datetime)->format($format);
        }
    }

    public function exit_datetime($format = 'H:i:s Y/m/d ', $type = "jdate")
    {
        if ($this->exit_datetime) {
            if ($type == "jdate")
                return jdate(Carbon::parse($this->exit_datetime)->timestamp)->format($format);
            else
                return Carbon::parse($this->exit_datetime)->format($format);
        }
    }

    public static function GetUserEntryForDay($worker,$date,$next_date){
     return   UserEntryLog::where( [
            "user_id" => $worker->id
        ] )->
        where( function ( $query ) use ( $date, $next_date ) {
            return $query->where( function ( $query ) use ( $date, $next_date ) {
                return $query->where( "entry_datetime", ">=", $date )->
                where( "entry_datetime", "<=", $next_date );
            } )->
            orWhere( function ( $query ) use ( $date, $next_date ) {
                return $query->where( "exit_datetime", ">=", $date )->
                where( "exit_datetime", "<=", $next_date );
            } );
        } )->
        get();
    }
}
