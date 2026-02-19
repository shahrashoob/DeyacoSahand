<?php

namespace App\Http\Controllers\HR\Employment\Admin\Confirm;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\ConfirmInfoController;
use App\Models\File\File;
use App\Models\HR\Education\Education;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\Post\PostStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
/*
 این کنترلر مربوط به تایید یا عدم تاییداطلاعات مدارک بارگزاری شده می باشد که در اپدیت جدید تایید وعدم تایید ندارد ولی تابع دانلود در اینجا است
 * */
class UploadDocumentController extends Controller
{
    protected $dashboard_path = "hr.employment.admin.dashboard.";

    public function confirm(Employment $employment)
    {
        $result = ConfirmInfoController::checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if ($employment->status_id != 4640109) {
            return redirect()->back()->withErrors("وضعیت در خواست همکاری جهت تایید اطلاعات  نامعتبر است.");
        }

        $employment->status_upload_document_id = 4641402;//تایید
        $employment->save();

        $employment->status_id = 4640100;//در انتظار تایید اطلاعات
        $employment->save();

        //تایید اطلاعات
        $confirm_info = ConfirmInfoController::PostSubmit($employment);
        if (!$confirm_info['result']) {
            $employment->status_upload_document_id = 4641401;
            $employment->save();
            return redirect()->back()->withErrors($confirm_info['error']);
        }


        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "تایید مدارک بارگزاری شده با موفقیت ثبت گردید."]);
    }

    public function reject(Employment $employment, Request $request)
    {
        $result = ConfirmInfoController::checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if ($employment->status_id != 4640109) {
            return redirect()->back()->withErrors("وضعیت در خواست همکاری جهت تایید اطلاعات  نامعتبر است.");
        }

        $employment->status_upload_document_id = 4641403;//عدم تایید
        $employment->save();

        $employment->status_id = 4640107;//در انتظار بارگزاری مدارک
        $employment->save();

        $employment->employment_document_types()->delete();

        event(new EmploymentLogEvent($employment, 4640011, $request->message));//عدم تایید مدارک بارگزاری شده

        $step_caption = "مدارک بارگزاری شده";
        ConfirmInfoController::SendSmsRejectInfo($employment, $request->message, $step_caption);

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "عدم تایید مدارک بارگزاری شده با موفقیت ثبت گردید."]);
    }

    //تابع دانلود
    public function download(Employment $employment, EmploymentDocumentType $employment_document_type)
    {
        $allowed_status_ids = PostStatus::getAllowedStatus(5);

        if (!in_array($employment->status_id, $allowed_status_ids)) {
            return redirect()->back()->withErrors("فایل جهت دانلود یافت نشد");
        }

        $path = public_path($employment_document_type->file->path);
        $fileName = $employment_document_type->file->caption;


        return Response::download($path, $fileName, ['Content-Type: application']);

    }
}
