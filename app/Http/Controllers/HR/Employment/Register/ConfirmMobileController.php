<?php

namespace App\Http\Controllers\HR\Employment\Register;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Models\File\File;
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
use App\Models\Post\Post;
use App\Models\User;
use App\Models\Utility\Address\Address;
use App\Models\Utility\Address\Country;
use App\Models\Utility\Address\Province;
use App\Models\HR\User\UserAddress;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Morilog\Jalali\Jalalian;

class ConfirmMobileController extends Controller
{
    protected $view_path = "hr.employment.register.confirm_mobile.";
    protected $route_path = "hr.employment.register.confirm_mobile.";


    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index($key = "")
    {
        //چک کردن اینکه آیا یک درخواست همکاری در حال تکمیل دارد یا خیر
        $employment = Employment::
        where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108, 4640113, 4640115, 4640118, 4640121, 4640123, 4640132])->
        first();

        if (!$employment) {
            return back()->withErrors("اطلاعات درخواست همکاری معتبر نمی باشد. لطفا با پشتیبانی تماس بگیرید.");
        }
        return view($this->view_path . "index", compact(["employment"]));
    }

    public function submit(Request $request, $key = "")
    {

        $verification_code = session("verification_code");
        $employment_id = session("employment_id");
        //گذاشتن کلید در سشن
        session()->put('employment_key', $key);

        if (!$verification_code || $verification_code != $request->verification_code) {
            return back()->withErrors("کد تایید به درستی وارد نشده است.");
        }

        $employment = Employment::find($employment_id);
        if (!$employment || $employment->key != $key) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است، لطفا مجدد تلاش کنید.");
        }
        if ($employment->status_id == 4640301) {
            $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
            if (!$result["result"]) {
                return back()->withErrors($result["error"]);
            }
     $result_check_national_code = Employment::CheckNationalCode($employment, env("IC_APIKEY"));
        }

//        $result_check_national_code["result"] = false;
        if (isset( $result_check_national_code) && $result_check_national_code["result"]) {
            // کد ملی در ic وجود دارد
            if(!isset($result_check_national_code["address"]["mobile"]) ||
                $result_check_national_code["address"]["mobile"]!=$employment->mobile
            ){
                return redirect()->route("login")->withErrors("شماره همراه و کدملی در زمان ثبت نام با شماره همراه و کدملی شما در منظومه داده ای دیاکو مطابقت ندارد."
//                .$result_check_national_code["address"]["mobile"]
                );
            }
            $next_route_to_exist_national_code = "hr.employment.register.personal_info.exist_national_code";
            return redirect()->route($next_route_to_exist_national_code, $employment->key);
        } else {
            $next_route = "hr.employment.register.personal_info.index";
            if ($employment->status_id == 4640107) { //در انتظار بارگزاری مدارک مرحله دوم
                $next_route = "hr.employment.register.personal.confirm_upload_document.index";
            }
            if (in_array($employment->status_id, [4640118, 4640115, 4640121])) { //در انتظار ثبت طب کار
                $next_route = "hr.employment.register.personal.work_medicine.index";
            }
            if (in_array($employment->status_id, [4640121, 4640115])) { //در انتظار تاییداطلاعات بانکی
                $next_route = "hr.employment.register.personal.bank_information.index";
            }
            $next_route_to_confirm_contract = "hr.employment.register.supplier.confirm_drafting_contract.index";
            if ($employment->status_id == 4640113) { //در انتظار تایدد قرارد دارد پیش نویس
                return redirect()->route($next_route_to_confirm_contract, $employment->key);
            }
            $next_route_to_confirm_contract_customer = "hr.employment.register.customer.confirm_drafting_contract.index";
            if ($employment->status_id == 4640123) {//در انتظار تایید قرارداد مشتری
                return redirect()->route($next_route_to_confirm_contract_customer, $employment->key);
            }
            $next_route_to_confirm_contract_contractor = "hr.employment.register.contractor.confirm_drafting_contract.index";
            if ($employment->status_id == 4640132) {//در انتظار تایید قرارداد پیمانکار
                return redirect()->route($next_route_to_confirm_contract_contractor, $employment->key);
            }
            return redirect()->route($next_route, $employment->key);
        }
    }

    public static function CheckEmploymentKey($employment)
    {
        $employment_key = session('employment_key');

        if ($employment_key && $employment->key == $employment_key) {
            return [
                "result" => true,
            ];
        } else {
            return [
                "result" => false,
                "error" => "اطلاعات درخواست همکاری نامعتبر می باشد. با پشتیبانی تماس بگیرید.",
            ];
        }
    }
}
