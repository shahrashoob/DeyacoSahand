<?php

namespace App\Http\Controllers\HR\Employment\Register\Personal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Register\PersonalInfoController;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\HR\User\UserDependent;
use App\Models\HR\User\UserJobInformation;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/*
این کنترلر برای  ثبت اطلاعات افراد تحت تکلف می باشد.
 * */

class DependentContoller extends Controller
{
    protected $view_path = "hr.employment.register.personal.dependent.";
    protected $route_path = "hr.employment.register.personal.dependent.";
    protected $next_rout_upload_confirm = "hr.employment.register.personal.confirm_upload_document.index";
    public function index($key)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108])-> // در حال تکمیل
        first();

        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        if (!$employment->user_id) {
            return back()->withErrors("لطفا ابتدا اطلاعات اولیه خود را تکمیل نمایید.");
        }
        if ($employment->status_academic_degree_id != 4641402) {
            $allow_delete = true;
        } else {
            $allow_delete = false;
        }
        $allow_show_upload = true;
        $request = session("request_user_dependent");
        if (!$request) {
            $request["dependent_type_id"] = null;
            $request["first_name"] = null;
            $request["last_name"] = null;
            $request["national_code"] = null;
            $request["date_of_birth"] = null;
        }
        $document_receive_step_document_type_list = DocumentReceiveStepDocumentType::where("receive_document_step_id", 12)->get();
        $dependent_type_option = Option::get("dependent_type");
        $post_document_receive_step_confirm = PostDocumentReceiveStepConfirm::where([
            'post_id' => $employment->post_id,
            "receive_document_step_id" => 12,
            'is_necessary_to_deliver_document_to_archive' => 1,
        ])->first();
        return view($this->view_path . "index", compact("employment", 'dependent_type_option', 'request', 'allow_delete',
            'allow_show_upload', 'document_receive_step_document_type_list', 'post_document_receive_step_confirm'));
    }

    public function submit(Request $request, $key)
    {

        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108])-> // در حال تکمیل
        first();

        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        $user_dependent = UserDependent::create([
            'user_id' => $employment->user_id,
            "dependent_type_id" => $request->dependent_type_id,
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "national_code" => $request->national_code,
            "date_of_birth" => $request->date_of_birth

        ]);
        $employment_document_type = EmploymentDocumentType::CreateEmploymentDocumentType($employment, $request, 12, $user_dependent->id);

        if (!$employment_document_type["result"]) {
            $user_dependent->delete();
            session([
                "request_user_dependent" => $request->only([
                    "dependent_type_id",
                    "first_name",
                    "last_name",
                    "national_code",
                    "date_of_birth"
                ])
            ]);
            return back()->withErrors($employment_document_type["error"]);
        } else {
            if ($employment_document_type["count"] > 0) {
                $employment->status_dependent_id = 4641401; // در انتظار بررسی
                $employment->save();
            }
            session(["request_user_dependent" => null]);
            return back()->with(["success" => "یک فرد تحت تکفل موفقیت اضافه گردید."]);
        }


    }

    public function upload($key, UserDependent $user_dependent)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107, 4640108])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        $document_receive_step_document_type_list = DocumentReceiveStepDocumentType::where("receive_document_step_id", 12)->get();
        return view($this->view_path . "upload", compact('employment', 'user_dependent', 'document_receive_step_document_type_list'));
    }

    public function submit_upload($key, Request $request, UserDependent $user_dependent)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107, 4640108])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        $employment_document_type = EmploymentDocumentType::CreateEmploymentDocumentType($employment, $request, 12, $user_dependent->id);
        if (!$employment_document_type["result"]) {
            return back()->withErrors($employment_document_type["error"]);
        } else {

            if ($employment_document_type["count"] > 0) {
                $employment->status_dependent_id = 4641401; // در انتظار بررسی
                $employment->save();
            }
            $document_dependent = PersonalInfoController::viewUploadDocument($employment, 8);
            $session = session('upload_data');
            if ($document_dependent) {
                $user_dependents = $employment->worker->user_dependents()->get();
                foreach ($user_dependents as $item) {
                    $session['upload_user_dependents'][12][$item->id] = true;
                }
            }
            session(['upload_data' => $session]);
            if ($employment->status_id == 4640107) {
                return redirect()->route($this->next_rout_upload_confirm, $employment->key)->with(["success" => "اطلاعات افراد تحت تکفل با موفقیت بارگذاری شد."]);
            }
            return redirect()->route($this->route_path . 'index', [$employment->key, $user_dependent])->with(["success" => "اطلاعات افراد تحت تکفل با موفقیت ثبت شد."]);

        }
    }


    public function destroy($key, UserDependent $user_dependent)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108])-> // در حال تکمیل
        first();
        $employment_document_type = EmploymentDocumentType::where([
            'employment_id' => $employment->id,
            'receive_document_step_id' => 12,
            'other_id' => $user_dependent->id,

        ])->first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        if ($employment->user_id != $user_dependent->user_id) {
            return back()->withErrors("اطلاعات تحت تکفل جهت حذف نامعتبر است.");
        }
        if ($employment_document_type) {
            $employment_document_type->delete();
            $employment_document_type->file->delete();
        }

        $user_dependent->delete();

        return back()->with(["success" => "یک فرد تحت تکفل با موفقیت حذف گردید."]);

    }

    //تابع دانلود
    public function download(Employment $employment, EmploymentDocumentType $employment_document_type)
    {


        if (!in_array($employment->status_id, [4640301, 4640107, 4640108])) {
            return redirect()->back()->withErrors("فایل جهت دانلود یافت نشد");
        }

        $path = public_path($employment_document_type->file->path);
        $fileName = $employment_document_type->file->caption;


        return Response::download($path, $fileName, ['Content-Type: application']);

    }
}
