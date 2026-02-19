<?php

namespace App\Models\HR\LeaveOvertime;

use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveOvertimeConfirmation extends Model {
    use HasFactory;

    protected $table = "leave_overtime_confirmation";
    protected $fillable = [
        "leave_overtime_id",
        "post_user_id",
        "replace_user_id",
        "number_top_levels_must_confirm",
        "number_top_levels_confirmed",
        "current_confirm_post_id",
        "status_id"
    ];

    public function leave_overtime() {
        return $this->belongsTo( LeaveOvertime::class );
    }
    public function post_user() {
        return $this->belongsTo( PostUser::class );
    }
    public function status() {
        return $this->belongsTo( Status::class );
    }
    public function current_confirm_post() {
        return $this->belongsTo( Post::class, "current_confirm_post_id" );
    }

    public function replace_worker() {
        return $this->belongsTo( Worker::class, "replace_user_id" );
    }
}
