<?php

namespace App\Models\Utility\SpecialLicense;

use App\Models\Post\Post;
use App\Models\Utility\Event;
use App\Models\HR\Committee\Committee;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialLicenseLog extends Model
{
    use HasFactory;
    protected $fillable=["special_license_id","status_id","event_id","user_id","post_id","committee_id","message_id"];
    public function special_license(){
        return $this->belongsTo(SpecialLicense::class);
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function event(){
        return $this->belongsTo(Event::class);
    }
    public function worker(){
        return $this->belongsTo(Worker::class,"user_id");
    }
    public function post(){
        return $this->belongsTo(Post::class);
    }
    public function committee(){
        return $this->belongsTo(Committee::class);
    }
    public function message(){
        return $this->belongsTo(Message::class);
    }
    public function get_datetime(){
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );
    }
}
