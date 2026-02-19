<?php

namespace App\Http\Controllers\HR\Employment\Admin\Confirm;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\ConfirmInfoController;
use App\Http\Controllers\re;
use App\Models\HR\Employment\Employment;
use App\Models\HR\User\UserAddress;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/*
 این کنترلر مربوط به تایید یا عدم تاییداطلاعات آدرس می باشد
 * */

class AddressController extends Controller
{
    protected $dashboard_path = "hr.employment.admin.dashboard.";

    public function confirm(Employment $employment)
    {
        $result = ConfirmInfoController::checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if ($employment->status_address_id != 4641401) {
            return redirect()->back()->withErrors("وضعیت در خواست همکاری جهت تایید اطلاعات آدرس نامعتبر است.");
        }
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        //        تایید اطلاعات شخصی در اولین مرحله باید صورت بگیرد
        self::ValidateAdderesINIc($employment);
        //ارسال اطلاعات به ای سی
         $result_ic = UserAddress::CreateAddressInIc($employment, env("IC_APIKEY"));
        if (!$result_ic["result"]) {
            return back()->withErrors($result_ic["error"]);
        }
        if ($employment->personal_type_id == 1) {
            //ذخیره ایدی ای سی در دیتابیس
            $address = $employment->worker->user_address()->first()->address;
            $address->address_id_in_ic_system = $result_ic['personal_address']['id'];
            $address->save();
        }else{
            $address = $employment->company->user_address()->first()->address;
            $address->address_id_in_ic_system = $result_ic['personal_address']['id'];
            $address->save();
        }

        $employment->status_address_id = 4641402;//تایید
        $employment->save();

        //تایید اطلاعات
        $confirm_info = ConfirmInfoController::PostSubmit($employment);
        if (!$confirm_info['result']) {
            $employment->status_address_id = 4641401;
            $employment->save();
            return redirect()->back()->withErrors($confirm_info['error']);
        }


        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "تایید آدرس با موفقیت ثبت گردید."]);
    }

    public function reject(Employment $employment, Request $request)
    {
        $result = ConfirmInfoController::checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if ($employment->status_address_id != 4641401) {
            return redirect()->back()->withErrors("وضعیت در خواست همکاری جهت عملیات نامعتبر است.");
        }

        $employment->status_address_id = 4641403;//عدم تایید
        $employment->status_id = 4640108;//در انتظار اصلاح فرم درخواست
        $employment->save();

        event(new EmploymentLogEvent($employment, 4640007, $request->message));//عدم تاییداطلاعات آدرس

        $step_caption = "اطلاعات آدرس";
        ConfirmInfoController::SendSmsRejectInfo($employment, $request->message, $step_caption);

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "عدم تایید ادرس با موفقیت ثبت گردید."]);
    }

    public static function ValidateAdderesINIc(Employment $employment)
    {
        switch ($employment->personal_type_id) {
            case 1:
                if (!$employment->worker->personal_id_in_ic_system) {
                    return back()->withErrors("لطفا ابتدا اطلاعات شخصی را تایید نمایید.");
                }
                break;
            case 2:
                if (!$employment->company->company_id_in_ic_system) {
                    return back()->withErrors("لطفا ابتدا اطلاعات شرکت را تایید نمایید.");
                }
                break;

        }
    }
}
