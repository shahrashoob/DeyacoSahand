<?php

namespace App\Models\HR\Shift;

use App\Models\HR\Shift\ShiftWork;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftWorkDay extends Model {
    use HasFactory;

    protected $fillable = [ "shift_id", "shift_work_id", "day", "start_datetime", "end_datetime" ];

    public function getDatetime( $format = 'H:i Y/m/d ' ) {
        return jdate( Carbon::parse( $this->datetime )->timestamp )->format( $format );
    }

    public function getStartDatetime( $format = '\'H:i' ) {
        return jdate( Carbon::parse( $this->start_datetime )->timestamp )->format( $format );
    }

    public function getEndDatetime( $format = '\'H:i' ) {
        return jdate( Carbon::parse( $this->end_datetime )->timestamp )->format( $format );
    }

    public function shift() {
        return $this->belongsTo( Shift::class );
    }

    public function shift_work() {
        return $this->belongsTo( ShiftWork::class );
    }

    /*
     * یک گروه شیفتی ممکن است، شامل دو یا چند تکه پیوسته باشد، این تابع اولین تکه را بر می گرداند
     */
    public static function getStartOfShiftWorkDay( ShiftWorkDay $shift_work_day ) {
        if($shift_work_day->start_datetime=="" ){
            return $shift_work_day;
        }
        $shift_work_day_start = ShiftWorkDay::where( [
            "shift_id"       => $shift_work_day->shift_id,
            "shift_work_id"  => $shift_work_day->shift_work_id,
            "end_datetime" => $shift_work_day->start_datetime
        ] )->first();
        if ( ! $shift_work_day_start ) {
            return $shift_work_day;
        }

        return self::getStartOfShiftWorkDay( $shift_work_day_start );
    }
}
