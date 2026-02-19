<?php

namespace App\Models\Utility\Script;

use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Cron\CronExpression;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class Script extends Model {
    use HasFactory;

    protected $fillable = [ "active_status_id", "cron","priority" ];

    public function active_status() {
        return $this->belongsTo( Status::class, "active_status_id" );
    }

    public function get_update_time() {
        return jdate( Carbon::parse( $this->updated_at )->timestamp )->format( 'H:i:s Y/m/d ' );

    }

    public function get_last_run_datetime() {
        if ( $this->last_run_datetime ) {
            return jdate( Carbon::parse( $this->last_run_datetime )->timestamp )->format( 'H:i:s Y/m/d ' );
        }

    }
    public function get_next_run_datetime() {
        if ( $this->next_must_run_date ) {
            return jdate( Carbon::parse( $this->next_must_run_date )->timestamp )->format( 'H:i:s Y/m/d ' );
        }

    }

    public function logs() {
        return $this->hasMany( ScriptLog::class );
    }

    public function AllowRunScript() {

        if ( $this->run_status_id == 410300 ) {
            return false;
        }

        $next_must_run_date = Carbon::parse( $this->next_must_run_date );

        if ( Carbon::now()->greaterThan( $next_must_run_date ) ) {
            return true;
        }

        return false;

    }

    public function next_run_date($cron_text=null) {
        $cron = CronExpression::factory($cron_text?? $this->cron );
        $cron->isDue();

        $previous_run_date = Carbon::parse( $cron->getPreviousRunDate()->format( 'Y-m-d H:i:s' ) );
        $next_run_date     = Carbon::parse( $cron->getNextRunDate()->format( 'Y-m-d H:i:s' ) );

        return $next_run_date;
    }

    public function startOperation() {

        $this->run_status_id = 410300; // در حال اجرا
        $this->save();
    }

    public function endOperation() {
        $this->run_status_id = 410100;
        $this->next_must_run_date=$this->next_run_date();
        $this->save();
    }
    public function UpdateScript(Request $request){

        $this->update( $request->all() );

        $this->next_must_run_date=$this->next_run_date();
        $this->run_status_id=410100;
        $this->save();
    }

    public static function SendSmd($script,$log_id,$message,$user_id=1){
        $software_name = Setting::getStringValue( "software_name" );
        $template      = "scriptexecution";
        $token         = $script->code;
        $token2        = jdate( Carbon::now()->timestamp )->format( 'H:i Y/m/d ' );
        $token3        = $log_id;
        $token10       = $software_name;
        $token20       = Str::replace( " ", ".", $message );
        $worker        = Worker::find( $user_id );//ربات دیجیتال

        Notification::send(
            "00" . ( $worker->mobile_country->area_code ?? "98" ) . $worker->mobile,
            new SMSNotification( $template, $token, $token2, $token3, $token10, $token20 )
        );
    }
    public static function TimeForSms()
    {
        // این تابع یک زمان های خاصی را برای پیامک انتخاب می کند.
        return in_array(Carbon::now()->hour, [
                20
            ]) &&
            Carbon::now()->minute >= 1 &&
            Carbon::now()->minute < 2;
    }
}
