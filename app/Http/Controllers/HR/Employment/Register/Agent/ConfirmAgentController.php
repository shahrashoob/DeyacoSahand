<?php

namespace App\Http\Controllers\HR\Employment\Register\Agent;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Models\HR\Agent\Agent;
use App\Models\HR\Employment\Employment;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ConfirmAgentController extends Controller
{//در این تابع نمایتده تایید نمایندگی خود را تایید می کند
    protected $view_path = "hr.employment.register.agent.confirm_agent.";
    protected $route_path = "hr.employment.register.agent.confirm_agent.";
    protected $max_send_sms = 60;

    public function index(Agent $agent, $active_code)
    {
        if ($agent->active_code != $active_code) {
            return redirect()->back()->withErrors("کد فعال سازی شما نامعتبر است.");
        }
        if ($agent->status_id == 4642002) {
            return redirect()->back()->withErrors("شما قبلا به عنوان نماینده انتخاب شده اید.");
        }

        $company_name = Setting::getStringValue('company_name');
        $software_name = Setting::getStringValue('software_name');
        return view($this->view_path . "index", compact('agent', 'company_name', 'software_name'));

    }

    public function submit(Agent $agent, $active_code, Request $request)
    {
        $verification_code = session("verification_code");
        if ($agent->active_code != $active_code) {
            return redirect()->back()->withErrors("کد فعال سازی شما نامعتبر است.");
        }
        if (!$verification_code || $verification_code != $request->verification_code) {
            return back()->withErrors("کد فعال سازی به درستی وارد نشده است.");
        }
// در صورتی که نماینده تایید شد وضعیت ان به اغاز همکاری میش ود و در پست سازمانی خود قرار میگیرد.
        $agent->status_id = 4642002;
        $agent->save();
        $agent->employment->status_id = 4640106;//آغاز همکاری
        $agent->employment->save();

        //پسس از تایید نمایندگی به نماینده نام کاربری و پسورد داده می شود.
        Employment::AddUserToPost($agent->employment);
        event(new EmploymentLogEvent($agent->employment, 4640041, null, null, $agent->employment->user_id));//تایید نمایندگی
        return redirect()->route('login')->with(["success" => "نمایندگی شما با موفقیت تایید شد."]);
    }

    public function reply(Agent $agent, $active_code)
    {//ارسال کد تایید
        if ($agent->active_code != $active_code) {
            return redirect()->back()->withErrors("کد فعال سازی شما نامعتبر است.");
        }
        //چک کردن حداکثر تعداد مجاز ارسال پیامک
        $result = $this->check_for_send_sms();
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        self::send_smd($agent);

        return redirect()->back()->with(["success" => "کد فعال سازی ارسال شده است. لطفا کد ارسال شده را در فیلد زیر وارد نمایید."]);
    }

    public function check_for_send_sms()
    {
        $count_send_sms = session("count_send_sms") + 1;
        if ($count_send_sms > $this->max_send_sms) {
            return [
                "result" => false,
                "error" => "تعداد درخواست شما برای ارسال کد فعال سازی بیش از حد مجاز می باشد، لطفا چند دقیقه دیگر تلاش کنید.",
                "count" => $count_send_sms

            ];
        }
        session(["count_send_sms" => $count_send_sms]);

        return [
            "result" => true,
            "count" => $count_send_sms
        ];
    }

    public static function send_smd(Agent $agent)
    {
        $otp_token = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);

        session([
            "verification_code" => $otp_token,
        ]);

        Notification::send("00" . ($agent->worker->mobile_country->area_code ?? "98") . $agent->worker->mobile,
            new SMSNotification("logintoken",
                $otp_token)
        );
    }
}
