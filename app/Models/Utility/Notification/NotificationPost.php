<?php

namespace App\Models\Utility\Notification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPost extends Model
{
    use HasFactory;
    protected $table="notification_post";
    protected $fillable=["post_id","notification_template_id"];
}
