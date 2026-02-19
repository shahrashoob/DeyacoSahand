<?php

namespace App\Models\HR\Employment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentNotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        "post_id",
        "status_id",
    ];
    protected $table = 'employment_notification_settings';

}
