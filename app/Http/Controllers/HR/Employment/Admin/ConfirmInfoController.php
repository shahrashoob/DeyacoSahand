<?php

namespace App\Http\Controllers\HR\Employment\Admin;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Models\HR\Agent\Agent;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentSelection;
use App\Models\HR\Employment\EmploymentSelectionSelector;
use App\Models\HR\Selection\SelectionPostSetting;
use App\Models\HR\Selection\SelectionSelector;
use App\Models\Post\PostDocumentType;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/*
   می باشد این کنترلر مربوط به تایید اطلاعات کاربر
 * */

class ConfirmInfoController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.confirm_info.",
        "enable_status" => ["100", "139", "140", "141", "108", "109", "116", "141"],
        "button" => ["caption" => "تایید اطلاعات (کارمند، مشتری، تامین کننده و پیمانکار)", "class" => "btn-primary"],
        "view_path" => "",
        "hidden_button" => true

    ];

    public static function PostSubmit(Employment $employment)
    {
        switch ($employment->personal_type_id) {
            case 1: // حقیقی
                switch ($employment->cooperation_type_id) {
                    case 1: //کارمند تمام وقت
                    case 11://وکارمند پاره وقت
                        $employment = Employment::NextStatus($employment);
                        if (!$employment['result']) {
                            return [
                                "result" => false,
                                'error' => $employment['error']
                            ];
                        } else {
                            return [
                                "result" => true,
                            ];
                        }
//                        //تابع تایید اطلاعات کارمند
//                        self::confirmEmployeeInformation($employment);
                        break;


                    case 6://تامین کننده
                        if ($employment->status_personal_id == 4641402 && $employment->status_address_id == 4641402) {

                            $employment->status_id = 4640110;//تایید اطلاعات مالی
                            $employment->save();
                            event(new EmploymentLogEvent($employment, 4640002,));//اطلاعات تایید شده

                        }
                        break;

                    case 3:// مشتری
                        if ($employment->status_personal_id == 4641402 && $employment->status_address_id == 4641402) {

                            $employment->status_id = 4640122;//در انتظار تنظیم پیش نویس قراداد هوشمند مشتری
                            $employment->save();
                            event(new EmploymentLogEvent($employment, 4640002));//اطلاعات تایید شده
                            self::SendSmsConfirmInfo($employment);
                        }
                        break;
                    case 31://نماینده مشتری
                        if ($employment->status_personal_id == 4641402) {

                            $employment->status_id = 4640135;//در انتظار تایید نمایندگی
                            $employment->save();
                            $agent = Agent::where('user_id', $employment->user_id)->first();
                            //اسمس برا نماینده می رود که نمایندگی خود را تایید کن
                            \App\Http\Controllers\Customer\AgentController::SendSmsForAgent($employment, $agent);
                            event(new EmploymentLogEvent($employment, 4640002));//اطلاعات تایید شده
                        }
                        break;
                    case 2:// پیمانکار
                        if ($employment->status_personal_id == 4641402 && $employment->status_address_id == 4641402) {

                            $employment->status_id = 4640129;//در انتظار تنظیم پیش نویس قراداد هوشمند پیمانکار
                            $employment->save();
                            event(new EmploymentLogEvent($employment, 4640002));//اطلاعات تایید شده
                        }
                        break;

                    case 21://نماینده پیمانکار
                        if ($employment->status_personal_id == 4641402) {

                            $employment->status_id = 4640135;//در انتظار تایید نمایندگی
                            $employment->save();
                            $agent = Agent::where('user_id', $employment->user_id)->first();
                            //اسمس برا نماینده می رود که نمایندگی خود را تایید کن
                            \App\Http\Controllers\Customer\AgentController::SendSmsForAgent($employment, $agent);
                            event(new EmploymentLogEvent($employment, 4640002));//اطلاعات تایید شده
                        }
                        break;
                    case 61://نماینده تامین کننده
                        if ($employment->status_personal_id == 4641402) {

                            $employment->status_id = 4640135;//در انتظار تایید نمایندگی
                            $employment->save();
                            $agent = Agent::where('user_id', $employment->user_id)->first();
                            //اسمس برا نماینده می رود که نمایندگی خود را تایید کن
                            \App\Http\Controllers\Customer\AgentController::SendSmsForAgent($employment, $agent);
                            event(new EmploymentLogEvent($employment, 4640002));//اطلاعات تایید شده

                        }
                        break;
                }

            case 2: // حقوقی

                switch ($employment->cooperation_type_id) {
                    case 6://تامین کننده
                        if ($employment->status_personal_id == 4641402 && $employment->status_address_id == 4641402 && $employment->status_company_id == 4641402) {

                            $employment->status_id = 4640110;//تایید اطلاعات مالی
                            $employment->save();
                            event(new EmploymentLogEvent($employment, 4640002,));//اطلاعات تایید شده
                        }
                        break;
                    case 3:// مشتری
                        if ($employment->status_personal_id == 4641402 && $employment->status_address_id == 4641402 && $employment->status_company_id == 4641402) {

                            $employment->status_id = 4640122;//در انتظار تنظیم پیش نویس قراداد هوشمند مشتری
                            $employment->save();
                            event(new EmploymentLogEvent($employment, 4640002));//اطلاعات تایید شده
                            self::SendSmsConfirmInfo($employment);
                        }
                        break;
                    case 2://پیمانکار
                        if ($employment->status_personal_id == 4641402 && $employment->status_address_id == 4641402 && $employment->status_company_id == 4641402) {

                            $employment->status_id = 4640129;//در انتظار تنظیم پیش نویس قراداد هوشمند پیمانکار
                            $employment->save();
                            event(new EmploymentLogEvent($employment, 4640002));//اطلاعات تایید شده

                        }
                        break;
                }

        }

        return [
            'result' => true,
        ];
    }

    /*
     * ارسال پیامک تایید اطلاعات
     */
    public static function SendSmsConfirmInfo(Employment $employment)
    {
        switch ($employment->cooperation_type_id) {
            case 1:
            case 11:
                // پیامک تغییر وضعیت
                $token10 = $employment->worker->fullname("with_gender_2");
                $token = "در سامانه" . Setting::getStringValue('software_name');
                $token2 = "مصاحبه";

                Notification::send("00" . ($employment->mobile_country->area_code ?? "98") . $employment->mobile,
                    new SMSNotification("employmentconfirmtype1",
                        $token,
                        $token2,
                        $token10,
                    ));
                break;
            case 3: // مشتری

                $rlt = Employment::SendSmsForCustomer($employment);
                if ($rlt == false) {
                    return true;
                }

                $token = "_APP_NAME_";
                $token10 = $employment->worker->fullname("with_gender_2");
                if ($employment->customer && $employment->customer->company) {
                    $token10 = $token10
                        . ".مدیرعامل.محترم." . $employment->customer->company->caption;
                }
                $token20 = " سامانه" . Setting::getStringValue('software_name');


                Notification::send("00" . ($employment->mobile_country->area_code ?? "98") . $employment->mobile,
                    new SMSNotification("employmentcustomerconfirminfo",
                        $token,
                        null,
                        null,
                        $token10,
                        $token20,
                    ));

                break;

        }
    }

    /*
     * ارسال پیامک عدم تایید
     */
    public static function SendSmsRejectInfo(Employment $employment, $reason, $step_caption)
    {
        switch ($employment->cooperation_type_id) {
            case 1:
            case 11:
                // پیامک تغییر وضعیت
                $token10 = $employment->worker->fullname("with_gender_2");
                $token = $step_caption;
                $token2 = $reason;
                $token3 = "_APP_NAME_" . "/employment/register";;
                $token20 = null;

                Notification::send("00" . ($employment->mobile_country->area_code ?? "98") . $employment->mobile,
                    new SMSNotification("employmentrejectinfo",
                        $token,
                        $token2,
                        $token3,
                        $token10,
                        $token20));
                break;
            case 2:
            case 21:
            case 3:
            case 31:
            case 6:
            case 61:

                break;
        }
    }


    public static function checkPermission(Employment $employment)
    {

        $result = DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }


    public static function confirmInfo(Employment $employment)
    {
        if ($employment->status_id == 4640100 && $employment->status_personal_id == 4641402 && $employment->status_address_id == 4641402 &&
            $employment->status_educational_course_id == 4641402 && $employment->status_job_information_id == 4641402 &&
            $employment->status_academic_degree_id == 4641402 && $employment->status_upload_document_id == 4641402) {
            return [
                'result' => true,
            ];
        }
        return [
            'result' => false,
        ];
    }

    public static function confirmEmployeeInformation(Employment $employment)
    {
//        //در صورتی که همه اطلاعات تایید شد وضعیت کارمند خاتمه یافته می شود
//        $confirm_info = self::confirmInfo($employment);
//
//        if ($confirm_info['result']) {
//            $employment->status_id = 4640106; // خاتمه یافته
//            $employment->save();
//
//            $employment->worker->status_id = 4620012; // در انتظار شروع همکاری
//            $employment->worker->save();
//            self::SendSmsConfirmInfo($employment);
//            event(new EmploymentLogEvent($employment, 4640002, null, null));
//
//            // اضافه کردن فرد به پست سازمانی در صورتی که کارمند است.
//            Employment::AddUserToPost($employment);
//        }

        if ($employment->employment_selections->count() == 0) {

            $employment->worker->status_id = 4620014;//در صورت تایید اطلاعات وضعیت کاربر در حال استخدام می شود.
            $employment->worker->save();

            $selection_post_settings = SelectionPostSetting::
            where('post_id', $employment->post_id)->
            get();


            $min_priority_number_post_setting = SelectionPostSetting::
            where('post_id', $employment->post_id)->
            orderBy('priority_number')->
            first();

            //ایجاد جداول بعد از تغییر وضعیت
            foreach ($selection_post_settings as $selection_post_setting) {


                // اضافه کردن پست های سازمانی
                $selection_selectors = SelectionSelector::
                where('post_id', $employment->post_id)->
                where('selection_id', $selection_post_setting->selection_id)->
                whereNotNull("post_selection_id")->
                get();


                foreach ($selection_selectors as $selection_selector) {

                    $employment_selection = EmploymentSelection::create([
                        'selection_id' => $selection_post_setting->selection_id,
                        'minimum_score_to_confirm_selection' => $selection_post_setting->minimum_score_to_confirm_selection,
                        'priority_number' => $selection_post_setting->priority_number,
                        'status_id' =>
                            $selection_post_setting->priority_number == $min_priority_number_post_setting->priority_number ?
                                4640102 : 4640101,
                        'employment_id' => $employment->id,
                    ]);

                    EmploymentSelectionSelector::create([
                        'selection_id' => $selection_selector->selection_id,
                        'post_id' => $selection_selector->post_selection_id,//گزینش کننده
                        'employment_id' => $employment->id,
                        'employment_selection_id' => $employment_selection->id
                    ]);
                }

                //اضافه کردن پست های سازمانی که در هر کمیته وجود دارد.

//                                    $selection_selectors = SelectionSelector::
//                                    where('post_id', $employment->post_id)->
//                                    where('selection_id', $selection_post_setting->selection_id)->
//                                    whereNotNull("committee_id")->
//                                    get();
//                                    foreach ($selection_selectors as $selection_selector) {
//
//                                        foreach ($selection_selector->committee->committee_post as $committee_post) {
//                                            EmploymentSelectionSelector::create([
//                                                'selection_id' => $selection_selector->selection_id,
//                                                'post_id' => $committee_post->post_id,
//                                                'committee_id' => $selection_selector->committee_id,
//                                                'minimum_percent_of_committee' => $selection_selector->minimum_percent_of_committee,
//                                                'employment_id' => $employment->id,
//                                                'employment_selection_id' => $employment_selection->id
//                                            ]);
//                                        }
//                                    }
            }


            //در خواست اولویت را برابر با اولویت جاری بگذار
            $min_priority_number = $employment->
            employment_selections()->
            orderBy('priority_number')->
            first();

            if ($min_priority_number) {
                $employment->current_priority_number = $min_priority_number->priority_number;
            }


            //آیا گزینشی برا یا این پست وجود دارد یا خیر؟در صورتی که بود در انتظار هماهنگی در صورتی که نبود تحوبل مدارک
            if ($selection_post_settings) {
                $employment->status_id = 4640102; // در انتظار هماهنگی ***
            } else {
                $post_document_type = PostDocumentType::
                where('post_id', $employment->post_id)->
                get();
                if ($post_document_type) {
                    $employment->status_id = 4640107; // بارگزاری مدارک
                } else {
                    $employment->status_id = 4640106; // خاتمه یافته

                    $employment->worker->status_id = 4620012; // در انتظار شروع همکاری
                    $employment->worker->save();
                }
            }

            $employment->save();

            event(new EmploymentLogEvent($employment, 4640002, null, null, $employment->user_id));


        }
    }
}
