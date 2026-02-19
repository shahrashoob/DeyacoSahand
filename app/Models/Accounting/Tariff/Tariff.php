<?php

namespace App\Models\Accounting\Tariff;

use App\Models\LineProduct\Product;
use App\Models\Utility\Currency;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tariff extends Model
{
    use HasFactory;

    protected $fillable = ["caption", "currency_id", "start_datetime", "end_datetime","status_id"];

    public function product(){
        return $this->belongsToMany(Product::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class, "status_id", "id");
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function start_datetime()
    {
        return jdate(Carbon::parse($this->start_datetime)->timestamp)->format('Y/m/d ');

    }

    public function end_datetime()
    {
        return jdate(Carbon::parse($this->end_datetime)->timestamp)->format('Y/m/d ');

    }

    public function log( $status_id = 0,$message = "")
    {

        $msg = null;
        if ($message != "") {
            $msg = Message::create(
                [
                    "text" => $message,
                    "other_id" => $this->id,
                    "message_type_id" => 120
                ]
            );
        }


        return TariffLog::create([
            "tariff_id" => $this->id,
            "status_id" => $status_id == 0 ? $this->status->id : $status_id,
            "message_id" => $msg->id ?? 0,
            "user_id" => Auth::user()->id,
            "caption" => $this->caption,
            "currency_id" => $this->currency_id,
            "start_datetime" => $this->start_datetime,
            "end_datetime" => $this->end_datetime,
        ]);


    }

}
