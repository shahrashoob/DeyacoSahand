<?php

namespace App\Models\Order\Loading;

use App\Models\Post\Post;
use App\Models\Utility\Car\CarType;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LoadingProcesses extends Model
{
    use HasFactory;

    protected $table = "loading_processes";
    protected $fillable = ["order_id", "status_id", "supervisor_collect_post_id", "supervisor_loading_post_id", "car_type_id"];

    public function code()
    {
        return 0;
    }

    public function supervisor_collect_post()
    {
        return $this->belongsTo(Post::class,"supervisor_collect_post_id","id");
    }
    public function supervisor_loading_post()
    {
        return $this->belongsTo(Post::class,"supervisor_collect_post_id","id");
    }
    public function status()
    {
        return $this->belongsTo(Status::class,"status_id","id");
    }

    public function getStatus($type=0)
    {
     if($type==0){
        return $this->status->caption;
     }
     else{
         return "در انتظار ".$this->status->caption;
     }
    }
    public function car_type()
    {
        return $this->belongsTo(CarType::class,"car_type_id","id");
    }

    public function log($message = "", $status_id = 0)
    {

        $msg = null;
        if ($message != "") {
            $msg = Message::create(
                [
                    "text" => $message,
                    "other_id" => $this->id,
                    "message_type_id" => 150
                ]
            );
        }


        return LoadingLog::create([
            "loading_process_id" => $this->id,
            "order_id" => $this->id,
            "status_id" => $status_id == 0 ? $this->status_id : $status_id,
            "message_id" => $msg->id ?? 0,
            "user_id" => Auth::user()->id
        ]);


    }


}
