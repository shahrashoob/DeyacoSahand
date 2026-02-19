<?php

namespace App\Models\Utility\Notification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;
    protected $fillable=["caption","message"];
    public function posts(){
        return $this->hasMany(NotificationPost::class);
    }
}
