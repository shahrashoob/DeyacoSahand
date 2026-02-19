<?php

namespace App\Models\HR\User;

use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;
    protected $table='user_devices';

    protected $fillable = [
        'user_id',
        'user_agent',
        "mac_address",
        "device_type_id",
        "platform",
        "browser",
        "browser_version",
        "device_brand",
        "platform_version",
        "caption"
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class,"user_id");
    }
    public function user_device_type()
    {
        return $this->belongsTo(UserDeviceType::class,'device_type_id');
    }
    public function get_created_at()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }
    public function get_updated_at()
    {
        return jdate(Carbon::parse($this->updated_at)->timestamp)->format('H:i Y/m/d ');

    }
}
