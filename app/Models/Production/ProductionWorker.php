<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  App\Models\Worker;

class ProductionWorker extends Model
{
    use HasFactory;
    protected  $table="production_worker";
    protected  $fillable=["production_card_id","worker_id","post_name","production_date_time_id","status_id"];

    public function worker(){

        return $this->belongsTo( Worker::class,"worker_id","id" );
    }

    
}
