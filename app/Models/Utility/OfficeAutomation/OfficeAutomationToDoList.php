<?php

namespace App\Models\Utility\OfficeAutomation;

use App\Models\Post\Post;
use App\Models\Utility\Priority;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OfficeAutomationToDoList extends Model {
    use HasFactory;

    protected $table = "office_automation_to_do_list";
    protected $fillable = [
        "office_automation_work_id",
        "office_automation_action_id",
        "user_id",
        "status_id",
        "description",
        "end_datetime",
        "priority_id",
        "caption"
    ];


    public function office_automation_work() {
        return $this->belongsTo( OfficeAutomationWork::class );
    }

    public function actions() {
        return $this->hasMany( OfficeAutomationAction::class )->where( "office_automation_to_do_type_id", "!=", 1 );
    }

    public function logs() {
        return $this->hasMany( OfficeAutomationLog::class );
    }

    public function to_view() {
        return $this->hasMany( OfficeAutomationAction::class )->where( "office_automation_to_do_type_id", 1 );
    }

    public function worker() {
        return $this->belongsTo( Worker::class, "user_id" );
    }


    public function status() {
        return $this->belongsTo( Status::class );
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

    public function getCode() {
        $perfix = $this->office_automation_work->getCode();
        if ( $this->code != null ) {
            return $this->code;
        }
        $counter = $this->office_automation_work->all_to_do_list()->where( "id", "<", $this->id )->count() + 1;
        $code    = Str::of( $counter )
                      ->when( $counter < 10, function ( $string ) {
                          return Str::of( '0' )->append( $string );
                      } );

        $this->code = $perfix . "/" . $code;
        $this->save();

        return $this->code;

    }

    public function showAllLogs( $user_id ) {

        if ( $this->user_id == $user_id ) {
            return true;
        }

        $to_view = OfficeAutomationAction::where( [
            "office_automation_to_do_list_id" => $this->id,
            "user_id"                         => $user_id,
            "office_automation_to_do_type_id" => 1
        ] )->
        first();

        if ( $to_view ) {
            return true;
        }

        return false;
    }

    public function getAction( $user_id ) {

        return $to_action = OfficeAutomationAction::where( [
            "office_automation_to_do_list_id" => $this->id,
            "user_id"                         => $user_id
        ] )->
        where( "office_automation_to_do_type_id", "!=", 1 )->
        first();
    }

    public function priority() {
        return $this->belongsTo( Priority::class );
    }

    public function allowSubmitWorkDown( $user_id ) {

// اگر ارجاعی دارد که او ایجاد کننده آن است، همه ارجاع های آن باید خاتمه یافته باشد.
        $count_action = OfficeAutomationAction::
        where( [
            "user_id"                         => $user_id,
            "office_automation_to_do_list_id" => $this->id,
            "office_automation_work_id"       => $this->office_automation_work_id
        ] )->
        where( "office_automation_actions.status_id", 5250002 )->count();


        $count_to_do = OfficeAutomationAction::join( "office_automation_to_do_list", "office_automation_to_do_list_id", "office_automation_to_do_list.id" )->
        where( [
            "office_automation_to_do_list.user_id"                   => $user_id,
            "office_automation_to_do_list.office_automation_work_id" => $this->office_automation_work_id
        ] )->
        whereNotIn( "office_automation_actions.status_id", [5250004,5250012,5250011] )->count();
        // در صورتی که خودش در جریان باشد و همه کارهای ایجاد شده برای آن خاتمه یافته باشد، بتواند انجام کار را ثبت کند.
        if ( $this->status_id == 5250002 && $count_to_do ==0 && $count_action != 0 ) {
            return true;
        }

        return false;
    }

}
