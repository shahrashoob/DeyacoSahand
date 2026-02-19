<?php

namespace App\Models\Utility\Notification;

use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SMSMessage extends Model {
    use HasFactory;
    var $message_type_name;

    public function __construct( $message_type_name ) {
        $this->message_type_name = $message_type_name;
    }

    public static function ExceptionError($text){

        $software_name = Setting::getStringValue( "software_name" );
        $worker        = Worker::find( 2 );//ربات دیجیتال
        $template      = "exeptionerroralert";
        $token         = $software_name;
        $token2        = "";
        $token3        = "";
        $token10       = $worker->fullname();
        $token20       = $text;


        Notification::send(
            "00" . ( $worker->mobile_country->area_code ?? "98" ) . $worker->mobile,
            new SMSNotification( $template, $token, $token2, $token3, $token10, $token20 )
        );
    }
}
