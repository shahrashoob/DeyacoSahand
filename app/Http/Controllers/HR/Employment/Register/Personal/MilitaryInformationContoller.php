<?php

namespace App\Http\Controllers\HR\Employment\Register\Personal;

use App\Http\Controllers\Controller;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\Personal\MilitaryInformation;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\HR\User\UserAddress;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
/*
این کنترلر برای  ثبت اطلاعات سربازی می باشد.
 * */
class MilitaryInformationContoller extends Controller
{
    protected $view_path = "hr.employment.register.personal.military_information.";
    protected $route_path = "hr.employment.register.personal.military_information.";
    protected $next_route = "hr.employment.register.other.index";
    protected $next_rout_upload_confirm = "hr.employment.register.personal.confirm_upload_document.index";

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index($key, $academic_degree_type_id = 0)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108])->
        first();

        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $military_option = Option::get("military", $employment->worker->military_information_id ?? "");
        $document_receive_step_document_type_list = DocumentReceiveStepDocumentType::where("receive_document_step_id", 11)->get();

        return view($this->view_path . "index", compact('employment', 'military_option', 'document_receive_step_document_type_list'));

    }

    public function submit(Request $request, $key)
    {

        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108])->
        first();

        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $worker = $employment->worker;
        $employment->worker->military_information_id = $request->military_information_id;
        $worker->save();
        $employment_document_type = EmploymentDocumentType::CreateEmploymentDocumentType($employment, $request, 11, null);
        if (!$employment_document_type["result"]) {
            return back()->withErrors($employment_document_type["error"]);
        }
        $employment->status_personal_id = 4641401; // در انتظار بررسی
        $employment->save();

        if ($employment->status_id == 4640107) {
            return redirect()->route($this->next_rout_upload_confirm, $employment->key)->with(["success" => "اطلاعات خدمت سربازی با موفقیت بارگذاری گردید."]);
        }
        return redirect()->route($this->next_route, $employment->key)->with(["success" => "اطلاعات خدمت سربازی با موفقیت ثبت گردید."]);
    }

}
