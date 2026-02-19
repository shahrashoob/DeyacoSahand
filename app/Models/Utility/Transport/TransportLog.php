<?php

namespace App\Models\Utility\Transport;

use App\Models\Form\Packing\PackingFormItem;
use App\Models\Order\Order;
use App\Models\Utility\Car\Car;
use App\Models\Utility\Car\CarType;
use App\Models\Utility\Event;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TransportLog extends Model {
    use HasFactory;
public $timestamps=false;
    protected $fillable = [ "transport_id", "status_id", "event_id","message_id","user_id" ];

    public function status() {
        return $this->belongsTo( Status::class );
    }

    public function transport() {
        return $this->belongsTo( Transport::class );
    }
    public function event() {
        return $this->belongsTo( Event::class );
    }




}
