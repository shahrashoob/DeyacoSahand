<?php

namespace App\Models\Utility\Notification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSTemplateGroup extends Model
{
    use HasFactory;
    protected $table="sms_template_groups";
    protected $fillable=['caption'];
}
