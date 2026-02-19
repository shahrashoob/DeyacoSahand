<?php

namespace App\Models\Utility\SpecialLicense;

use App\Models\Post\Post;
use App\Models\HR\Committee\Committee;
use App\Models\Utility\Status;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class SpecialLicenseConfirmation extends Model
{
    use HasFactory;
    protected $table="special_license_confirmation";
    protected $fillable=["special_license_id","post_id","committee_id","user_id","status_id","priority_number"];
    public function special_license(){
        return $this->belongsTo(SpecialLicense::class);
    }
    public function post(){
        return $this->belongsTo(Post::class);
    }
    public function committee(){
        return $this->belongsTo(Committee::class);
    }
    public function worker(){
        return $this->belongsTo(Worker::class,"user_id");
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }

}
