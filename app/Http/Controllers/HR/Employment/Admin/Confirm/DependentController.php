<?php

namespace App\Http\Controllers\HR\Employment\Admin\Confirm;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\ConfirmInfoController;
use App\Models\HR\Employment\Employment;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\HR\User\UserDependent;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use Illuminate\Http\Request;

/*
 این کنترلر مربوط به تایید یا عدم تاییداطلاعات افراد تحت تکفل می باشد
 * */

class DependentController extends Controller
{
    protected $dashboard_path = "hr.employment.admin.dashboard.";

    public function confirm(Employment $employment)
    {
        $result = ConfirmInfoController::checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if ($employment->status_dependent_id != 4641401) {
            return redirect()->back()->withErrors("وضعیت در خواست همکاری جهت تایید اطلاعات تحصیلی نامعتبر است.");
        }

        //تایید اطلاعات شخصی در اولین مرحله باید صورت بگیرد
        if (!$employment->worker->personal_id_in_ic_system) {
            return back()->withErrors("لطفا ابتدا اطلاعات شخصی را تایید نمایید.");
        }
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        //ارسال اطلاعات به ای سی
        $result_ic = UserDependent::CreateDependentInIc($employment, env("IC_APIKEY"));
        if (!$result_ic["result"]) {
            return back()->withErrors($result_ic["error"]);
        }

        $employment->status_dependent_id = 4641402;//تایید
        $employment->save();

        //تایید اطلاعات
        $confirm_info = ConfirmInfoController::PostSubmit($employment);

        if (!$confirm_info['result']) {

            $employment->status_dependent_id = 4641401;
            $employment->save();
            return redirect()->back()->withErrors($confirm_info['error']);
        }
        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "تایید اطلاعات افراد تحت تکفل با موفقیت ثبت گردید."]);

    }

    public function reject(Employment $employment, Request $request)
    {
        $result = ConfirmInfoController::checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if ($employment->status_dependent_id != 4641401) {
            return redirect()->back()->withErrors("وضعیت در خواست همکاری جهت عملیات نامعتبر است.");
        }

        $employment->status_dependent_id = 4641403;//عدم تایید
        $employment->status_id = 4640108;//در انتظار اصلاح فرم درخواست
        $employment->save();

        event(new EmploymentLogEvent($employment, 4640024, $request->message));//عدم تاییداطلاعات افراد تحت تکفل

        $step_caption = "اطلاعات افراد تحت تکفل";
        ConfirmInfoController::SendSmsRejectInfo($employment, $request->message, $step_caption);

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "عدم تایید اطلاعات افراد تحت تکفل با موفقیت ثبت گردید."]);
    }
}
