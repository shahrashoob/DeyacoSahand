<?php

namespace App\Models\LineProduct\Product\ProductCreation;

use App\Models\Utility\Event;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCreationProcessLog extends Model
{
    use HasFactory;
    protected $fillable=["product_creation_process_id","event_id","status_id","user_id","message_id"];
    protected $table="product_creation_process_logs";


    public function worker() {
        return $this->belongsTo( Worker::class, "user_id", "id" );
    }
    public function status() {
        return $this->belongsTo( Status::class );
    }
    public function event() {
        return $this->belongsTo( Event::class );
    }

    public function create_date() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'Y/m/d ' );

    }

    public function create_datetime() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

}
