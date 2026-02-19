<?php

namespace App\Notifications;

use App\Models\Utility\Notification\SMSMessageResult;
use App\Models\Utility\Notification\SMSTemplate;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Carbon\Carbon;
use Database\Seeders\Utility\Notification\SMSTemplateSeeder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Kavenegar\KavenegarApi;

class SMSNotification extends Notification
{
    use Queueable;

    public function __construct($template, $token = null, $token2 = null, $token3 = null, $token10 = null, $token20 = null, $is_force = false)
    {
        $this->template = $template;
        $this->token = $token;
        $this->token2 = $token2;
        $this->token3 = $token3;
        $this->token10 = $token10;
        $this->token20 = $token20;
        $this->is_force = $is_force;
    }

    public function via($mobile)
    {

        try {
            $list_setting = Setting::whereIn("id", [1, 8, 115, 121])->get()->keyBy("id");
            $company_name = $list_setting[1]; // نام شرکت
            $send_sms = $list_setting[8]; // is_active_sms_module
            $credit = $list_setting[115]->integer_value; // اعتبار حساب
            $min_of_charge_for_payment = $list_setting[121]->integer_value; // حداقل اعتباری که سامانه اجازه ارسال پیامک دارد

            // ذخیره قالب پیامک
            $sms_template_id = -1;
            if (isset(SMSTemplateSeeder::$data[$this->template]["id"])) {
                $sms_template = SMSTemplateSeeder::$data[$this->template];
                $sms_template_id = $sms_template["id"];
            }

            // حساب اعتبار دارد
            // اگر پیامک فورس است، نیاز نیست اعتبار چک شود.
            if ($credit < $min_of_charge_for_payment && !$this->is_force) {
                return false;
            }
            // اجازه ارسال دارد
            if ($send_sms->integer_value == 0) {
                return false;
            }
            // موبال خالی نباشد
            if (!isset($mobile) || $mobile == "") {
                return false;
            }

            if ($this->token20 == null) {
                // به طور پیش فرض همجا مقدار token20 را به نام شرکت قرار می دهیم.
                $this->token20 = $company_name->string_value ?? null;
            }


            if (env("KAVENEGAR_APIKEY") == "sandbox") {
                $message = SMSTemplateSeeder::$data[$this->template]["text"];

                if ($this->token10) {
                    $message = str_replace('%token10', $this->token10, $message);
                }
                if ($this->token20) {
                    $message = str_replace('%token20', $this->token20, $message);
                }
                if ($this->token2) {
                    $message = str_replace('%token2', $this->token2, $message);
                }
                if ($this->token3) {
                    $message = str_replace('%token3', $this->token3, $message);
                }

                if ($this->token) {
                    $message = str_replace('%token', $this->token, $message);
                }
                $sms_message_result = SMSMessageResult::create(
                    [
                        "messageid" => 0,
                        "message" => $message,
                        "status" => 1,
                        "statustext" => "sandbox",
                        "sender" => 0,
                        "receptor" => $mobile,
                        "cost" => 0,
                        "sms_template_id" => $sms_template_id
                    ]
                );
                return ;
            }

            $result = (new KavenegarApi(env("KAVENEGAR_APIKEY")))->VerifyLookup(
                $mobile,
                $this->remove_character($this->token),
                $this->remove_character($this->token2),
                $this->remove_character($this->token3),
                $this->template,
                null,
                $this->remove_character($this->token10, 10),
                $this->remove_character($this->token20, 20),
            );

            // Save To DataBAse
            foreach ($result as $item) {

                $sms_message_result = SMSMessageResult::create(
                    [
                        "messageid" => $item->messageid,
                        "message" => $item->message,
                        "status" => $item->status,
                        "statustext" => $item->statustext,
                        "sender" => $item->sender,
                        "receptor" => $item->receptor,
                        "cost" => $item->cost,
                        "sms_template_id" => $sms_template_id
                    ]
                );
            }


        } catch (\Kavenegar\Exceptions\ApiException $e) {
            // در صورتی که خروجی وب سرویس 200 نباشد این خطا رخ می دهد
            //  echo $e->errorMessage();
        } catch (\Kavenegar\Exceptions\HttpException $e) {
            // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد
            //  echo $e->errorMessage();
        }
    }

    public function remove_character($string, $is_tocke10_20 = false)
    {
        if ($is_tocke10_20) {

            if ($is_tocke10_20 == 10 && substr_count($string, ' ') > 4) {
                $string = Str::replace(" ", ".", $string);
            }
            if ($is_tocke10_20 == 20 && substr_count($string, ' ') > 8) {
                $string = Str::replace(" ", ".", $string);
            }

        } else {
            $string = Str::replace(" ", ".", $string);
        }

        $string = Str::replace("_APP_NAME_", env("APP_NAME"), $string);
        $string = Str::replace("-", ".", $string);
        $string = Str::replace("_", ".", $string);
        $string = Str::replace("|", ".", $string);

        return $string;
    }

    public static function Test(){
        $software_name = Setting::getStringValue( "software_name" );
        $template      = "logintoken";
        $token         = 1254;
        $token2        = jdate( Carbon::now()->timestamp )->format( 'H:i Y/m/d ' );
        $token3        = "5646";
        $token10       = $software_name;
        $token20       = "TestSMS";

        $worker=Worker::find(1);
        \Illuminate\Support\Facades\Notification::send(
            "0".$worker->mobile,
            new SMSNotification( $template, $token, $token2, $token3, $token10, $token20,true )
        );
    }
}
