<?php

namespace App\Models\HR\Shift;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewShiftWorkDay extends Model
{
    protected $table = "new_shift_work_days";
    protected $fillable = [ "shift_id", "shift_work_id", "day", "start_datetime", "end_datetime" ];

    public function shift() {
        return $this->belongsTo( Shift::class );
    }

    public function start_datetime() {
        if($this->start_datetime == null){
            return "";
        }
        return jdate( Carbon::parse( $this->start_datetime )->timestamp )->format( 'H:i Y/m/d ' );

    }

    public function end_datetime() {
        if($this->end_datetime == null){
            return "";
        }
        return jdate( Carbon::parse( $this->end_datetime )->timestamp )->format( 'H:i Y/m/d ' );

    }
    public function datetime() {
        if($this->datetime == null){
            return "";
        }
        return jdate( Carbon::parse( $this->datetime )->timestamp )->format( 'Y/m/d ' );

    }
}
