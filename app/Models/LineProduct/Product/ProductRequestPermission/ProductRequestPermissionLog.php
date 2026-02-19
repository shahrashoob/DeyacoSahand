<?php

namespace App\Models\LineProduct\Product\ProductRequestPermission;

use App\Models\Form\Form;
use App\Models\Utility\Event;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRequestPermissionLog extends Model
{
    use HasFactory;
    protected $table="product_request_permission_logs";
    protected $fillable = [
        "product_request_permission_id",
        "status_id",
        "user_id",
        "message_id",
        "json_data_list_id",
        "form_id",
        "event_id"
    ];

    public function worker(){
        return $this->belongsTo(Worker::class,"user_id","id");
    }
    public function message(){
        return $this->belongsTo(Message::class);
    }
    public function product_request_permission(){
        return $this->belongsTo(ProductRequestPermission::class);
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function form(){
        return $this->belongsTo(Form::class);
    }
    public function event(){
        return $this->belongsTo(Event::class);
    }
    public function get_datetime(){
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );
    }
}
