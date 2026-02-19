<?php

namespace App\Http\Controllers\HR\Employment\Register\Personal;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\DeterminationWorkShiftController;
use App\Models\File\File;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Post\PostDocumentType;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
/*
این کنترلر برای  بارگزاری مدارک مرحله دوم .
 * */
class UploadFinalDocumentsController extends Controller
{
    protected $view_path = "hr.employment.register.personal.upload_final_document.";
    protected $route_path = "hr.employment.register.personal.upload_final_document.";
    protected $next_route = "hr.employment.register.other.index";


    public function index($key)
    {
        $employment = Employment::where("key", $key)->
        where("status_id", 4640107)-> // در حال تکمیل
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $document_personal = $this->view_upload_final_document($employment, 1);
        $document_address = $this->view_upload_final_document($employment, 2);

        $document_job_information = $this->view_upload_final_document($employment, 8)->count();
        $job_information_list = [];
        if ($document_job_information == 1) {
            $job_information_list = $employment->worker->user_job_informations()->get();
        }

        $document_job_information = $this->view_upload_final_document($employment, 9)->count();
        $educational_course_list = [];
        if ($document_job_information == 1) {
            $educational_course_list = $employment->worker->user_educational_courses()->get();
        }
        return view($this->view_path . "index", compact('document_personal', 'job_information_list','educational_course_list',
        'document_address', 'employment'));

    }

    public function submit($key, Request $request)
    {

        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107])-> // در حال تکمیل
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }


        $employment->status_id = 4640116;  //در انتظار تایید مشخصات مرحله دوم
        $employment->save();

        event(new EmploymentLogEvent($employment, 4640019, null, null, $employment->user_id));

        return redirect()->route("login")->with(["success" => "بارگزاری مدارک با موفقیت ثبت گردید "]);


    }

    public function view_upload_final_document($employment, $step_id)
    {

        return DocumentReceiveStepDocumentType::join('post_document_receive_step_confirms', 'document_receive_step_document_types.receive_document_step_id', 'post_document_receive_step_confirms.receive_document_step_id')->
        where([
            'post_document_receive_step_confirms.receive_document_step_id' => $step_id,
            'post_document_receive_step_confirms.post_id' => $employment->post_id,
            'post_document_receive_step_confirms.confirm_type' => 2
        ])->
        select('document_receive_step_document_types.document_type_id')->
        get();

    }
}
