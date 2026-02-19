<?php

namespace App\Notifications;

use App\Models\Utility\Notification\SMSMessage;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Kavenegar\KavenegarApi;
use Kavenegar\Laravel\Message\KavenegarMessage;
use Kavenegar\Laravel\Notification\KavenegarBaseNotification;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Lookup;

class CustomerNotification {
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( $message_type, $token = null, $token2 = null, $token3 = null ) {
        $this->message_type = $message_type;
        $this->token        = $token;
        $this->token2       = $token2;
        $this->token3       = $token3;
    }

    public function via( $address ) {

        $send_sms = Setting::find( 8 ); // is_active_sms_module

        if ( ! isset( $send_sms ) || ( $send_sms && $send_sms->integer_value == 0 ) || !isset($address) || $address->mobile == "" ) {
            return false;
        }

        $english_name = Setting::find( 12 )->string_value; // is_active_sms_module
        $template     = "";
        switch ( $this->message_type ) {
            case "create_customer":
                $template = "createcustomer";
                break;
            case "changepass_customer":
                $template = "changepasscustomer";
                break;
        }
        $template = $english_name . $template;

        ( new KavenegarApi( env( "KAVENEGAR_APIKEY" ) ) )->VerifyLookup(
            "00" . ( $address->country->area_code ?? "98" ) . $address->mobile,
            $this->remove_character( $this->token ),
            $this->remove_character( $this->token2 ),
            $this->remove_character( $this->token3 ),
            $template
        );

    }

    public function remove_character( $string ) {
        $string = Str::replace( " ", ".", $string );
        $string = Str::replace( "-", ".", $string );
        $string = Str::replace( "_", ".", $string );
        $string = Str::replace( "|", ".", $string );

        return $string;
    }
}
