<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Utility\Message;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

class ProductionDateTime extends Model
{
    use HasFactory;
    use Loggable;
    protected $table="production_date_times";
    protected $fillable=["production_card_id","version","shift_time","start_datetime","end_datetime","status_id"];
    public function production_worker(){

        return $this->hasMany( ProductionWorker::class,"production_date_time_id","id" );
    }
    public function production_card(){
        return $this->belongsTo( Production::class,"production_card_id","id" );
    }
    public function checkForAddWorker($worker_id){

        $current_production_datetime_id=$this->id;
         $start_datetime=$this->start_datetime;
        $end_datetime=$this->end_datetime;
        
        $porductinoDateTime=ProductionDateTime::
        join("production_worker", "production_date_time_id","production_date_times.id")
       ->where("worker_id","=",$worker_id)
        ->where(function($query) use ($start_datetime,$end_datetime){
            $query->orWhere(function($query) use ($start_datetime,$end_datetime){
               $query->where('end_datetime', '>=', $start_datetime);
               $query->where('start_datetime', '<=', $start_datetime);
            });
            $query->orWhere(function($query) use ($start_datetime,$end_datetime){
                $query->where('end_datetime', '>=', $end_datetime);
                $query->Where('start_datetime', '<=', $end_datetime);
            });
           
        })->when($current_production_datetime_id,function($query) use ($current_production_datetime_id){
            return $query->where("production_date_time_id","!=",$current_production_datetime_id);
        })
        ->first();

        if($porductinoDateTime){
            return $porductinoDateTime;
        }
        return null;
        // ->toSql();

    }


    public function log($message=""){
 
        $msg=null;     
                  if($message!=""){
                          $msg= Message::create(
                                  [
                                    "text"=>$message,
                                    "other_id"=>0,
                                    "message_type_id"=>120
                                  ]
                                  );
                  }
  
  
       return ProductionDateTimeLog::create([
          "production_datetime_id"=>$this->id,
          "status_id"=>$this->status_id,
          "message_id"=>$msg->id??0,
          "user_id"=>Auth::user()->id
        ]);
        
    }

}
