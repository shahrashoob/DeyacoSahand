<?php

namespace App\Http\Controllers\HR\Employment\Register;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Models\File\File;
use App\Models\HR\Education\Education;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentLog;
use App\Models\HR\Personal\AcademicDegree;
use App\Models\HR\Personal\AcademicDegreeType;
use App\Models\HR\Personal\Gender;
use App\Models\HR\Personal\MaritalStatus;
use App\Models\HR\Personal\Nationality;
use App\Models\HR\Personal\PersonalType;
use App\Models\HR\User\CooperationType;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\Post\Post;
use App\Models\User;
use App\Models\Utility\Address\Address;
use App\Models\Utility\Address\Country;
use App\Models\Utility\Address\Province;
use App\Models\HR\User\UserAddress;
use App\Models\Utility\Option;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Morilog\Jalali\Jalalian;

class OtherController extends Controller
{
    protected $view_path = "hr.employment.register.other.";
    protected $route_path = "hr.employment.register.other.";
    protected $next_route = "hr.employment.register.confirm_information.index";

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
        if (!$employment->worker->user_job_informations) {
            return back()->withErrors("لطفا ابتدا اطلاعات شغلی خود را تکمیل نمایید.");
        }
        if (!$employment->worker->user_educational_courses) {
            return back()->withErrors("لطفا ابتدا دوره های آموزشی خود را تکمیل نمایید.");
        }
        $request = session("request_other");
        if (!$request) {
            $request["description"] = null;

        }

        $before_route = "hr.employment.register.educational_course.index";
        if (in_array($employment->cooperation_type_id, [61, 6])) {
            $before_route = "hr.employment.register.address.index";

        }
        return view($this->view_path . "index", compact("employment", 'request', "before_route"));
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
        if (!$employment->worker->user_job_informations) {
            return back()->withErrors("لطفا ابتدا اطلاعات شغلی خود را تکمیل نمایید.");
        }
        if (!$employment->worker->user_educational_courses) {
            return back()->withErrors("لطفا ابتدا دوره های آموزشی خود را تکمیل نمایید.");
        }

        session(["request_other" => $request->all()]);

        $employment->description = $request->description;
        $employment->save();
        if ($request->description) {
            return redirect()->route($this->next_route, $employment->key)->with(["success" => "توضیحات شما با موفقیت ثبت گردید.<br/>لطفا اطلاعات خود بررسی کنید و در صورت عدم مغایرت، تایید نمایید."]);

        } else {
            return redirect()->route($this->next_route, $employment->key)->with(["success" => "لطفا اطلاعات خود بررسی کنید و در صورت عدم مغایرت، تایید نمایید."]);

        }

    }
}
