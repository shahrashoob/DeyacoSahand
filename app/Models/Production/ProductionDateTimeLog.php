<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionDateTimeLog extends Model
{
    use HasFactory; 
    protected  $table="production_datetime_logs";
    protected  $fillable=["status_id","message_id","user_id","production_datetime_id"];

}
