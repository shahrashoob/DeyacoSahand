<?php

namespace App\Models\Utility\Notification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSTemplate extends Model
{
    use HasFactory;
    protected $table="sms_templates";
    protected $fillable=['sms_template_group_id','caption','text'];
    public function sms_template_group()
    {
        return $this->belongsTo(SMSTemplateGroup::class, 'sms_template_group_id');
    }
}
