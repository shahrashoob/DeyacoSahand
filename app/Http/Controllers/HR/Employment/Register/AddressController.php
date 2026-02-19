<?php

namespace App\Http\Controllers\HR\Employment\Register;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Models\File\File;
use App\Models\HR\Education\Education;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
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

class AddressController extends Controller
{
    protected $view_path = "hr.employment.register.address.";
    protected $route_path = "hr.employment.register.address.";
    protected $next_route = "hr.employment.register.personal.academic_degree.index";
    protected $next_other_route = "hr.employment.register.other.index";
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
        //اطلاعات را سشن می ریزیم تا فرم پاک نشود.
        $request = session("request_address");
        $before_user_address = UserAddress::where('user_id', $employment->user_id)->first();
        if (!$request) {
            if ($before_user_address) {
                $request["province_id"] = $before_user_address->address->province_id;
                $request["city_name"] = $before_user_address->address->city_name;
                $request["phone"] = $before_user_address->address->phone;
                $request["mobile"] = $before_user_address->address->mobile;
                $request["postal_code"] = $before_user_address->address->postal_code;
                $request["address"] = $before_user_address->address->address;
                $request["country_id"] = $before_user_address->address->country_id;

            } else {
                $request["province_id"] = null;
                $request["city_name"] = null;
                $request["phone"] = null;
                $request["mobile"] = null;
                $request["postal_code"] = null;
                $request["address"] = null;
                $request["country_id"] = null;
            }
        }
        $post_document_receive_step_confirm = PostDocumentReceiveStepConfirm::where([
            'post_id' => $employment->post_id,
            "receive_document_step_id" => 2,
            'is_necessary_to_deliver_document_to_archive' => 1,
        ])->first();
        $document_receive_step_document_type_list = DocumentReceiveStepDocumentType::where("receive_document_step_id", 2)->get();
        $province_option = Option::get("province", $request["province_id"] ?? "");
        $country_option = Option::get("country", $request["country_id"] ?? $employment->country_id);

        return view($this->view_path . "index", compact('request', "employment", 'province_option','country_option',
            'document_receive_step_document_type_list', 'post_document_receive_step_confirm'));
    }

    public function submit(Request $request, $key)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108])-> // در حال تکمیل
        first();

        session([
            "request_address" => $request->only([
                "province_id",
                "city_name",
                "phone",
                "postal_code",
                "address",
                "country_id",
            ])
        ]);

        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        if (!$employment->user_id) {
            return back()->withErrors("لطفا ابتدا اطلاعات اولیه خود را تکمیل نمایید.");
        }

        $request->validate([
            "province_id" => ['required'],
            "city_name" => ['required'],
            "phone" => ['required'],
            "postal_code" => ['required'],
            "address" => ['required'],
            "country_id"=> ['required'],

        ]);

        $before_user_address = UserAddress::where('user_id', $employment->user_id)->first();
        $before_company_address = UserAddress::where('company_id', $employment->company_id)->first();
        $address_info = [
            "country_id" => $request->country_id,
            "province_id" => $request->province_id,
            "city_name" => $request->city_name,
            "phone" => $request->phone,
            "mobile" => $employment->mobile,
            "postal_code" => $request->postal_code,
            "address" => $request->address,
            "state_id" => 0,
            "city_id" => 0,

        ];
        if($employment->personal_type_id==1){
            if (!$before_user_address) {
                $address = Address::create($address_info);


                UserAddress::create([
                    'user_id' => $employment->user_id,
                    'address_id' => $address->id,
                    'is_default' => 1,
                ]);
            } else {
                $before_user_address->address->update($address_info);
            }
        }else{// در صورتی که حقوقی باشد باید ادرس شرکت ذخیره شود
            if (!$before_company_address) {
                $address = Address::create($address_info);

                UserAddress::create([
                    'company_id' => $employment->company_id,
                    'address_id' => $address->id,
                    'is_default' => 1,
                ]);
            } else {
                $before_company_address->address->update($address_info);
            }
        }


        if ($employment->cooperation_type_id== 3 && $employment->personal_type_id==2) {
            $employment->status_address_id = 4641401; // در انتظار بررسی
            $employment->save();
            return redirect()->route('hr.employment.register.customer.agent.index', $employment->key)->with(["success" => " اطلاعات آدرس با موفقیت ثبت گردید."]);

        }
        if ($employment->cooperation_type_id== 6 && $employment->personal_type_id==2) {
            $employment->status_address_id = 4641401; // در انتظار بررسی
            $employment->save();
            return redirect()->route('hr.employment.register.supplier.agent.index', $employment->key)->with(["success" => " اطلاعات آدرس با موفقیت ثبت گردید."]);

        }
        if ($employment->cooperation_type_id== 2 && $employment->personal_type_id==2) {
            $employment->status_address_id = 4641401; // در انتظار بررسی
            $employment->save();
            return redirect()->route('hr.employment.register.contractor.agent.index', $employment->key)->with(["success" => " اطلاعات آدرس با موفقیت ثبت گردید."]);

        }
        if (in_array($employment->cooperation_type_id, [6, 61, 3,31,2,21]) && $employment->personal_type_id==1 ) {

            $employment->status_address_id = 4641401; // در انتظار بررسی
            $employment->save();
            return redirect()->route($this->next_other_route, $employment->key)->with(["success" => " اطلاعات آدرس با موفقیت ثبت گردید."]);

        }

        $employment_document_type = EmploymentDocumentType::CreateEmploymentDocumentType($employment, $request, 2, null);

        if (!$employment_document_type["result"]) {
            return back()->withErrors($employment_document_type["error"]);
        } else {

            if ($employment_document_type["count"] > 0) {
                $employment->status_address_id = 4641401; // در انتظار بررسی
                $employment->save();
            }
            session(["request_address" => null]);
            return redirect()->route($this->next_route, $employment->key)->with(["success" => "اطلاعات آدرس با موفقیت ثبت گردید."]);
        }

    }

    public function upload($key)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107])->
        first();

        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $document_address = PersonalInfoController::viewUploadDocument($employment, 2);

        return view($this->view_path . "upload", compact(
            "employment", 'document_address',
        ));
    }

    public function submit_upload(Request $request, $key)
    {


        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }


        $employment_document_type = EmploymentDocumentType::CreateEmploymentDocumentType($employment, $request, 2, null);

        if (!$employment_document_type["result"]) {
            return back()->withErrors($employment_document_type["error"]);
        } else {
            $employment->status_address_id = 4641401; // در انتظار بررسی
            $employment->save();
            $session = session('upload_data');
            $document_address = PersonalInfoController::viewUploadDocument($employment, 2);
            foreach ($document_address as $item) {
                $session['upload_document_address'][2][$item->document_type_id] = true;
            }
            session(['upload_data' => $session]);
            if ($employment->status_id == 4640107) {
                return redirect()->route($this->next_rout_upload_confirm, $employment->key)->with(["success" => "مدارک آدرس با موفقیت بارگزاری شد."]);
            }
            return redirect()->route($this->next_route, $employment->key)->with(["success" => "مدارک آدرس با موفقیت بارگزاری شد."]);
        }

    }

}
