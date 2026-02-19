<?php

namespace App\Models\Utility;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Log extends Model
{
    use HasFactory;

    protected $table="logs_status";

    protected $fillable=[
            "old_status_id","status_id","status_type_id",
            "table_name",
            "message_id","other_id","opration","user_id"
        ];
    /*
            log type:
            edit
            change تغییرات در دیگر وضعیت ها
            create
            delete
            view
    */
    
    // user id
      // Old status
      // new Status
      // table_name
      // message_id
      // data this
      // operation
      // url
      // status_type_id
      // other_id

      public static function log_type1($data){
                 
                $msg=null;     
                if($data["message"]!=""){
                        $msg= Message::create(
                                [
                                  "text"=>$data["message"],
                                  "other_id"=>0,
                                  "message_type_id"=>120
                                ]
                                );
                }

                $data["message_id"]=$msg->id??0;
                $data["user_id"]=Auth::user()->id;

                Log::create($data);

      }
}
