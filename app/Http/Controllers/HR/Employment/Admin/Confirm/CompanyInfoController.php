<?php

namespace App\Http\Controllers\HR\Employment\Admin\Confirm;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\ConfirmInfoController;
use App\Models\HR\Company\Company;
use App\Models\Customer\Customer;
use App\Models\HR\Employment\Employment;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use Illuminate\Http\Request;

class CompanyInfoController extends Controller
{
    protected $dashboard_path = "hr.employment.admin.dashboard.";

    public function confirm(Employment $employment)
    {
        $result = ConfirmInfoController::checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if ($employment->status_company_id != 4641401) {
            return redirect()->back()->withErrors("وضعیت در خواست همکاری جهت تایید اطلاعات آدرس نامعتبر است.");
        }
        if (!$employment->worker->personal_id_in_ic_system) {
            return back()->withErrors("لطفا ابتدا اطلاعات مدیر عامل را تایید نمایید.");
        }
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        //این شرط چک شده که حتما برای حقوقی ها این نمایش داده می شود.
        if ($employment->personal_type_id == 2) {
            //ارسال اطلاعات به ای سی
            $result_ic = Company::CreateCompanyInIc($employment, env("IC_APIKEY"));
            if (!$result_ic["result"]) {
                return back()->withErrors($result_ic["error"]);
            }
            if (isset($result_ic['personal_company']['id'])) {
                //ذخیره ایدی ای سی در دیتابیس
                $employment->company->company_id_in_ic_system = $result_ic['personal_company']['id'];
                $employment->company->save();
            }
//            Employment::PersonalApplication($employment->company->company_id_in_ic_system, $employment->cooperation_type_id, $employment->personal_type_id, $employment->nationality_id, env("APP_NAME"), env("IC_APIKEY"));
        }
        $employment->status_company_id = 4641402;//تایید
        $employment->save();

        //تایید اطلاعات
        $confirm_info = ConfirmInfoController::PostSubmit($employment);
        if (!$confirm_info['result']) {
            $employment->status_company_id = 4641401;
            $employment->save();
            return redirect()->back()->withErrors($confirm_info['error']);
        }


        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "تایید اطلاعات شرکت با موفقیت ثبت گردید."]);
    }

    public function reject(Employment $employment, Request $request)
    {
        $result = ConfirmInfoController::checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if ($employment->status_company_id != 4641401) {
            return redirect()->back()->withErrors("وضعیت در خواست همکاری جهت عملیات نامعتبر است.");
        }

        $employment->status_company_id = 4641403;//عدم تایید
        $employment->status_personal_id = 4641403;//عدم تایید
        $employment->status_id = 4640108;//در انتظار اصلاح فرم درخواست
        $employment->save();

        event(new EmploymentLogEvent($employment, 4640007, $request->message));

        $step_caption = "اطلاعات شرکت";
        ConfirmInfoController::SendSmsRejectInfo($employment, $request->message, $step_caption);

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "عدم تایید ادرس با موفقیت ثبت گردید."]);
    }


}
