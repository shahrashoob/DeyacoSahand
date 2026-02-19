<?php

namespace App\Http\Controllers\HR\Employment\Register\Personal;

use App\Http\Controllers\HR\Employment\Register\PersonalInfoController;
use App\Models\HR\Employment\Employment;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/*
این کنترلر برای تایید بارگزاری مدارک می باشد.
 * */

class ConfirmUploadDocumentController extends Controller
{
    // hr/employment/register/personal/confirm_upload_document
    protected $view_path = "hr.employment.register.personal.confirm_upload_document.";
    protected $route_path = "hr.employment.register.personal.confirm_upload_document.";

    public function index($key)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107])->
        first();
        if (!session()->has('upload_data')) {
            PersonalInfoController::ConfirmUploadDocument($employment);// تمام اطلاعات بارگزاری مدار ک را false کردم
        }
        $session = session('upload_data', []);

        $document_personal_upload = $session['upload_document_personal'] ?? [];
        $document_address_upload = $session['upload_document_address'] ?? [];

        $document_academic_degree_upload = $session['upload_document_academic_degree'] ?? [];
        $document_job_information_upload = $session['upload_user_job_information'] ?? [];
        $document_educational_course_upload = $session['upload_user_educational_courses'] ?? [];
        $document_user_dependent_upload = $session['upload_user_dependents'] ?? [];
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        // گام هایی دیپلم و ... که فرد مدرک بارگذاری کرده است.
        $current_receive_document_step_ids = UserAcademicDegree::join("academic_degree_types", "academic_degree_types.id", "academic_degree_type_id")->
        where("user_id", $employment->user_id)->
        pluck("receive_document_step_id");
        $current_receive_document_step_ids[] = -1;

        // آیا مدرک تحصیلی برای بارگذاری در مرحله دوم دارد؟
        $has_academic_degree_in_step2 = $employment->post->post_document_receive_step_confirms()->
        whereIn('receive_document_step_id', $current_receive_document_step_ids)->where('confirm_type', 2)->get();
        //مدارکی که بارگذاری کرده است
        $academic_degree_in_step2_upload = $employment->employment_document_types()->whereIn('receive_document_step_id', $current_receive_document_step_ids)->get();
        return view($this->view_path . "index", compact("employment", 'document_address_upload',
            'document_job_information_upload', 'document_educational_course_upload', 'document_academic_degree_upload',
            'document_personal_upload', 'academic_degree_in_step2_upload', 'document_address_upload', 'document_user_dependent_upload', "has_academic_degree_in_step2"));
    }

    public function submit($key)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107])->
        first();


        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }


        $result = Employment::NextStatus($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        } else {
            return redirect()->route("login")->with(["success" => "مدارک شما باموفقیت بارگزاری شد.<br/>اقدامات بعدی از طریق پیامک برای شما ارسال خواهد شد."]);
        }
    }

}
