<?php

namespace App\Models\Utility\Notification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationWorker extends Model
{
    use HasFactory;
    protected $table="notification_worker";
    protected $fillable=["user_id","notification_template_id","mobile"];
}
