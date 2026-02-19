<?php

namespace App\Http\Controllers\HR\Employment\Register\Personal;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Register\PersonalInfoController;
use App\Models\File\File;
use App\Models\HR\Education\Education;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\Employment\EmploymentLog;
use App\Models\HR\Personal\AcademicDegree;
use App\Models\HR\Personal\AcademicDegreeType;
use App\Models\HR\Personal\Gender;
use App\Models\HR\Personal\JobInformation;
use App\Models\HR\Personal\MaritalStatus;
use App\Models\HR\Personal\Nationality;
use App\Models\HR\Personal\PersonalType;
use App\Models\HR\User\CooperationType;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\HR\User\UserJobInformation;
use App\Models\Post\Post;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\User;
use App\Models\Utility\Address\Address;
use App\Models\Utility\Address\Country;
use App\Models\Utility\Address\Province;
use App\Models\HR\User\UserAddress;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use App\Models\Utility\Option;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Morilog\Jalali\Jalalian;

class JobInformationController extends Controller
{
    protected $view_path = "hr.employment.register.personal.job_information.";
    protected $route_path = "hr.employment.register.personal.job_information.";
    protected $next_route = "hr.employment.register.personal.educational_course.index";
    protected $next_rout_upload_confirm = "hr.employment.register.personal.confirm_upload_document.index";

    public function __construct()
    {
        $this->middleware('guest');
    }

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
        if (!$employment->worker->user_address) {
            return back()->withErrors("لطفا ابتدا اطلاعات آدرس خود را تکمیل نمایید.");
        }
        if (!$employment->worker->user_academic_degrees) {
            return back()->withErrors("لطفا ابتدا اطلاعات تحصیلی خود را تکمیل نمایید.");
        }
        //اطلاعات را سشن می ریزیم تا فرم پاک نشود.
        $request = session("request_job_information");
        if (!$request) {
            $request["start_date_of_work"] = null;
            $request["end_date_of_work"] = null;
            $request["post_caption"] = null;
            $request["company_name_of_work"] = null;
            $request["address_of_work"] = null;
            $request["identifier_name"] = null;
            $request["identification_number"] = null;
        }
        if ($employment->status_job_information_id != 4641402) {
            $allow_delete = true;
        } else {
            $allow_delete = false;
        }
        $allow_show_upload = true;





        $document_receive_step_document_type_list = DocumentReceiveStepDocumentType::where("receive_document_step_id", 8)->get();
        $user_job_informations = UserJobInformation::where('user_id', $employment->user_id)->get();

        $post_document_receive_step_confirm=PostDocumentReceiveStepConfirm::where([
            'post_id'=>$employment->post_id,
            "receive_document_step_id" =>8,
            'is_necessary_to_deliver_document_to_archive'=>1,
        ])->first();

        return view($this->view_path . "index", compact("employment", 'allow_show_upload','post_document_receive_step_confirm',
            'request', 'user_job_informations', 'allow_delete', 'document_receive_step_document_type_list'));
    }

    public function submit(Request $request, $key)
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
        if (!$employment->worker->user_address) {
            return back()->withErrors("لطفا ابتدا اطلاعات آدرس خود را تکمیل نمایید.");
        }
        if (!$employment->worker->user_academic_degrees) {
            return back()->withErrors("لطفا ابتدا اطلاعات تحصیلی خود را تکمیل نمایید.");
        }
        if ($employment->worker->user_job_informations->count() >= 15) {
            return back()->withErrors(" ثبت سابقه شغلی بیش از  15 مورد امکان پذیر نمی باشد. ");
        }


        $employment->worker->insurance_number = $request->insurance_number;
        $employment->worker->save();


        $request->validate([
            "start_date_of_work" => ['required'],
            "end_date_of_work" => ['required'],
            "post_caption" => ['required'],
            "company_name_of_work" => ['required'],
            "address_of_work" => ['required'],
            "identifier_name" => ['required'],
            "identification_number" => ['required'],
        ]);

        if (strlen($request->identification_number) !== 11) {
            return back()->withErrors("شماره معرف باید 11 رقم باشد.");
        }

        $user_job_inforamation = UserJobInformation::create([
            'user_id' => $employment->user_id,
            'start_date_of_work' => $request->start_date_of_work,
            'end_date_of_work' => $request->end_date_of_work,
            'post_caption' => $request->post_caption,
            'company_name_of_work' => $request->company_name_of_work,
            'address_of_work' => $request->address_of_work,
            'identifier_name' => $request->identifier_name,
            'identification_number' => $request->identification_number,
        ]);

        $employment_document_type = EmploymentDocumentType::CreateEmploymentDocumentType($employment, $request, 8, $user_job_inforamation->id);

        if (!$employment_document_type["result"]) {
            $user_job_inforamation->delete();
            session([
                "request_job_information" => $request->only([
                    "start_date_of_work",
                    "end_date_of_work",
                    "post_caption",
                    "company_name_of_work",
                    "address_of_work",
                    "identifier_name",
                    "identification_number"
                ])
            ]);
            return back()->withErrors($employment_document_type["error"]);
        } else {
            if ($employment_document_type["count"] > 0) {
                $employment->status_job_information_id = 4641401; // در انتظار بررسی
                $employment->save();
            }
            session(["request_job_information" => null]);
            return back()->with(["success" => "یک سابقه شغلی با موفقیت اضافه گردید."]);
        }


    }

    public function upload($key, UserJobInformation $user_job_information)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107,4640108])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        $document_receive_step_document_type_list = DocumentReceiveStepDocumentType::where("receive_document_step_id", 8)->get();
        return view($this->view_path . "upload", compact('employment', 'user_job_information', 'document_receive_step_document_type_list'));
    }

    public function submit_upload($key, Request $request, UserJobInformation $user_job_information)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107,4640108])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        $employment_document_type = EmploymentDocumentType::CreateEmploymentDocumentType($employment, $request, 8, $user_job_information->id);
        if (!$employment_document_type["result"]) {
            return back()->withErrors($employment_document_type["error"]);
        } else {

            if ($employment_document_type["count"] > 0 ) {
                $employment->status_job_information_id = 4641401; // در انتظار بررسی
                $employment->save();
            }
            $document_job_information = PersonalInfoController::viewUploadDocument($employment, 8);
            $session = session('upload_data');
            if ($document_job_information) {
                $user_job_informations = $employment->worker->user_job_informations()->get();
                foreach ($user_job_informations as $item) {
                    $session['upload_user_job_information'][8][$item->id] = true;
                }
            }
            session(['upload_data' => $session]);
            if($employment->status_id==4640107){
                return redirect()->route($this->next_rout_upload_confirm , $employment->key)->with(["success" => "سابقه شغلی با موفقیت بارگذاری شد."]);
            }
            return redirect()->route($this->route_path . 'index', [$employment->key, $user_job_information])->with(["success" => "سابقه شغلی با موفقیت ثبت شد."]);

        }
    }

    public function destroy($key, UserJobInformation $user_job_information)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108])-> // در حال تکمیل
        first();
        $employment_document_type = EmploymentDocumentType::where([
            'employment_id' => $employment->id,
            'receive_document_step_id' => 8,
            'other_id' => $user_job_information->id,

        ])->first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        if ($employment->user_id != $user_job_information->user_id) {
            return back()->withErrors("سابقه شغلی جهت حذف نامعتبر است.");
        }
        $employment->status_job_information_id = 4641401;//در انتظار تایید اطلاعات
        $employment->save();

        if ($employment_document_type) {
            $employment_document_type->delete();
            $employment_document_type->file->delete();
        }

        $user_job_information->delete();
        return back()->with(["success" => "یک سابقه شغلی با موفقیت حذف گردید."]);

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
