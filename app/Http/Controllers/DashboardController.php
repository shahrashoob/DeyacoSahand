<?php

namespace App\Http\Controllers;

use App\Models\Customer\Customer;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\HR\Evaluation\EvaluationForm\EvaluationFormExport;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormSessionData;
use App\Models\Post\PostUser;
use App\Models\HR\User\UserDevice;
use App\Models\Utility\Option;
use App\Models\Utility\Other\BarcodeLink;
use App\Models\Utility\PupUp\PupUp;
use App\Models\Utility\PupUp\PupUpPost;
use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{

    public function home(Request $request)
    {


        $worker = Worker::find(\Auth::id());
        // اگر فرد  مشتری است که وضعیت او هنوز فعال نشده به این صفحه می رود
        if ($worker->register_status_id == 2002002) {

            $customer=Customer::where("user_id",$worker->id)->first();
            if($customer && $customer->parent_id){


                return redirect()->route("customer_group.retail_customer.buy.index");
            }
            $list = ProductCreationProcess::
            // در صورتی که به داشبورد طراحی کالا دسترسی ندارد، فقط درخواست های خودش را ببیند.
            where("user_id", $worker->id)->
            paginate();

            return view("customer.tmp.dashboard", compact("list"));
        }

        // بررسی اینکه آیا فرد می تواند از برون سازمان وارد شود؟
        $is_static_ip = Setting::
            where("key", "static_ip")->
            where("string_value", "like", "%" . $_SERVER['HTTP_HOST'] . "%")->
            count() > 0;

        $has_permission = PostUser::join("posts", "posts.id", "post_id")->
            where("user_id", $worker->id)->
            where("the_worker_has_permission_to_entering_from_static_ip", 1)->count()
            == 0;

        session([
            "error" => null
        ]);

        // بررسی تعداد دستگاه های مجاز
        $result_device = self::AllowDeviceForUser($request, $worker);

        // دریافت نام دستگاه
        if (!$result_device["result"] && isset($result_device['user_device'])) {
            return redirect()->route("add_caption_for_device", [$worker, $result_device['user_device']]);
        }

        if (!$result_device["result"]) {
            session([
                "error_login" => $result_device["error"],
            ]);
            return redirect()->route("logout");
        }

        if ($is_static_ip && $has_permission) {
            session([
                "error_login" => "با توجه به اینکه موقعیت شما خارج از سازمان می باشد، امکان ورود به سامانه برای شما وجود ندارد، <br/> لطفا پس از ورود به سازمان مجددا اقدام نمایید.",
            ]);

            return redirect()->route("logout");
        }

        // بررسی اینکه در چه وضعیت هایی می توانند وارد سامانه شوند
        $allow_entry = PostUser::join("posts", "posts.id", "post_user.post_id")->
        join("post_entry_status", "post_entry_status.post_id", "posts.id")->
        where("allow_show_personal_menu", 1)->
        where("post_entry_status.status_id", $worker->status_id)->
        where("user_id", $worker->id)->exists();

        if (!$allow_entry) {
            session([
                "error_login" => "با توجه به اینکه وضعیت شما " . $worker->status->caption . " می باشد، امکان ورود به سامانه برای شما مقدور نیست.",
            ]);

            return redirect()->route("logout");
        }

        // بررسی اینکه فرم ارزیابی در انتظار تکمیل دارد یا خیر
        $post_user_all = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids", $worker);
        $count = EvaluationFormExport::
        join("evaluation_forms", "evaluation_forms.id", "evaluation_form_id")->
        where("status_id", 4650001)->
        where("evaluation_forms.created_at", "<", Carbon::now()->addDay(-1))->
        whereIn("evaluation_form_exports.post_id", $post_user_all)->
        count();
        if ($count > 0) {
            Session::flash('warning', ' ' . $count . ' فرم ارزیابی در انتظار تکمیل برای شما وجود دارد، 
                <br/>
            لطفا با مراجعه به داشبورد منابع انسانی/فرم های ارزیابی نسبت به تکمیل فرم ها اقدام فرمایید. ');
        }


        $pup_up = PupUp::get_current_pup_up(session("pup_up"));

        // بعد از 2 ساعت توکن را منقضی می کند.
        session([
            "bearer_token" => Auth::user()->createToken("API TOKEN", ['*'], Carbon::now()->addMinute(24 * 60))->plainTextToken
        ]);

        return view("dashboard", compact("pup_up"));
    }

    public function update($force = false)
    {
        $this->update_with_artisan($force);
    }

    public function update_with_artisan($force = false)
    {
        $current_version = Setting::where("key", "version")->first();
        $last_version = file_get_contents(base_path() . "/.env.version");
        if ($force || $last_version != $current_version->string_value) {
            Artisan::call("migrate");
            Artisan::call("db:seed");
            Artisan::call("view:clear");
            $current_version->string_value = $last_version;
            $current_version->save();
        }
    }


    /**
     * تغییر کلمه عبور در صورتی که اولین بار بعد تعییر رمز وارد می شوند
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function change_pass()
    {
        $worker = Worker::find(Auth::user()->id);


        return view("auth.reset-password", compact("worker"));
    }

    public
    function submit_change_pass(
        Request $request
    )
    {

        $worker = Worker::find(Auth::user()->id);
        if ($request["password"] != $request["confirm_password"]) {
            return back()->withErrors("کلمه عبور و تکرار آن برابر نیست");
        }

        if ($request["password"] != "") {
            $request["password"] = Hash::make($request['password']);
        } else {
            unset($request["password"]);
        }

        $worker->required_reset_password = 0;
        $worker->update($request->all());

        if (Customer::where("user_id", $worker->id)->exists()) {
            $tamplate = "customerresetpass";
        } else {
            $tamplate = "resetpass";
        }

//
        Notification::send("00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile, new SMSNotification($tamplate, $worker->fullname()));


        return redirect()->route("dashboard")->with(["success" => "تغییر کلمه عبور با موفقیت انجام شد."]);
    }

    public function reset_pass()
    {
        return view("auth.reset_pass");
    }


    public function submit_reset_pass(Request $request)
    {
        $request->validate([
            'national_code' => ['required'],
            'mobile' => ['required'],
        ]);

        $worker = User::where('national_code', $request->input('national_code'))
            ->where('mobile', $request->input('mobile'))
            ->first();

        if (!$worker) {
            return back()->withErrors("نام کاربری یا شماره موبایل نادرست است.");
        }
        $otp_token = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);

        session([
            "verification_code" => $otp_token,
            "user_id" => $worker->id
        ]);

        Notification::send("00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
            new SMSNotification("logintoken",
                $otp_token)
        );
        return redirect()->route("reset_pass_verification_code");
    }

    public function reset_pass_verification_code()
    {

        return view("auth.verification_code");
    }

    public function reset_pass_confirm_verification_code(Request $request)
    {
        $request->validate([
            'token' => ['required'],
        ]);

        $verificationCode = session("verification_code");
        $userId = session("user_id");

        $worker = Worker::find($userId);
        if (!$worker) {
            return redirect()->route("login")->withErrors("اطلاعات کاربری نادرست است، لطفا یک بار دیگر تلاش کنید.");
        }
        if ($request->token == $verificationCode) {
            $newPassword = Str::random(8);
            $hashedPassword = Hash::make($newPassword);

            User::where('id', $userId)->update([
                'password' => $hashedPassword,
                'required_reset_password' => 1
            ]);

            if (Customer::where("user_id", $worker->id)->exists()) {
                $tamplate = "changepasscustomer";
            } else {
                $tamplate = "changepass";
            }
            Notification::send(
                "00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
                new SMSNotification($tamplate, $worker->email, $newPassword, null, $worker->fullname()));

            return redirect()->route('login')->with(["success" => "کلمه عبور جدید به شماره موبایل شما پیامک شد."]);

        } else {

            return back()->withErrors("کد تأیید نادرست می باشد.");

        }
    }

    public function add_caption_for_device(Worker $worker, UserDevice $user_device)
    {
        $service_caption_option = Option::get("service_caption", $user_device->id);

        return view("auth.add_caption_for_device", compact('user_device', 'worker', 'service_caption_option'));
    }

    public function submit_add_caption_for_device(Request $request, Worker $worker, UserDevice $user_device)
    {

        $existing_device = UserDevice::where('user_id', $worker->id)
            ->where('mac_address', $user_device->mac_address)
            ->first();

        if (!$existing_device) {
            return back()->withErrors("ورود شما به علت عدم داشتن نام دستگاه نامعتبر است لطفا با پشتیبانی تماس بگیرید.");
        }
        $existing_user_device = UserDevice::where('user_id', $worker->id)
            ->where('caption', $request->caption)
            ->first();
        if ($existing_user_device) {
            return back()->withErrors("نام دستگاه  تکراری می باشد، لطفا نام دیگری  انتخاب نمایید.");
        }
        $existing_device->caption = $request->caption;
        $existing_device->save();

        return redirect()->route("dashboard")->with(["success" => "دستگاه شما با موفقیت ثبت گردید."]);
    }

    public
    function confirm_pup_up(
        Request $request, PupUp $pup_up
    )
    {

        $pup_up_post = PupUpPost::firstOrCreate([
            "pup_up_id" => $pup_up->id,
            "user_id" => Auth::user()->id
        ]);
        $pup_up_post->number++;
        $pup_up_post->save();

        $session_pup_up = session("pup_up");
        if (!isset($session_pup_up)) {
            $session_pup_up = [];
        }

        $session_pup_up[$pup_up->id] = $pup_up->id;

        session(["pup_up" => $session_pup_up]);

        return back();
    }

    ########################################################################
    ## Register Customer
    public
    function qr_link(
        $key
    )
    {
        $barcode_link = BarcodeLink::where("code", $key)->first();
        if (!$barcode_link) {
            return back()->withErrors(__("message.the desired address was not found"));
        }
        session(["barcode_link" => $barcode_link]);

        $barcode_link->increment("number_of_visits");
        if (!\session("locale")) {
            return view("auth.language");
        }

        switch ($barcode_link->barcode_link_type_id) {
            case 1:
                return view("auth.qr_login", compact("barcode_link"));

            case 2:
                return view("auth.qr_login", compact("barcode_link"));

            default:
                1/0;
                break;
        }

    }

    public
    function login_sms(Request $request)
    {

        $otp_token =
            rand(10000, 99999);
        session([
            "mobile" => $request->mobile,
            "otp_token" => $otp_token
        ]);
        Notification::send($request->mobile,
            new SMSNotification("logintoken",
                $otp_token)
        );

        return redirect()->route("login_token");

    }

    public
    function login_token()
    {
        $mobile = session("mobile");

        return view("auth.token", compact("mobile"));
    }

    public
    function submit_token(
        Request $request
    )
    {
        $request->all();
        $mobile = session("mobile");
        $token = session("otp_token");
        $barcode_link = session("barcode_link");
        if(!$barcode_link){
            return redirect()->route("dashboard")->withErrors("توکن نامعتبر است، لطفا یکبار دیگر تلاش کنید.");
        }

        if ("0" . $request->mobile == $mobile && $request->token == $token) {

            $worker = Worker::where([
                "email" => $mobile,
                "mobile" => ltrim($mobile, "0"),
                "cooperation_type_id" => 3,
                "register_status_id" => 2002002
            ])->first();
            if (!$worker) {
                $worker = Worker::create([
                    "email" => $mobile,
                    "mobile" => ltrim($mobile, "0"),
                    "cooperation_type_id" => 3,
                    "required_reset_password" => 0,
                    "register_status_id" => 2002002 // در حال ثبت نام مشتری
                ]);
                $customer = Customer::create([
                    "user_id" => $worker->id,
                    "status_id" => 2001001, // در انتظار ثبت اطلاعات پایه
                    "parent_id" => $barcode_link->customer_id,
                ]);


                    $barcode_link->increment("number_of_register");

//                PostUser::create( [
//                    "user_id" => $worker->id,
//                    "post_id" => 1017 // مشتریان QR
//                ] );

            }

            Auth::loginUsingId($worker->id);

            $request->session()->forget('mobile');
            $request->session()->forget('otp_token');

            return redirect()->route("dashboard");
        } else {
            return redirect()->route("login_token", "login")->withErrors(__("message.the code entered is invalid"));
        }


    }

    public function device_info(Request $request, $mac_address)
    {
        return back()->withErrors("این صفحه حذف گریدید است.");
//        Cookie::queue(Cookie::make('mac_address', $mac_address, 24 * 60 * 30)); // برای یک ماه اعتبار دارد.
//        return redirect()->route("login");
    }

    public static function AllowDeviceForUser(Request $request, Worker $worker)
    {
//        return ["result" => true];
        $mac_address = Cookie::get('mac_address_' . $worker->id);
        $user_agent = $request->server('HTTP_USER_AGENT');
        if (!$mac_address) {
            $mac_address = $worker->id . "-" . Carbon::now()->timestamp;
            Cookie::queue(Cookie::make('mac_address_' . $worker->id, $mac_address, 24 * 60 * 30 * 100)); // برای 100 ماه اعتبار دارد.
        }


//        return [
//            "result" => false,
//            "error" => $mac_address
//        ];

        // آیا تعداد دستگاه ها نامحدود است.
        $min_device = $worker->posts()->join("posts", "posts.id", "post_id")->
        max("max_device_allow_for_login");
        // آیا تعداد دستگاه ها نامحدود است.
        if ($min_device == -1) {
            return ["result" => true];
        }
        $max_device = $worker->posts()->join("posts", "posts.id", "post_id")->
        max("max_device_allow_for_login");

// بررسی وجود mac_address برای      user_id
        $existingdevice = UserDevice::where('user_id', $worker->id)
            ->where('mac_address', $mac_address)
            ->first();
        if ($existingdevice && !$existingdevice->caption) {
            return [
                "result" => false,
                "error" => "لطفا یک عنوان برای دستگاه خود انتخاب کنید.",
                'user_device' => $existingdevice
            ];
        }

        if ($existingdevice) {
            $existingdevice->updated_at = now();
            $existingdevice->save();
            return [
                "result" => true
            ];
        }
// بررسی تعداد دستگاه‌های ثبت ‌شده برای کاربر
        $userdevicecount = UserDevice::where('user_id', $worker->id)->count();

// بررسی حداکثر تعداد دستگاه‌های مجاز برای کاربر
        if ($userdevicecount >= $max_device) {
            return [
                "result" => false,
                "error" => "دستگاه شما برای ورود غیر مجاز است، لطفا با واحد پشتیبانی تماس بگیرید."
            ];
        }
        $device_info = self::DeviceInfo();
        $user_device = UserDevice::create([
            'user_id' => $worker->id,
            'user_agent' => $user_agent,
            'mac_address' => $mac_address,
            "device_type_id" => $device_info["device_type_id"],
            "platform" => $device_info["platform"],
            "platform_version" => $device_info["platform_version"],
            "browser" => $device_info["browser"],
            "browser_version" => $device_info["browser_version"],
            "device_brand" => $device_info["device_brand"],
        ]);
        return [
            "result" => false,
            "error" => "لطفا یک عنوان برای دستگاه خود انتخاب کنید.",
            'user_device' => $user_device
        ];
    }

    public static function DeviceInfo()
    {

        $agent = new Agent();

        $device_info["platform"] = $agent->platform();
        $device_info["platform_version"] = $agent->version($agent->platform());;
        $device_info["browser"] = $agent->browser();
        $device_info["browser_version"] = $agent->version($agent->browser());
        $device_info["device_brand"] = $agent->device();
        if ($agent->isMobile()) {
            $device_info["device_type_id"] = 1; // 'تلفن همراه';

            $deviceBrand = $agent->device();
            // $deviceModel = $agent->version($deviceBrand);
//            $deviceType = "(" . $deviceBrand . " " . $deviceModel . ")" . $deviceType;
        } elseif ($agent->isTablet()) {
            $device_info["device_type_id"] = 2; // ' تبلت';
        } else {
            $device_info["device_type_id"] = 3; // 'دستکتاپ ';

//            $deviceType = $deviceType . " (" . $browser . " " . $version . " - " . $agent->platform() . ")";
        }
        return $device_info;
    }
}

