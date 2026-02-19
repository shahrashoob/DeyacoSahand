<?php

namespace App\Http\Controllers\HR\Employment\Register;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\ConfirmInfoController;
use App\Models\File\File;
use App\Models\HR\Company\Company;
use App\Models\HR\Education\Education;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentLog;
use App\Models\HR\Personal\AcademicDegree;
use App\Models\HR\Personal\AcademicDegreeType;
use App\Models\HR\Personal\Gender;
use App\Models\HR\Personal\MaritalStatus;
use App\Models\HR\Personal\Nationality;
use App\Models\HR\Personal\PersonalType;
use App\Models\HR\User\CooperationType;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\LineProduct\Product;
use App\Models\Post\Post;
use App\Models\User;
use App\Models\Utility\Address\Address;
use App\Models\Utility\Address\Country;
use App\Models\Utility\Address\Province;
use App\Models\HR\User\UserAddress;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Message;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Morilog\Jalali\Jalalian;
use Mpdf\Tag\Em;

class StartController extends Controller
{
    protected $view_path = "hr.employment.register.start.";
    protected $route_path = "hr.employment.register.start.";
    protected $next_route = "hr.employment.register.confirm_mobile.index";

    protected $max_send_sms = 30;

    public function __construct()
    {
        $this->middleware('guest');
    }

    //در این تابع اطلاعات صفحه اول همکاری نمایش داده می شود.
    public function index($key = "")
    {
        //چک کردن اینکه آیا یک درخواست همکاری در حال تکمیل دارد یا خیر
        $employment = Employment::
        where("key", $key)->
        where("employments.status_id", 4640301)->
        first();

        // کشور مبدا
        $origin_country_id = Setting::getIntegerValue('origin_country_id');

        $cooperation_type_option = Option::get("cooperation_type", $employment->cooperation_type_id ?? 0, 0, [1, 11, 3, 6, 2]);
        $personal_type_option = Option::get("personal_type", $employment->personal_type_id ?? 0);
        $country_option = Option::get("country", $employment->country_id ?? $origin_country_id);
        $mobile_country_option = Option::get("country", $employment->mobile_country_id ?? $origin_country_id);
        return view($this->view_path . "index", compact(["cooperation_type_option", "personal_type_option", "country_option", "employment", "origin_country_id", "mobile_country_option"]));
    }

    public function submit(Request $request, $key = "")
    {

        $national_code = $request->national_code;
        if (!$national_code) {
            return back()->withErrors("لطفا کد ملی/شناسه ملی را وارد نمایید.");
        }

        //چک کردن حداکثر تعداد مجاز ارسال پیامک
        $result = $this->check_for_send_sms();
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $origin_country_id = Setting::getIntegerValue('origin_country_id');
        // ویرایش درخواست قبلی
        $before_employment = Employment::
        where("key", $key)->
        where("employments.status_id", 4640301)->
        first();

        if (!$before_employment) {
            //چک کردن اینکه آیا یک درخواست همکاری در حال تکمیل دارد یا خیر
            $before_employment = Employment::
            where("employments.national_code", $national_code)->
            where("employments.status_id", 4640301)->
            first();

            if ($before_employment) {
                return redirect()->route($this->route_path . "index", $before_employment->key)->withErrors("با توجه به اینکه شما یک درخواست همکاری در حال تکمیل دارید، لطفا ابتدا آن را تکمیل نمایید.");
            }

        }

        // درخواست ناتمام از قبل وجود داشته است.
        if ($before_employment) {
            if (Worker::where('mobile', $request->mobile)->exists() && $before_employment->mobile != $request->mobile) {
                return back()->withErrors("شماره همراه در سامانه وجود دارد، لطفا شماره دیگری وارد کنید.");
            }
            $before_employment->cooperation_type_id = $request->cooperation_type_id;
            $before_employment->personal_type_id = $request->personal_type_id;
            $before_employment->country_id = $request->country_id;
            $before_employment->nationality_id = $origin_country_id == $request->country_id ? 1 : 2;
            $before_employment->national_code = Message::convert_farsi_digits_to_english($request->national_code);
            $before_employment->mobile_country_id = $request->mobile_country_id;
            $before_employment->mobile = Message::convert_farsi_digits_to_english($request->mobile);
            $before_employment->save();
            self::send_smd($before_employment);
            return redirect()->route($this->next_route, $before_employment->key);
        }
        //اگر که در حال انتظار بارگزاری مدارکو در انتظار اصلاح فرم درخواست بود بتواند وارد شود
        $before_upload_document_employment = Employment::
        where("employments.national_code", $national_code)->
        whereIn("employments.status_id", [4640107, 4640108, 4640113, 4640115, 4640118, 4640121, 4640123, 4640132])->
        first();

        if ($before_upload_document_employment) {

            if ($request->mobile != $before_upload_document_employment->mobile) {
                return back()->withErrors("شماره موبایل وارد شده با شماره موبایل ثبت نام شده مغایرت دارد.");
            }
            self::send_smd($before_upload_document_employment);
            return redirect()->route($this->next_route, $before_upload_document_employment->key);

        }

        // اگر قبلا در سامانه ثبت نام کرده، امکان ثبت نام مجدد برای او وجود ندارد.
        $user_exist = Employment:: where("national_code", $national_code)->
        where('cooperation_type_id', $request->cooperation_type_id)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108, 4640113, 4640115, 4640118, 4640121, 4640123, 4640132])->
        first();
        if ($user_exist) {
            return back()->withErrors("شما قبلا با این کد ملی در سامانه درخواست همکاری داده اید.");
        }
        $user = User:: where("national_code", $national_code)->first();
        if ($user) {
            return back()->withErrors("شما قبلا با این کد ملی در سامانه ثبت نام نموده اید.");
        }
        if ($request->personal_type_id == 2) {
            if (Company:: where("national_code", $national_code)->exists()) {
                return back()->withErrors("شما قبلا با این شناسه ملی در سامانه ثبت نام نمود اید.");
            }
        }
        if ($request->personal_type_id == 1) { // حقیقی
            if (Worker::where('mobile', $request->mobile)->exists()) {
                return back()->withErrors("شماره همراه در سامانه وجود دارد، لطفا شماره دیگری وارد کنید.");
            }
        }
        if (strlen($request->mobile) !== 10) {
            return back()->withErrors("شماره همراه باید 10 رقم باشد.");
        }

        session([
            "cooperation_type_id" => $request->cooperation_type_id,
            "personal_type_id" => $request->personal_type_id,
            "nationality_id" => $origin_country_id == $request->country_id ? 1 : 2,
            "national_code" => $request->national_code,
            "mobile" => $request->mobile,
            "mobile_country_id" => $request->mobile_country_id,
        ]);

//        $result_check_national_code = Employment::CheckNationalCode($national_code, env("IC_APIKEY"));
////        $result_check_national_code["result"] = false;
//        if ($result_check_national_code["result"]) {
//            // کد ملی در ic وجود دارد
//            return redirect()->route($this->route_path . "exist_national_code");
//        } else {
        $employment = Employment::create([
            "cooperation_type_id" => $request->cooperation_type_id,
            "personal_type_id" => $request->personal_type_id,
            "nationality_id" => $origin_country_id == $request->country_id ? 1 : 2,
            "national_code" => $request->national_code,
            "country_id" => $request->country_id,
            "mobile" => $request->mobile,
            "mobile_country_id" => $request->mobile_country_id,
            "status_id" => 4640301
        ]);

        $employment->key = $employment->id . Str::random(50);
        $employment->save();

//        }
        self::send_smd($employment);
        return redirect()->route($this->next_route, $employment->key);
    }


    //در صورتی که کاربر ثبت نام خود را کرده باشد و بخواهد وارد سامانه همکاری با ما شود از این صفحه وارد می شود
    public function link($key = "")
    {
        //چک کردن اینکه آیا یک درخواست همکاری در این وضعیت ها است یا خیر
        $employment = Employment::
        where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108, 4640113, 4640115, 4640118, 4640121, 4640123])->
        first();

        if (!$employment) {
            return back()->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        return view($this->view_path . "link", compact(["employment"]));
    }

    public function submit_link($key = "")
    {
        //چک کردن اینکه آیا یک درخواست همکاری در این وضعیت ها است یا خیر
        $employment = Employment::
        where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108, 4640113, 4640115, 4640118, 4640121, 4640123])->
        first();
        if (!$employment) {
            return back()->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        //چک کردن حداکثر تعداد مجاز ارسال پیامک
        $result = $this->check_for_send_sms();
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
//ارسال sms کد تایید
        self::send_smd($employment);
        return redirect()->route($this->next_route, $employment->key);
    }


    public static function send_smd(Employment $employment)
    {


        $direct_register_data = session("logout_data") ?? null;

        $otp_token = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);

        session([
            "verification_code" => $otp_token,
            "employment_id" => $employment->id
        ]);

        $otp_token = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);

        session([
            "verification_code" => $otp_token,
            "employment_id" => $employment->id
        ]);

        $employment_mobile = "00" . ($employment->mobile_country->area_code ?? "98") . $employment->mobile;


        // کاربر بر روی دکمه ثبت نام مستقیم کلیک کرده و پیامک به جای ارسال برای مشتری، به خود او ارسال می شود.
        if (in_array($employment->cooperation_type_id, [2, 3, 6]) && $direct_register_data) {

            $data_json = JsonDataList::find($direct_register_data);
            if ($data_json) {
                $worker = Worker::find($data_json->other_id);
                if ($worker) {
                    $employment_mobile = "00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile;
                }
                $data_json->delete();
            }


        }
        Notification::send($employment_mobile,
            new SMSNotification("logintoken",
                $otp_token)
        );

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


}
