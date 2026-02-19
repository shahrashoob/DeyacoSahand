<?php

namespace App\Models\HR\LeaveOvertime;

use App\Models\Post\Post;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function jdate;

class LeaveOvertime extends Model {
    use HasFactory;

    protected $fillable = [
        "post_id",
        "user_id",
        "leave_overtime_type_id",
        "start_datetime",
        "end_datetime",
        "start_datetime_return",
        "end_datetime_return",
        "status_id",
        "replacement_leave_overtime_id",
        "register_after_tacking"
    ];

    public function getCode() {
        return $this->id + 1000;
    }

    public function post() {
        return $this->belongsTo( Post::class );
    }

    public function worker() {
        return $this->belongsTo( Worker::class, "user_id" );
    }

    public function replacement_leave_overtime() {
        return $this->belongsTo( LeaveOvertime::class, "replacement_leave_overtime_id" );
    }

    public function leave_overtime_confirmations() {
        return $this->hasMany( LeaveOvertimeConfirmation::class, "leave_overtime_id" );
    }

    public function leave_overtime_type() {
        return $this->belongsTo( LeaveOvertimeType::class );
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }

    public function get_replace_worker($spelit=", "){
        $text = "";
        $list=$this->leave_overtime_confirmations()->whereNotNull("replace_user_id")->groupBy("replace_user_id")->get();
        foreach ( $list as $item ) {
            if ( $item->replace_worker ) {
                $text .= ( $item->replace_worker->fullname() ) .$spelit;
            }
        }
        $text = trim( $text, ", " );
        return $text;
    }

    public function getStatus() {


        if ( $this->status_id == 4630001 ) {
            if($this->leave_overtime_type->leave_overtime_group_id==5){
                return "در انتظار تایید جانشین";
            }
            $text = "";
            $list=$this->leave_overtime_confirmations()->where("status_id",$this->status_id)->groupBy("replace_user_id")->get();
            foreach ( $list as $item ) {
                if ( $item->replace_worker ) {
                    $text .= ( $item->replace_worker->fullname() ) . ", ";
                }
            }
            $text = trim( $text, ", " );

            return "در انتظار تایید " . $text;
        }
        if ( $this->status_id == 4630002 ) {
            $text = "";
            $list=$this->leave_overtime_confirmations()->where("status_id",$this->status_id)->groupBy("current_confirm_post_id")->get() ;
            foreach ( $list as $item ) {
                if ( $item->current_confirm_post ) {
                    $text .= ( $item->current_confirm_post->caption ) . ", ";
                }
            }
            $text = trim( $text, ", " );

            return "در انتظار تایید " . $text;
        }
        if ( $this->status_id == 4630001 ) {
            $text = "";
            foreach ( $this->leave_overtime_confirmations as $item ) {
                $text .= ( $item->post_user->post->caption ?? "" ) . ", ";
            }
            $text = trim( $text, ", " );

            return "در انتظار تایید " . $text;
        }

        if ( $this->status_id == 4630004 ) {
            return "عدم تایید " . ( $this->current_confirm_post->caption ?? "" );
        }


        return $this->status->caption;
    }

    public function logs() {
        return $this->hasMany( LeaveOvertimeLog::class );
    }

    public function getText() {
        $logs = LeaveOvertimeLog::where( "leave_overtime_id", $this->id )->whereIn( "event_id", [
            "4630001",
            "4630010"
        ] )->get();

        $text = "";
        foreach ( $logs as $item ) {
            $text .= ( $item->message->text ?? "" ) . "<br/>";
        }

        return $text;
    }

    public function get_created_at() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( " Y/m/d - %A - H:i:s " );
    }

    public function get_start_datetime() {
        return jdate( Carbon::parse( $this->start_datetime )->timestamp )->format( " Y/m/d - %A - H:i:s " );
    }

    public function get_end_datetime() {
        return jdate( Carbon::parse( $this->end_datetime )->timestamp )->format( " Y/m/d - %A - H:i:s " );
    }
}
