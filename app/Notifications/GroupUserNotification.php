<?php

namespace App\Notifications;

use App\Models\Utility\Notification\NotificationTemplate;
use App\Models\Utility\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Kavenegar\KavenegarApi;
use Kavenegar;

class GroupUserNotification {
    use Queueable;

    public function __construct( $worker_data,$message ) {
        $this->worker_data = $worker_data;
        $this->message = $message;
    }

    public function via( NotificationTemplate $notification_template ) {

        $send_sms = Setting::find( 8 ); // is_active_sms_module

        if ( ! isset( $send_sms ) || ( $send_sms && $send_sms->integer_value == 0 )  ) {
            return false;
        }

        $mobile_list = [];
        foreach ( $this->worker_data  as $worker ) {
            $mobile_list[] = $worker["mobile"];
        }
        $result = Kavenegar::Send( "1000707777",
            $mobile_list,
            $this->message );

        $notification_template->messageid=$result[0]->messageid??"";
        $notification_template->cost=$result[0]->cost??"";
        $notification_template->status=$result[0]->status??"";
        $notification_template->statustext=$result[0]->statustext??"";
        $notification_template->save();

    }

}
