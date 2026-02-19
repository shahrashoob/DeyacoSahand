<?php

namespace App\Models\Utility\OfficeAutomation;

use App\Models\Utility\Priority;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeAutomationUser extends Model {
    use HasFactory;

    protected $fillable = [ "user_id", "office_automation_work_id", "input_or_create", "status_id", "priority_id" ];


    public function worker() {
        return $this->belongsTo( Worker::class, "user_id" );
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }

    public function priority() {
        return $this->belongsTo( Priority::class );
    }

    public function getInputCreate() {
        if ( $this->input_or_create == 1 ) {
            return "ایجاد شده";
        } else {
            $action = OfficeAutomationAction::where( [
                "office_automation_work_id" => $this->office_automation_work_id,
                "user_id"                   => $this->user_id
            ] )->first();

            if ( $action ) {
                return " وارده (" . $action->office_automation_to_do_type->caption . ")";
            }
        }
    }

    public function CreateFullName() {
        if ( $this->input_or_create == 1 ) {
            return $this->worker->fullName();
        } else {
            $action = OfficeAutomationAction::where( [
                "office_automation_work_id" => $this->office_automation_work_id,
                "user_id"                   => $this->user_id
            ] )->first();

            if ( $action ) {
                return  $action->office_automation_to_do_list->worker->fullName();
            }
        }
    }
}
