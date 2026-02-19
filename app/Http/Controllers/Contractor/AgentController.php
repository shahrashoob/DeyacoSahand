<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\HR\Agent\Agent;
use App\Models\HR\Agent\AgentType;
use App\Models\HR\Employment\Employment;
use App\Models\Utility\Option;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    protected $view_path = "contractor.agent.";
    protected $route_path = "contractor.agent.";


    public function index()
    {


        $first_agent = Agent::where('user_id', Auth::user()->id)->where('status_id', 4642002)->whereNotNull('contractor_id')->first();
        if(!$first_agent){
            return redirect()->back()->withErrors("امکان ثبت نماینده نمی باشد با پشتیبانی تماس بگیرید.");
        }

            $agent_list = Agent::where('contractor_id', $first_agent->contractor_id)->get();


        return view($this->view_path . "index", compact('agent_list'));
    }


    public function create()
    {
        $agent_type_option = Option::get("agent_type");
        $gender_option = Option::get("gender");
        $country_option = Option::get("country");
        return view($this->view_path . "create", compact('agent_type_option', 'gender_option', 'country_option'));

    }

    public function submit(Request $request)
    {


        $exists_email = Worker::where('email', $request->email)->exists();
        if ($exists_email) {
            return redirect()->back()->withErrors("نام کاربری تکراری می باشد.");
        }
        $exists_national_code = Worker::where('national_code', $request->national_code)->exists();
        if ($exists_national_code) {
            return redirect()->back()->withErrors("کدملی تکراری می باشد.");
        }

        $exists_mobile = Worker::where('mobile', $request->mobile)->exists();
        if ($exists_mobile) {
            return redirect()->back()->withErrors("تلفن همراه تکراری می باشد.");
        }
        $agent_list = Agent::where('agent_type_id', $request->agent_type_id)->where('employment_id', Auth::user()->id)->get();
        $agent_type = AgentType::where('id', $request->agent_type_id)->first();
        if ($agent_list->count() >= $agent_type->number_of_agent) {
            return back()->withErrors("ثبت بیش از " . $agent_type->number_of_agent . " نماینده امکان پذیر نمی باشد.");
        }

        $employment = Employment::create([
            'national_code' => $request->national_code,
            'cooperation_type_id' => 21,
            'mobile' => $request->mobile,
            'status_id' => 4640100,
            'personal_type_id' => 1,
            'status_personal_id' => 4641401,
            "country_id" => $request->country_id,
            "nationality_id" => ($request->country_id == 112) ? 1 : 2,
        ]);


        $user = worker::create([
            "lastname" => $request->lastname,
            "national_code" => $request->national_code,
            "firstname" => $request->firstname,
            "email" => $request->email,
            "mobile" => $request->mobile,
            "date_of_birth" => $request->date_of_birth,
            "gender_id" => $request->gender_id,
            "country_id" => $request->country_id,
            "nationality_id" => ($request->country_id == 112) ? 1 : 2,
            'birth_certificate_number' => $request->birth_certificate_number

        ]);
        $employment->user_id = $user->id;
        $employment->save();

        $first_agent = Agent::where('user_id', Auth::user()->id)->where('status_id', 4642002)->WherenotNull('contractor_id')->first();
        $agent = Agent::create([
            'contractor_id' =>$first_agent->contractor_id,
            'agent_type_id' => $request->agent_type_id,
            'has_the_right_to_sign' => $request->has_the_right_to_sign ? 1 : 0,
            'user_id' => $employment->user_id,
            'active_code' => Str::random(15),
            'employment_id' => $employment->id,
            'company_id' => $first_agent->company_id,

        ]);

//        self::SendSmsForAgent($employment, $agent);
        return redirect()->route($this->route_path . 'index')->with(["success" => "اطلاعات نماینده با موفقیت ثبت گردید.<br/>نماینده در وضعیت تایید مشخصات مرحله اول قرار گرفته است."]);
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


