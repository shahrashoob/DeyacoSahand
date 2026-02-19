<?php

namespace App\Models\Utility\OfficeAutomation;

use App\Models\Utility\Priority;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OfficeAutomationWork extends Model {
    use HasFactory;

    protected $fillable = [
        "caption",
        "user_id",
        "status_id",
        "priority_id",
        "end_datetime"
    ];

    public function getCode() {

        if ( $this->random == null ) {
            $this->random = Str::random( 6 );
            $this->save();
        }

        $perfix = "DCWT/";
        if ( $this->code != null ) {
            return $this->code;
        }
        $counter    = $this->id + 1000;
        $this->code = $perfix . ( $counter );
        $this->save();


        return $this->code;
    }

    public function worker() {
        return $this->belongsTo( Worker::class, "user_id" );
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }

    public function priority() {
        return $this->belongsTo( Priority::class );
    }



//    public function work_child() {
//
//        return $this->hasMany( OfficeAutomationWork::class, "office_automation_work_parent_id" );
//    }
    public function to_do_list() {
        return $this->hasMany( OfficeAutomationToDoList::class )->whereNull( "office_automation_action_id" );
    }

    public function all_to_do_list() {
        return $this->hasMany( OfficeAutomationToDoList::class );
    }

    public function office_automation_user( $user_id ) {
        return OfficeAutomationUser::where( "user_id", $user_id )->where( "office_automation_work_id", $this->id )->first();
    }


    public function files() {
        return $this->hasMany( OfficeAutomationFile::class );
    }

    public function get_create_date_and_time() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }

    public function start_datetime() {
        return jdate( Carbon::parse( $this->start_datetime )->timestamp )->format( 'Y/m/d ' );

    }

    public function end_datetime() {
        return jdate( Carbon::parse( $this->end_datetime )->timestamp )->format( 'Y/m/d ' );

    }

    public function user_view_all_actions( $user_id ) {


        // آیا کاربر هیچ اکشن مشاهده نشده ای دارد؟
        $count = OfficeAutomationAction::where( [
            "office_automation_work_id" => $this->id,
            "user_id"                   => $user_id,
            "view_status_id"            => "5250011"
        ] )->
        count();


        if ( $count == 0 && $user_id == $this->user_id ) {
            $count = 0;
        }

        return $count == 0;
    }

//    public function StatusUpdate() {
//        $count = OfficeAutomationToDoList::
//        where( "office_automation_work_id", $this->id )->
//        where( "office_automation_to_do_type_id", "!=", 1 )-> // رونوشت
//        where( "status_id", "!=", 5250004 )-> //خاتمه یافته
//        count();
//
//        // اگر همه To Do ها خاتمه یافته بودن، کار را هم خاتمه یافته می کند.
//        if ( $count == 0 ) {
//            $this->status_id = 5250004;
//            $this->save();
//        }
//    }

}
