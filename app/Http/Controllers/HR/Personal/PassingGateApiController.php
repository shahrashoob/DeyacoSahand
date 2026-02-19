<?php

namespace App\Http\Controllers\HR\Personal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HR\User\UserEntryLog;
use App\Models\Utility\Setting;
use App\Models\Utility\SmartObject;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PassingGateApiController extends Controller {
    //

    public function submit( Request $request ) {

        if ( ! $request->user()->currentAccessToken() ) {
            return $result = [
                "result"       => false,
                "status"       => 402,
                "message_type" => "error",//1=>success 2=> error , 3=>warning
                "message"      => "توکن منقضی شده است.",
                "sound_name"=>"error.mp3"
            ];
        }

        $qr_string = $request->qr_string ?? "";
        //  $qr_string = "https://coreharirnam.ir/Personal/1/t7rg2n8/1695884862";

        $result = [
            "result"       => true,
            "status"       => 200,
            "message_type" => "",//1=>success 2=> error , 3=>warning
            "message"      => "",
            "sound_name"=>"error.mp3"
        ];

        $smart_object = SmartObject::where( "user_id", Auth::id() )->first();
        if ( ! $smart_object ) {
            return $result = [
                "result"       => false,
                "status"       => 2001,
                "message_type" => "error",
                "message"      => "اطلاعات اشیاء هوشمند جهت اتصال به سامانه نامعتبر است.",
                "sound_name"=>"error.mp3"
            ];
        }

        // به دست آوردن شناسه و توکن کاربر
        $split_personal = explode( "Personal", $qr_string );
        if ( count( $split_personal ) != 2 ) {
            return $result = [
                "result"       => false,
                "status"       => 200,
                "message_type" => "error",
                "message"      => "کد پرسنلی نامعتبر است (کد 1) ",
                "sound_name"=>"error.mp3"
            ];
        }
        $split_user = explode( "/", $split_personal[1] );

        if ( count( $split_user ) < 3 ) {
            return $result = [
                "result"       => false,
                "status"       => 200,
                "message_type" => "error",
                "message"      => "کد پرسنلی نامعتبر است (کد 2) ",
                "sound_name"=>"error.mp3"
            ];
        }

        $user_id = $split_user[1];
        $random  = $split_user[2];
        $timestamp  =isset( $split_user[3])? $split_user[3]:0;

        $worker = Worker::where( [
            "id"     => $user_id,
            "random" => $random
        ] )->first();

        if ( ! $worker ) {
            return $result = [
                "result"       => false,
                "status"       => 200,
                "message_type" => "error",
                "message"      => "کد پرسنلی نامعتبر است (کد 3) ",
                "sound_name"=>"error.mp3"
            ];
        }

        // بررسی اعتبار توکن یکبار مصرف
        if($worker->one_time_token_status_id == 1200){
            $current_timestamp= Carbon::now()->timestamp;
            $qr_one_time_token_time=Setting::getIntegerValue("qr_one_time_token_time");
            if($current_timestamp - $timestamp > $qr_one_time_token_time){
                return $result = [
                    "result"       => false,
                    "status"       => 200,
                    "message_type" => "error",
                    "message"      => "کد پرسنلی نامعتبر است (کد 4) ",
                    "sound_name"=>"error.mp3"
                ];
            }
        }

        $user_entry_log = UserEntryLog::where( "user_id", $worker->id )->whereNull( "exit_datetime" )->first();

        // چک کردن اینکه از آخرین ورود/خروج فرد 30 ثانیه فاصله باشد.
        $max_diff=Setting::getIntegerValue("repetitive_passing");
        if ( $user_entry_log ) {
            // قبلا یک ورود ثبت شده است و کمتر از 30 ثانیه از ثبت آن نگذشته است، بنابراین دوباره ثبت نمی کند
            if(  Carbon::now()->diffInSeconds(Carbon::parse($user_entry_log->entry_datetime)) <=$max_diff){
                return $result = [
                    "result"       => true,
                    "status"       => 200,
                    "message_type" => "success",
                    "message"      => "ورود با موفقیت ثبت شده است.",
                    "sound_name"=>"input_success.mp3"
                ];
            }
        } else {
            $user_entry_log_before = UserEntryLog::where( "user_id", $worker->id )->orderByDesc("id")->first();
            if($user_entry_log_before &&  Carbon::now()->diffInSeconds(Carbon::parse($user_entry_log_before->exit_datetime)) <=$max_diff){
                return $result = [
                    "result"       => true,
                    "status"       => 200,
                    "message_type" => "success",
                    "message"      => "خروج با موفقیت ثبت شده است.",
                    "sound_name"=>"output_success.mp3"
                ];
            }
        }

        if ( $user_entry_log ) {
            $result_entry = ConfirmExitController::PassGateExit( $worker, $smart_object->user_id );
        } else {
            $result_entry = ConfirmEntryController::PassGateEnter( $worker, $smart_object->user_id );
        }

        if ( $result_entry["result"] ) {
            return $result = [
                "result"       => true,
                "status"       => 200,
                "message_type" => "success",
                "message"      => $result_entry["success"],
                "sound_name"=>$user_entry_log?"output_success.mp3":"input_success.mp3"
            ];
        } else {
            return $result = [
                "result"       => false,
                "status"       => 200,
                "message_type" => "error",
                "message"      => $result_entry["error"],
                "sound_name"=>"error.mp3"
            ];
        }

    }

    public function system_info(Request $request){
        if ( ! $request->user()->currentAccessToken() ) {
            return $result = [
                "result"       => false,
                "status"       => 402,
                "message_type" => "error",//1=>success 2=> error , 3=>warning
                "message"      => "توکن منقضی شده است."
            ];
        }

        return $result = [
            "result"       => true,
            "status"       => 200,
            "message_type" => "success",//1=>success 2=> error , 3=>warning
            "message"      => "",
            "system_info"=>[
                "software_name"=>Setting::getStringValue("software_name"),
                "deyaco_name"=>"سازمان دیجیتال دیاکو"
            ]
        ];
    }

}
