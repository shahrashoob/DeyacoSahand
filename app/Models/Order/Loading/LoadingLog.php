<?php

namespace App\Models\Order\Loading;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadingLog extends Model
{
    use HasFactory;

    protected $table = "loading_logs";
    protected $fillable = ["loading_process_id", "order_id", "status_id", "message_id", "user_id"];


}
