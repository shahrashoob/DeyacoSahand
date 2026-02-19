<?php

namespace App\Models\Accounting\Tariff;

use App\Models\User;
use App\Models\Utility\Currency;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TariffLog extends Model {
    use HasFactory;

    protected $table = "tariff_logs";
    protected $fillable = [
        "tariff_id",
        "status_id",
        "message_id",
        "user_id",
        "caption",
        "currency_id",
        "start_datetime",
        "end_datetime"
    ];

    public function tariff() {
        return $this->belongsTo( Tariff::class );
    }

    public function status() {
        return $this->belongsTo( Status::class, "status_id", "id" );
    }

    public function currency() {
        return $this->belongsTo( Currency::class );
    }

    public function create_datetime() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i:s Y/m/d ' );

    }

    public function start_datetime() {
        return jdate( Carbon::parse( $this->start_datetime )->timestamp )->format( 'Y/m/d ' );

    }

    public function end_datetime() {
        return jdate( Carbon::parse( $this->end_datetime )->timestamp )->format( 'Y/m/d ' );

    }

    public function message() {
        return $this->belongsTo( Message::class );
    }

    public function user() {
        return $this->belongsTo( User::class );
    }

}
