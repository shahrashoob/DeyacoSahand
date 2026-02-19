<?php

namespace App\Http\Controllers\HR\Employment\Register\Customer;

use App\Http\Controllers\Controller;
use App\Models\HR\Agent\Agent;
use App\Models\HR\Agent\AgentType;
use App\Models\HR\Employment\Employment;
use App\Models\Utility\Option;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    protected $view_path = "hr.employment.register.customer.agent.";
    protected $route_path = "hr.employment.register.customer.agent.";

// به طور موقت از این کنترلر استفاده نمی شود.این برای زمانی است که بخواهیم در همکاری با ما نمایندگان را ایجاد کنیم
    public function index($key)
    {
        $employment = Employment::where("key", $key)->
        where("status_id", 4640301)->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        if (!$employment->user_id) {
            return back()->withErrors("لطفا ابتدا اطلاعات اولیه خود را تکمیل نمایید.");
        }
        $agent_type_option = Option::get("agent_type");
        $agent_list = Agent::where('employment_id', $employment->id)->get();
        return view($this->view_path . "index", compact('employment', 'agent_type_option', 'agent_list'));

    }

    public function submit($key, Request $request)
    {
        $employment = Employment::where("key", $key)->
        where("status_id", 4640301)->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        $employment_customer = Employment::where([
            'national_code' => $request->national_code,
            'cooperation_type_id' => 31,
            'mobile' => $request->mobile,
            'status_id' => 4640106,
        ])->first();

        if (!$employment_customer) {
            return redirect()->back()->withErrors("نماینده با این مشخصات در سامانه ثبت نشده است.");
        }

        $agent_list = Agent::where('agent_type_id', $request->agent_type_id)->where('employment_id', $employment->id)->get();
        $agent_type = AgentType::where('id', $request->agent_type_id)->first();
        if ($agent_list->count() >= $agent_type->number_of_agent) {
            return back()->withErrors("ثبت بیش از " . $agent_type->number_of_agent . " نماینده امکان پذیر نمی باشد.");
        }

        $agent_employment = Agent::where('user_id', $employment_customer->user_id)->exists();
        if ($agent_employment) {
            return redirect()->back()->withErrors("نماینده با این مشخصات تکراری می باشد.");
        }

        $agent = Agent::create([
            'employment_id' => $employment->id,
            'agent_type_id' => $request->agent_type_id,
            'has_the_right_to_sign' => $request->has_the_right_to_sign ? 1 : 0,
            'user_id' => $employment_customer->user_id,
            'active_code' => Str::random(15),

        ]);

        self::SendSmsForAgent($employment, $agent);
        return redirect()->back()->with(["success" => "اطلاعات نماینده با موفقیت ثبت گردید.کد فعال سازی برای نماینده ارسال شده است.<br/>لطفا کد فعال سازی نماینده در قسمت کد فعال سازی وارد نمایید."]);
    }

    public function destroy($key, Agent $agent)
    {
        $employment = Employment::where("key", $key)->
        where("status_id", 4640301)->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $agent = Agent::where('id', $agent->id)->delete();
        return redirect()->back()->with(["success" => "نماینده با موفقیت حذف شد."]);
    }


    public function reply($key, Agent $agent)
    {
        $employment = Employment::where("key", $key)->
        where("status_id", 4640301)->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        self::SendSmsForAgent($employment, $agent);
        return redirect()->back()->with(["success" => "کد فعال سازی مجددا برای نماینده ارسال گردید."]);
    }


    public static function SendSmsForAgent($employment, $agent)
    {
        $token10 = $agent->worker->fullname("with_gender_2");
        $token = $agent->active_code;
        $token2 = $agent->agent_type->caption;
        $token3 = "_APP_NAME_" . "/employment/register";;
        $token20 = $employment->worker->fullname();

        Notification::send("00" . ($agent->worker->mobile_country->area_code ?? "98") . $agent->worker->mobile,
            new SMSNotification("employmentregisterajent",
                $token,
                $token2,
                $token3,
                $token10,
                $token20));
    }

}
