<?php

namespace App\Http\Controllers\HR\Employment\Admin\Confirm;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\ConfirmInfoController;
use App\Models\HR\Employment\Employment;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/*
 این کنترلر مربوط به تایید یا عدم تاییداطلاعات فردی می باشد
 * */

class PersonalInfoController extends Controller
{
    protected $dashboard_path = "hr.employment.admin.dashboard.";
    protected $view_path = "hr.employment.admin.confirm.personal_info.";

    public function confirm(Employment $employment)
    {
        $result = ConfirmInfoController::checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        if ($employment->status_personal_id != 4641401) {
            return redirect()->back()->withErrors("وضعیت در خواست همکاری جهت تایید اطلاعات شخصی نامعتبر است.");
        }

        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        //ارسال اطلاعات به ای سی
        $result_ic = Worker::CreatePersonalInIc($employment, env("IC_APIKEY"));
        if (!$result_ic["result"]) {
            return back()->withErrors($result_ic["error"]);
        }

        if (isset($result_ic['personal']['id'])) {
            //ذخیره ایدی ای سی در دیتابیس
            $employment->worker->personal_id_in_ic_system = $result_ic['personal']['id'];
            $employment->worker->save();
        }
        //ارسال عکس شخصی
        $result_api_image_user = Worker::CallUserImageApi($employment, env('IC_URL'));
        if (!$result_api_image_user["result"]) {
            return back()->withErrors($result_api_image_user["error"]);
        }

        Employment::PersonalApplication($employment->worker->personal_id_in_ic_system, $employment->cooperation_type_id, $employment->personal_type_id, $employment->nationality_id, env("APP_NAME"), env("IC_APIKEY"));


        $employment->status_personal_id = 4641402;//تایید
        $employment->save();

        //تایید اطلاعات
        $confirm_info = ConfirmInfoController::PostSubmit($employment);
        if (!$confirm_info['result']) {
            $employment->status_personal_id = 4641401;
            $employment->save();
            return redirect()->back()->withErrors($confirm_info['error']);
        }


        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "تایید اطلاعات فردی با موفقیت ثبت گردید."]);

    }

    public function reject(Employment $employment, Request $request)
    {
        $result = ConfirmInfoController::checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        if ($employment->status_personal_id != 4641401) {
            return redirect()->back()->withErrors("وضعیت در خواست همکاری جهت عملیات نامعتبر است.");
        }
        $employment->status_personal_id = 4641403;//عدم تایید
        $employment->status_id = 4640108;//در انتظار اصلاح فرم درخواست
        $employment->save();

        event(new EmploymentLogEvent($employment, 4640006, $request->message));//عدم تاییداطلاعات شخصی


        $step_caption = "اطلاعات شخصی";
        ConfirmInfoController::SendSmsRejectInfo($employment, $request->message, $step_caption);

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => " عدم تایید اطلاعات فردی با موفقیت ثبت گردید."]);
    }
}
