<?php

namespace App\Http\Controllers\HR\Personal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\PersonalController;
use App\Models\Post\PostUser;
use App\Models\Utility\HR\Leave\Leave;
use App\Models\HR\User\UserEntryLog;
use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ConfirmIllegalExistController extends Controller
{
    public static $info = [
        "route"         => "hr.personal.confirm_illegal_exit.",
        "enable_status" => [ "001", "002" ],
        "button"        => [
            "caption" => "خروج غیرمجاز",
            "class"   => "btn btn-danger text-white",
            "icon"    => "feather icon-log-out"
        ],
        "view_path"     => "hr.personal.confirm_illegal_exit.",
        "message"       => [ "confirm" => "آیا از  خروج غیرمجاز شاغل اطمینان دارید؟" ]
    ];

    public function submit( Worker $worker ) {

        $result = $this->checkPermission( $worker );
        if ( $result != "" ) {
            return $result;
        }

        $user_entry_log = UserEntryLog::where( "user_id", $worker->id )->whereNull( "exit_datetime" )->first();

        if ( ! $user_entry_log ) {
            return back()->withErrors( "شاغل آخرین بار از سازمان خارج شده است و لازم است تا ورود برای او ثبت شود." );
        }


        $status_id = 4620008;// خارج شده از سازمان


        $user_entry_log->update( [
            "exit_register_user_id" => Auth::id(),
            "exit_datetime"         => now(),
            "exit_permit_status_id"=> 461000100
        ] );

        $worker->status_id = $status_id;
        $worker->save();

        $hr_sign_out_sms = Setting::getIntegerValue( "hr_sign_out_sms" );
        $date = jdate( Carbon::parse( $user_entry_log->exit_datetime ) )->format( 'Y/m/d' );
        $time = jdate( Carbon::parse( $user_entry_log->exit_datetime ) )->format( 'H:i:s' );

        if ( $hr_sign_out_sms ) {
             Notification::send( "00" . ( $worker->mobile_country->area_code ?? "98" ) . $worker->mobile,
                new SMSNotification( "hrentryillegalsignout", $date, $time, null, $worker->fullname() ) );
        }

        PostUser::PostTrafficNotification($worker,Auth::id(),"خروج غیرمجاز",$time." ".$date);

        return back()->with( [ "success" => "خروج با موفقیت ثبت گردید." ] );
    }


    public function checkPermission( Worker $worker ) {

        $result = PersonalController::checkPermissionConditions( $worker, ConfirmIllegalExistController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
