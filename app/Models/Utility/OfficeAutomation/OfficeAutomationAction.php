<?php

namespace App\Models\Utility\OfficeAutomation;

use App\Models\Post\Post;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeAutomationAction extends Model {
    use HasFactory;

    protected $fillable = [
        "office_automation_work_id",
        "office_automation_to_do_list_id",
        "user_id",
        "status_id",
        "office_automation_to_do_type_id"
    ];

    public function worker() {
        return $this->belongsTo( Worker::class, "user_id" );
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }

    public function getStatus( $current_user_id ) {
        $list    = [];
        $caption = "";

        if ( $current_user_id == $this->office_automation_work->user_id ) {
            return $this->office_automation_work->status->caption;
        }

        $count = OfficeAutomationAction::where( [
            "office_automation_work_id" => $this->office_automation_work_id,
            "user_id"                   => $this->user_id
        ] )->
        where( "status_id", 5250002 )->
        count();

        if ( $count > 0 ) {
            return "در جریان";
        } else {
            return "خاتمه یافته";
        }


    }

    public function office_automation_to_do_type() {
        return $this->belongsTo( OfficeAutomationToDoType::class );
    }

    public function office_automation_work() {
        return $this->belongsTo( OfficeAutomationWork::class );
    }

    public function office_automation_to_do_list() {
        return $this->belongsTo( OfficeAutomationToDoList::class );
    }

    public function to_do_list_child() {
        return $this->hasMany( OfficeAutomationToDoList::class, "office_automation_action_id" );
    }


}
