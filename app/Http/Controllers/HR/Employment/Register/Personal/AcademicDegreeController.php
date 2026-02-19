<?php

namespace App\Http\Controllers\HR\Employment\Register\Personal;


use App\Http\Controllers\Controller;


use App\Models\HR\Employment\Employment;

use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\Personal\AcademicDegreeType;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\Post\PostDocumentReceiveStepConfirm;

use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


class AcademicDegreeController extends Controller
{
    protected $view_path = "hr.employment.register.personal.academic_degree.";
    protected $route_path = "hr.employment.register.personal.academic_degree.";
    protected $next_route = "hr.employment.register.personal.job_information.index";
    protected $next_rout_upload_confirm = "hr.employment.register.personal.confirm_upload_document.index";


    public function __construct()
    {
        $this->middleware('guest');
    }


    public function index($key, $academic_degree_type_id = 0)
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
        if ($employment->worker->user_address()->count() == 0) {
            return back()->withErrors("لطفا ابتدا اطلاعات آدرس خود را تکمیل نمایید.");
        }
        //اطلاعات را سشن می ریزیم تا فرم پاک نشود.
        $request = session("request_academic_degree");
        if (!$request) {
            $request["academic_degree_type_id"] = null;
            $request["feild_of_academic_degree_id"] = null;
            $request["name_of_academic_degree"] = null;
            $request["start_date"] = null;
            $request["end_date"] = null;
            $request["average"] = null;
        }

        //افزودن فیلد رشته و مقطع تحصیلی
        $academic_degree_type_option = Option::get("academic_degree_type", $academic_degree_type_id);
        $feild_of_academic_degree_option = Option::get("feild_of_academic_degree", 0, $academic_degree_type_id);


        $user_academic_degrees = UserAcademicDegree::where('user_id', $employment->user_id)->get();
        if ($employment->status_academic_degree_id != 4641402) {
            $allow_delete = true;
        } else {
            $allow_delete = false;
        }
        $allow_show_upload = true;

        $post_document_receive_step_confirm = PostDocumentReceiveStepConfirm::where([
            'post_id' => $employment->post_id,
            'is_necessary_to_deliver_document_to_archive' => 1,
        ])->
        whereIn("receive_document_step_id", [3, 4, 5, 6, 7])->
        first();

        $document_receive_step_document_type_list = DocumentReceiveStepDocumentType::where("receive_document_step_id", 3)->get();


        $allow_user_academic_degree_upload = UserAcademicDegree::join('academic_degree_types', 'academic_degree_types.id', 'user_academic_degrees.academic_degree_type_id')
            ->join('post_document_receive_step_confirms', 'post_document_receive_step_confirms.receive_document_step_id', 'academic_degree_types.receive_document_step_id')
            ->where('user_academic_degrees.user_id', $employment->user_id)
            ->where('post_document_receive_step_confirms.post_id', $employment->post_id)
            ->where('post_document_receive_step_confirms.confirm_type', 2)
            ->select('user_academic_degrees.*', 'academic_degree_types.*', 'post_document_receive_step_confirms.*', 'user_academic_degrees.id as user_academic_degree_id')
            ->get();
//        if ($allow_user_academic_degree_upload) {
//            foreach ($allow_user_academic_degree_upload as $allow_user_academic_degree) {
//                $employment_document_type = EmploymentDocumentType::where([
//                    'receive_document_step_id' => $allow_user_academic_degree->receive_document_step_id,
//                    'other_id' => $allow_user_academic_degree->user_academic_degree_id,
//                ])->first();
//            }
//        }
        $employment_document_types = [];
        if ($allow_user_academic_degree_upload->count() > 0) {
            foreach ($allow_user_academic_degree_upload as $allow_user_academic_degree) {
                $document_type = EmploymentDocumentType::where([
                    'receive_document_step_id' => $allow_user_academic_degree->receive_document_step_id,
                    'other_id' => $allow_user_academic_degree->user_academic_degree_id,
                ])->first();

                if ($document_type) {
                    $employment_document_types[$allow_user_academic_degree->user_academic_degree_id] = $document_type;
                }
            }
        }

        return view($this->view_path . "index", compact("employment", 'request', 'allow_delete', 'allow_show_upload',
            'academic_degree_type_option', 'employment_document_types', 'allow_user_academic_degree_upload', 'user_academic_degrees', 'feild_of_academic_degree_option', 'document_receive_step_document_type_list', 'post_document_receive_step_confirm'));
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
        if ($employment->worker->user_address()->count() == 0) {
            return back()->withErrors("لطفا ابتدا اطلاعات آدرس خود را تکمیل نمایید.");
        }
        if ($employment->worker->user_academic_degrees->count() >= 10) {
            return back()->withErrors(" ثبت اطلاعات تحصیلی بیش از  10 مورد امکان پذیر نمی باشد. ");
        }

        $request->validate([
            "academic_degree_type_id" => ['required'],
            "feild_of_academic_degree_id" => ['required'],
            "name_of_academic_degree" => ['required'],
            "start_date" => ['required'],
            "end_date" => ['required'],
            "average" => ['required'],
        ]);

        if ($request->average < 0 || $request->average > 20) {
            return back()->withErrors("معدل باید عددی بین 0 تا 20 باشد.");
        }


        $user_academic_degree = UserAcademicDegree::create([
            'user_id' => $employment->user_id,
            "academic_degree_type_id" => $request->academic_degree_type_id,
            "feild_of_academic_degree_id" => $request->feild_of_academic_degree_id,
            "name_of_academic_degree" => $request->name_of_academic_degree,
            "average" => $request->average,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
        ]);


        $step_id = $user_academic_degree->academic_degree_type->receive_document_step_id;
        $employment_document_type = EmploymentDocumentType::CreateEmploymentDocumentType($employment, $request, $step_id, $user_academic_degree->id);

        if (!$employment_document_type["result"]) {
            $user_academic_degree->delete();
            session([
                "request_academic_degree" => $request->only([
                    "academic_degree_type_id",
                    "feild_of_academic_degree_id",
                    "name_of_academic_degree",
                    "start_date",
                    "end_date",
                    "average"
                ])
            ]);
            return back()->withErrors($employment_document_type["error"]);
        } else {
            if ($employment_document_type["count"] > 0) {
                $employment->status_academic_degree_id = 4641401; // در انتظار بررسی
                $employment->save();
            }
            session(["request_academic_degree" => null]);
            return back()->with(["success" => "یک دوره تحصیلی ما موفقیت اضافه گردید."]);
        }

    }

    public function upload($key, UserAcademicDegree $user_academic_degree)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107, 4640108])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        $document_receive_step_document_type_list = DocumentReceiveStepDocumentType::where("receive_document_step_id", 3)->get();
        return view($this->view_path . "upload", compact('employment', "user_academic_degree", 'document_receive_step_document_type_list'));
    }

    public function submit_upload($key, Request $request, UserAcademicDegree $user_academic_degree)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107, 4640108])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $step_id = $user_academic_degree->academic_degree_type->receive_document_step_id;
        $employment_document_type = EmploymentDocumentType::CreateEmploymentDocumentType($employment, $request, $step_id, $user_academic_degree->id);

        if (!$employment_document_type["result"]) {
            return back()->withErrors($employment_document_type["error"]);
        } else {
            if ($employment_document_type["count"] > 0) {
                $employment->status_academic_degree_id = 4641401; // در انتظار بررسی
                $employment->save();
            }

            $academic_degree_type_ids = [];

            $academic_degree_type_ids[] = AcademicDegreeType::where('receive_document_step_id', 3)->pluck('id');
            $academic_degree_type_ids[] = AcademicDegreeType::where('receive_document_step_id', 4)->pluck('id');
            $academic_degree_type_ids[] = AcademicDegreeType::where('receive_document_step_id', 5)->pluck('id');
            $academic_degree_type_ids[] = AcademicDegreeType::where('receive_document_step_id', 6)->pluck('id');
            $academic_degree_type_ids[] = AcademicDegreeType::where('receive_document_step_id', 7)->pluck('id');

            $user_academic_degrees = $employment->worker->user_academic_degrees()->whereIn('academic_degree_type_id', collect($academic_degree_type_ids)->flatten()->toArray())->get();
            $session = session('upload_data');
            foreach ($user_academic_degrees as $item) {
                $session['upload_document_academic_degree'][$item->id] = true;
            }

            session(['upload_data' => $session]);
            if ($employment->status_id == 4640107) { //در انتظار بارگذاری مدارک (مرحله دوم)
                return redirect()->route($this->route_path . 'index', [$employment->key, $user_academic_degree])->with(["success" => "یک مدرک تحصیلی با موفقیت ثبت شد."]);
            }
            return redirect()->route($this->route_path . 'index', [$employment->key, $user_academic_degree])->with(["success" => "مدرک تحصیلی با موفقیت ثبت شد."]);

        }
    }

    public function destroy($key, UserAcademicDegree $user_academic_degree)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108])-> // در حال تکمیل
        first();
        $step_id = $user_academic_degree->academic_degree_type->receive_document_step_id;
        $employment_document_type = EmploymentDocumentType::where([
            'employment_id' => $employment->id,
            'receive_document_step_id' => $step_id,
            'other_id' => $user_academic_degree->id

        ])->first();

        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        if ($employment->user_id != $user_academic_degree->user_id) {
            return back()->withErrors("اطلاعات تحضیلی جهت حذف نامعتبر است.");
        }
        $user_academic_degree->delete();
        if ($employment_document_type) {
            $employment_document_type->delete();
            $employment_document_type->file->delete();
        }
        return back()->with(["success" => "یک دوره تحصیلی با موفقیت حذف گردید."]);

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
