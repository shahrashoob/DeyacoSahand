<?php

namespace App\Http\Controllers\HR\Employment\Register;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\ConfirmInfoController;
use App\Models\HR\Agent\Agent;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\HR\User\UserDependent;
use App\Models\HR\User\UserEducationalCourse;
use App\Models\HR\User\UserJobInformation;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ConfirmInformationController extends Controller
{
    protected $view_path = "hr.employment.register.confirm_information.";
    protected $route_path = "hr.employment.register.confirm_information.";

    public function index($key)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108, 4640115])-> // در حال تکمیل
        first();

        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        if (!$employment->user_id) {
            return back()->withErrors("لطفا ابتدا اطلاعات اولیه خود را تکمیل نمایید.");
        }
        if (!$employment->worker->user_address) {
            return back()->withErrors("لطفا ابتدا اطلاعات آدرس خود را تکمیل نمایید.");
        }

        // در حالت های زیر باید چک شود که اطلاعات تحصیلی و ... تکمیل شده است یا خیر
        if (in_array($employment->cooperation_type_id, [1, 11])) {
            if (!$employment->worker->military_information_id && $employment->worker->gender_id == 1 && $employment->nationality_id == 1) {

                return back()->withErrors("لطفا ابتدا اطلاعات خدمت سربازی خود را تکمیل نمایید.");
            }
            if ($employment->worker->user_academic_degrees()->count() == 0) {
                return back()->withErrors("لطفا ابتدا اطلاعات تحصیلی خود را تکمیل نمایید.");
            }
            if (!isset($employment->worker->image)) {
                return redirect()->route("hr.employment.register.personal_info.index", $employment->key)->withErrors("لطفا تصویر پرسنلی را بارگذاری نمایید.");
            }
        }
        $get_the_supplier_image = Setting::getIntegerValue("get_the_supplier_image");
        $get_the_customer_image = Setting::getIntegerValue("get_the_customer_image");
        $get_the_contractor_image = Setting::getIntegerValue("get_the_contractor_image");
        if ((in_array($employment->cooperation_type_id, [ 61]) && $get_the_supplier_image) ||
            (in_array($employment->cooperation_type_id, [ 31])) && $get_the_customer_image ||
            (in_array($employment->cooperation_type_id, [ 21]) && $get_the_contractor_image)) {
            if (!isset($employment->worker->image)) {
                return back()->withErrors('لطفا تصویر پرسنلی/ لوگو شرکت را بارگزاری نمایید.');
            }
        }
        if ($employment->personal_type_id == 2 && in_array($employment->cooperation_type_id, [3, 2, 6])) {
            $agents = Agent::where([
                'employment_id' => $employment->id,
                'status_id' => 4642001
            ])->get();
            if ($agents->count() != 0) {
                $error_message = "با توجه به اینکه افراد زیر درخواست نمایندگی را تایید نکرده اند، امکان تایید نهایی در خواست وجود ندارد. <br/>";
                foreach ($agents as $item) {
                    $error_message .= $item->worker->fullname() . "<br/>";
                }
                return back()->withErrors($error_message);
            }
        }

        $allow_delete = false;
        $allow_show_upload = false;
        $user_job_informations = UserJobInformation::where('user_id', $employment->user_id)->get();
        $user_educational_courses = UserEducationalCourse::where('user_id', $employment->user_id)->get();
        $user_academic_degrees = UserAcademicDegree::where('user_id', $employment->user_id)->get();
        $user_dependents = UserDependent::where('user_id', $employment->user_id)->get();


        return view($this->view_path . "index", compact("employment", 'allow_show_upload', 'user_dependents', 'user_job_informations', 'allow_delete', 'user_educational_courses', 'user_academic_degrees'));
    }


    public function submit(Request $request, $key)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108, 4640115])-> // در حال تکمیل
        first();

        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        if (!$employment->user_id) {
            return back()->withErrors("لطفا ابتدا اطلاعات اولیه خود را تکمیل نمایید.");
        }
        if (!$employment->worker->user_address) {
            return back()->withErrors("لطفا ابتدا اطلاعات آدرس خود را تکمیل نمایید.");
        }

        // در حالت های زیر باید چک شود که اطلاعات تحصیلی و ... تکمیل شده است یا خیر
        if (in_array($employment->cooperation_type_id, [1, 11])) {
            if ($employment->worker->user_academic_degrees()->count() == 0) {
                return back()->withErrors("لطفا ابتدا اطلاعات تحصیلی خود را تکمیل نمایید.");
            }


        }

        // در زمانی مه کارمندپاره وقت یا تمام وقت نیست
        if (!in_array($employment->cooperation_type_id, [1, 11])) {

            if ($employment->was_any_info_in_ic == 1) {//اگر اطلاعات قبل در ای سی بوده است
                if (in_array($employment->cooperation_type_id, [6, 61])) {
                    $employment->status_id = 4640110;//در انتظار تنظیم پیش نویس قرارداد هوشمندتامین کننده
                }
                if (in_array($employment->cooperation_type_id, [3, 31])) {
                    $employment->status_id = 4640122; //در انتظار تنظیم پیش نویس قرارداد هوشمند مشتریان
                }
                if (in_array($employment->cooperation_type_id, [2, 21])) {
                    $employment->status_id = 4640129;//در انتظار تنظیم پیش نویس قرارداد هوشمند (پیمانکار)
                }
                $employment->status_address_id = 4641402;
                $employment->save();
                Employment::SendSmsNextStatusForPost($employment);
                return redirect()->route("login")->with(["success" => "ثبت نام شما با موفقیت انجام شد.<br>درخواست شما در انتظار تنظیم پیش نویس قراداد هوشمند قرار گرفته است. اقدامات بعدی از طریق پیامک به شما اطلاع داده می شود."]);
            } else {
                if ($employment->status_id == 4640301) {//در صورتی که نبوده است و در وضعیت تکمیل فرم در خواست می باشد

                    switch ($employment->cooperation_type_id) {
                        case 1:
                        case 11:
                        case 4:
                        case 5:
                        $employment->status_id = 4640100;// باید در انتظار تایید مشخصات مرحله اول قرار بگیرد
                        break;
                        case 2:
                            $employment->status_id = 4640140;//  در انتظار تایید مشخصات مرحله اول (پیمانکار)
                            break;
                        case 3:
                            $employment->status_id = 4640139; // در انتظار تایید مشخصات مرحله اول (مشتری)
                            break;
                        case 6:
                            $employment->status_id = 4640141; // در انتظار تایید مشخصات مرحله اول (تامین کننده)
                            break;

                    }

                    $employment->save();


                    $employment->worker->status_id = 4620013; // // در انتظار بررسی مشخصات فردی
                    $employment->worker->save();
                    event(new EmploymentLogEvent($employment, 4640002, null, null, $employment->user_id));//درخواست اطلاعات
                    $status_token = "در انتظار تایید اطلاعات";
                    $template = "employmentregister6";
                    if ($employment->cooperation_type_id == 3) {
                        $rlt = Employment::SendSmsForCustomer($employment);
                        if ($rlt==true) {
                            self::SendSmsConfirmInfo($employment, $status_token, $template);
                        }
                    }
                    else{
                        self::SendSmsConfirmInfo($employment, $status_token, $template);
                    }


                    session()->flush();
                    return redirect()->route("login")->with(["success" => "ثبت نام شما با موفقیت انجام شد.<br>درخواست شما در انتظار تایید مشخصات قرار گرفته است. اقدامات بعدی از طریق پیامک به شما اطلاع داده می شود."]);
                }
            }
        }

        if (in_array($employment->cooperation_type_id, [1, 11])) {
            session()->flush();
            $result = Employment::NextStatus($employment);
            if (!$result["result"]) {
                return back()->withErrors($result["error"]);
            } elseif ($employment->was_any_info_in_ic == 1) {
                return redirect()->route("login")->with(["success" => "ثبت نام شما با موفقیت انجام شد.<br>درخواست شما در انتظار هماهنگی گزینش قرار گرفته است. اقدامات بعدی از طریق پیامک به شما اطلاع داده می شود."]);
            } else {
                return redirect()->route("login")->with(["success" => "ثبت نام شما با موفقیت انجام شد.<br>درخواست شما در انتظار تایید مشخصات قرار گرفته است. اقدامات بعدی از طریق پیامک به شما اطلاع داده می شود."]);
            }

        }

        if ($employment->status_id == 4640108) { // در انتظار اصلاح فرم درخواست
            session()->flush();
            $result = Employment::NextStatus($employment);
            if (!$result["result"]) {
                return back()->withErrors($result["error"]);
            } else {
                return redirect()->route("login")->with(["success" => "ثبت نام شما با موفقیت اصلاح گردید.<br>درخواست شما در انتظار تایید مشخصات قرار گرفته است. اقدامات بعدی از طریق پیامک به شما اطلاع داده می شود."]);
            }

        }

    }


    public static function SendSmsConfirmInfo(Employment $employment, $status_token, $template)
    {
        switch ($employment->cooperation_type_id) {
            case 1:
            case 11:
                $token10 = $employment->worker->fullname("with_gender_2");
                $token = $status_token;
                $token2 = null;
                $token3 = "_APP_NAME_" ;;
                $token20 = null;

                break;
            case 2://پیمانکار
            case 21://نماینده پیمانکار
                $token10 = $employment->worker->fullname("with_gender_2");
                $token = $status_token;
                $token2 = null;
                $token3 = "_APP_NAME_" ;;
                $token20 = null;
                break;
            case 6://تامین کننده
            case 61://تامین کننده
                $token10 = $employment->worker->fullname("with_gender_2");
                $token = $status_token;
                $token2 = null;
                $token3 = "_APP_NAME_" ;;
                $token20 = null;
                break;
            case 3:
            case 31:
                $token10 = $employment->worker->fullname();
                $token = $status_token;
                $token2 = null;
                $token3 = "_APP_NAME_" ;;
                $token20 = null;

                break;
        }

        Notification::send("00" . ($employment->mobile_country->area_code ?? "98") . $employment->mobile,
            new SMSNotification($template,
                $token,
                $token2,
                $token3,
                $token10,
                $token20));

    }


}
