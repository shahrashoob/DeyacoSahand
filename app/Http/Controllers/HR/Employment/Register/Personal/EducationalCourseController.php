<?php

namespace App\Http\Controllers\HR\Employment\Register\Personal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Register\PersonalInfoController;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\User\UserEducationalCourse;
use App\Models\HR\User\UserJobInformation;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class EducationalCourseController extends Controller
{
    protected $view_path = "hr.employment.register.personal.educational_course.";
    protected $route_path = "hr.employment.register.personal.educational_course.";
    protected $next_route = "hr.employment.register.other.index";
    protected $next_rout_upload_confirm = "hr.employment.register.personal.confirm_upload_document.index";

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index($key)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301,4640107,4640108])-> // در حال تکمیل
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
        if (!$employment->worker->user_job_informations) {
            return back()->withErrors("لطفا ابتدا اطلاعات شغلی خود را تکمیل نمایید.");
        }
        $request = session("request_educational_courses");
        if (!$request) {
            $request["course_name"] = null;
            $request["start_date"] = null;
            $request["name_of_institution"] = null;
            $request["end_date"] = null;
            $request["duration"] = null;

        }
        if($employment->status_educational_course_id!=4641402){
            $allow_delete = true;
        }else{
            $allow_delete = false;
        }
        $allow_show_upload=true;


        $user_educational_courses = UserEducationalCourse::where('user_id', $employment->user_id)->get();
        $document_receive_step_document_type_list = DocumentReceiveStepDocumentType::where( "receive_document_step_id",9)->get();

        $post_document_receive_step_confirm=PostDocumentReceiveStepConfirm::where([
            'post_id'=>$employment->post_id,
            "receive_document_step_id" => 9,
            'is_necessary_to_deliver_document_to_archive'=>1,
        ])->first();
        return view($this->view_path . "index", compact("employment",'post_document_receive_step_confirm','request','allow_show_upload','user_educational_courses','allow_delete','document_receive_step_document_type_list'));
    }

    public function submit(Request $request, $key)
    {

        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301,4640107,4640108])-> // در حال تکمیل
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
        if (!$employment->worker->user_job_informations) {
            return back()->withErrors("لطفا ابتدا اطلاعات شغلی خود را تکمیل نمایید.");
        }
        if ($employment->worker->user_educational_courses->count() >= 15) {
            return back()->withErrors(" ثبت دوره تحصیلی بیش از  15 مورد امکان پذیر نمی باشد. ");
        }




        $request->validate([
            "course_name" => ['required'],
            "name_of_institution" => ['required'],
            "start_date" => ['required'],
            "end_date" => ['required'],
            "duration" => ['required'],
        ]);


        $user_educational_course=UserEducationalCourse::create([
            'user_id' => $employment->user_id,
            'course_name'=> $request->course_name,
            'name_of_institution'=> $request->name_of_institution,
            'start_date'=> $request->start_date,
            'end_date'=> $request->end_date,
            'duration'=> $request->duration,

        ]);

        $employment_document_type = EmploymentDocumentType::CreateEmploymentDocumentType($employment, $request,9,$user_educational_course->id);

        if (!$employment_document_type["result"]) {
            $user_educational_course->delete();
            session([
                "request_educational_courses" => $request->only([
                    "course_name",
                    "name_of_institution",
                    "start_date",
                    "end_date",
                    "duration"
                ])
            ]);
            return back()->withErrors($employment_document_type["error"]);
        } else {
            if( $employment_document_type["count"]>0) {
                $employment->status_educational_course_id = 4641401; // در انتظار بررسی
                $employment->save();
            }
            session()->forget("request_educational_courses");
            return back()->with(["success" => "یک دوره آموزشی جدید با موفقیت ثبت گردید."]);
        }
    }


    public function upload($key, UserEducationalCourse  $user_educational_course)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107,4640108])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        $document_receive_step_document_type_list = DocumentReceiveStepDocumentType::where("receive_document_step_id", 9)->get();
        return view($this->view_path . "upload", compact('employment','user_educational_course','document_receive_step_document_type_list'));
    }

    public function submit_upload($key, Request $request,  UserEducationalCourse  $user_educational_course)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107,4640108])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        $employment_document_type = EmploymentDocumentType::CreateEmploymentDocumentType($employment, $request,9, $user_educational_course->id);
        if (!$employment_document_type["result"]) {
            return back()->withErrors($employment_document_type["error"]);
        } else {
            if ($employment_document_type["count"] > 0) {
                $employment->status_educational_course_id = 4641401; // در انتظار بررسی
                $employment->save();
            }
            $document_educational_course = PersonalInfoController::viewUploadDocument($employment, 9);
            $session = session('upload_data');
            if ($document_educational_course) {
                 $user_educational_courses = $employment->worker->user_educational_courses()->get();
                foreach ($user_educational_courses as $item) {
                    $session['upload_user_educational_courses'][9][$item->id] = true;
                }
            }
            session(['upload_data' => $session]);
            if($employment->status_id==4640107){
                return redirect()->route($this->next_rout_upload_confirm , $employment->key)->with(["success" => "گواهینامه آموزشی با موفقیت بارگذاری شد."]);
            }
            return redirect()->route($this->route_path.'index', [$employment->key, $user_educational_course])->with(["success" => "گواهینامه آموزشی با موفقیت ثبت شد."]);

        }
    }
    public function destroy($key,UserEducationalCourse  $user_educational_course)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301,4640107,4640108])-> // در حال تکمیل
        first();
        $employment_document_type = EmploymentDocumentType::where([
            'employment_id'=> $employment->id,
            'receive_document_step_id'=>9,
            'other_id'=>$user_educational_course->id

        ])->first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        if ($employment->user_id !=  $user_educational_course->user_id) {
            return back()->withErrors("دوره آموزشی جهت حذف نامعتبر است.");
        }

        if ($employment_document_type) {
            $employment_document_type->delete();
            $employment_document_type->file->delete();
        }
        $user_educational_course->delete();
        return back()->with(["success" => "یک دوره آموزشی با موفقیت حذف گردید."]);

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
