<?php

namespace App\Http\Controllers\HR\Employment\Register;


use App\Http\Controllers\Controller;
use App\Models\HR\Company\Company;
use App\Models\File\File;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\Personal\AcademicDegree;
use App\Models\HR\Personal\AcademicDegreeType;
use App\Models\HR\Personal\MaritalStatus;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\HR\User\UserDependent;
use App\Models\HR\User\UserEducationalCourse;
use App\Models\HR\User\UserJobInformation;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Utility\Address\Address;
use App\Models\HR\User\UserAddress;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class PersonalInfoController extends Controller
{
    protected $view_path = "hr.employment.register.personal_info.";
    protected $route_path = "hr.employment.register.personal_info.";
    protected $next_route = "hr.employment.register.address.index";
    protected $next_rout_upload = "hr.employment.register.address.upload";
    protected $next_rout_upload_confirm = "hr.employment.register.personal.confirm_upload_document.index";
    protected $key_email = "**deyaco**";

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

        $empty_post_option = Option::get("empty_posts", $employment->post_id, $employment->cooperation_type_id ?? 0);
        $gender_option = Option::get("gender", $employment->worker->gender_id ?? 0);
        $marital_status_option = Option::get("marital_status", $employment->worker->marital_status_id ?? 0);
        $get_the_supplier_image = Setting::getIntegerValue("get_the_supplier_image");
        $get_the_customer_image = Setting::getIntegerValue("get_the_customer_image");
        $get_the_contractor_image = Setting::getIntegerValue("get_the_contractor_image");
        $document_receive_step_document_type_list = DocumentReceiveStepDocumentType::where("receive_document_step_id", 1)->get();
        $post_document_receive_step_confirm = PostDocumentReceiveStepConfirm::where([
            'post_id' => $employment->post_id,
            "receive_document_step_id" => 1,
            'is_necessary_to_deliver_document_to_archive' => 1,
        ])->first();
        $email = $employment->worker->email ?? "";
        if (Str::length($email) > 50) {
            $array = explode($this->key_email, $email);
            $email = $array[0];
        }


        return view($this->view_path . "index", compact(
            "employment", "empty_post_option", "gender_option",
            "marital_status_option", "email", "document_receive_step_document_type_list", 'get_the_contractor_image', 'post_document_receive_step_confirm', 'get_the_supplier_image', "get_the_customer_image"
        ));
    }


    public function submit(Request $request, $key)
    {


        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301, 4640107, 4640108])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }


        $worker_info = [
            "cooperation_type_id" => 0,
            "personal_type_id" => $employment->personal_type_id,
            "nationality_id" => $employment->nationality_id,
            "national_code" => ($employment->personal_type_id == 1) ? $employment->national_code : $request->national_code,
            'email' => $request->email ?? "",
            'post_id' => $request->post_id ?? "",
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'father_name' => $request->father_name,
            'birth_certificate_number' => $request->birth_certificate_number ?? null,
            'gender_id' => $request->gender_id??1,
            'marital_status_id' => $request->marital_status_id,
            'place_of_birth' => $request->place_of_birth,
            'date_of_birth' => $request->date_of_birth,
            'date_of_readiness_to_start_work' => $request->date_of_readiness_to_start_work,
            'insurance_number' => $request->insurance_number,
            'mobile' => $employment->mobile,
            'country_id' => $employment->country_id,
            'mobile_country_id' => $employment->mobile_country_id,

        ];

        $company_info = [
            "caption" => $request->caption,
            "register_code" => $request->register_code,
            "national_code" => $employment->national_code,
            'user_id' => $employment->user_id
        ];

        $exists_email = Worker::
        where('email', $request->email)->
        where('id', "!=", $employment->user_id ?? 0)->
        exists();
        if ($exists_email) {
            // اگر نام کاربری تکراری بود، یک عدد رندوم بزرگ قرار می دهیم. و در مقداری که کاربر وارد کرده است را در سشن قرار می دهیم.
            $worker_info["email"] = $request->email . $this->key_email . $employment->id . Str::random(50);
            return back()->withErrors("نام کاربری تکراری می باشد.");
        }
        $check_user_name = Worker::CheckUserName($request->email);
        if (!   $check_user_name["result"]) {
            return back()->withErrors($check_user_name["error"]);
        }
//در اینجا فیلد هایی ک مورد نیاز است را در سمت بک اند چک می کند
        $request->validate(
            $this->validate_personal($employment->personal_type_id, $employment->cooperation_type_id)
        );


        $user_image = null;
        $result_file = self::checkFileUploded($request, "user_image_file_id", ["png", 'jpg', 'jpeg']);
        if (!$result_file["result"]) {
            return back()->withErrors($result_file["error"]);
        }

        if ($employment->personal_type_id == 2) {//در صورتی که حقوقی باشد باید حتما چک شود که کد ملی مدیر عامل در جدول worker تکراری نباشد
            if (!$request->national_code) {
                return back()->withErrors("کد ملی مدیر عامل الزامی می باشد.");
            }
            $exists_national_code = Worker::where('national_code', $request->national_code)->
            where('id', "!=", $employment->user_id ?? 0)->
            exists();
            if ($exists_national_code) {
                return back()->withErrors("کد ملی مدیر عامل تکراری می باشد.");
            }
        }
        $get_the_supplier_image = Setting::getIntegerValue("get_the_supplier_image");
        $get_the_customer_image = Setting::getIntegerValue("get_the_customer_image");
        $get_the_contractor_image = Setting::getIntegerValue("get_the_contractor_image");
        if ((in_array($employment->cooperation_type_id, [6, 61]) && $get_the_supplier_image) ||
            (in_array($employment->cooperation_type_id, [3, 31])) && $get_the_customer_image ||
            (in_array($employment->cooperation_type_id, [2, 21]) && $get_the_contractor_image)) {
            if (!$request->file('user_image_file_id') && !isset($employment->worker->image)) {
                return back()->withErrors('لطفا تصویر پرسنلی/ لوگو شرکت را بارگزاری نمایید.');
            }
        }


//اگر کاربر حقوقی باشد و یا حقیقی باید جدول userایجاد شود که در حقوقی ها همان مدیر عامل است.
        if ($employment->worker) {
            $employment->worker->update($worker_info);
        } else {
            //ذخیره اطلاعات در جدول کاربر
            $worker = Worker::create($worker_info);
            $employment->user_id = $worker->id;
            $employment->save();
        }


        //در صورتی که حقوقی باشد باید اطلاعات شرکت نیز ثبت شود
        if ($employment->personal_type_id == 2) {
            if ($employment->company) {
                $employment->company->update($company_info);
                $employment->company->user_id = $employment->user_id;
                $employment->company->save();
            } else {
                //ذخیره اطلاعات در جدول شرکت ها
                $company = Company::create($company_info);
                $company->user_id = $employment->user_id;
                $company->save();
                $employment->company_id = $company->id;


            }
        }

        $employment->date_of_readiness_to_start_work = $request->date_of_readiness_to_start_work;
        $employment->post_id = $request->post_id ?? null;
        $employment->save();

// عکس پرسنلی با توجه به تنظیمات گرفته میش ود ولی برای کامند پاره وقت و تمام وقت الزامی  می باشد که در بالا چک شده است.
        if ($request->file('user_image_file_id')) {
            $user_image = File::uploadFile($request->file('user_image_file_id'), $employment->user_id . "_" . Str::random(4) . '.' . File::get_file_extension($request->file('user_image_file_id')->getClientOriginalName()), 20, 'chatify/users-avatar', true);
            $employment = Employment::find($employment->id);
            $employment->worker->image_id = $user_image->id;
            $employment->worker->save();
        }

        // تنها همکاری های مشتری، تامینن کننده,پیمانکار نماینده دارد که
        // این نماینده هم برای حقوقی و هم حقیقی ایجاد می شود.
        Employment::CreateAgent($employment);

        // در صورتی که این تایپ هارا داشته باشد تایید اطلاعات می شود
        if (in_array($employment->cooperation_type_id, [6, 61, 3, 31, 2, 21])) {
            $employment->status_personal_id = 4641401; // در انتظار بررسی
            $employment->save();
            return redirect()->route($this->next_route, $employment->key)->with(["success" => "اطلاعات فردی با موفقیت ثبت گردید."]);

        }
// در صورتی که کارمند تمام قت باشد باتوجه به بارگزاری مدارد اطلاعات اولیه در انتظار تایید می شود.
        if (in_array($employment->cooperation_type_id, [1, 11])) {
            $employment_document_type = EmploymentDocumentType::CreateEmploymentDocumentType($employment, $request, 1, null);

            if (!$employment_document_type["result"]) {
                return back()->withErrors($employment_document_type["error"]);
            } else {
                if ($employment_document_type["count"] > 0) {
                    $employment->status_personal_id = 4641401; // در انتظار بررسی
                    $employment->save();
                }
                return redirect()->route($this->next_route, $employment->key)->with(["success" => "اطلاعات فردی با موفقیت ثبت گردید."]);
            }
        }

    }

//این تابع برای مرحله بارگزاری مدارک می باشد.
    public function upload($key)
    {
        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640107])->
        first();

        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        //در این تابع با توجه به اینکه اطلاعات فردی مرحله اول مب شاد ویو ان را نمایش میدهد.
        $document_personal = self::viewUploadDocument($employment, 1);


        return view($this->view_path . "upload", compact(
            "employment", 'document_personal',
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


        $employment_document_type = EmploymentDocumentType::CreateEmploymentDocumentType($employment, $request, 1, null);
//و زمانی که مدرک را بارگزاری کرد در انتظار تایید مشخصات مرحله دوم قرار می گیرد.
        if (!$employment_document_type["result"]) {
            return back()->withErrors($employment_document_type["error"]);
        } else {
            $employment->status_personal_id = 4641401; // در انتظار بررسی
            $employment->save();
            // اپلود را در صدا میزنبم و این یکبار در صفحه اول بارگزار یمدارک ایجاد می شود
            $session = session('upload_data');
            $document_personal = self::viewUploadDocument($employment, 1);
            foreach ($document_personal as $item) {
                $session['upload_document_personal'][1][$item->document_type_id] = true;
            }
            session(['upload_data' => $session]);
            if ($employment->status_id == 4640107) {
                return redirect()->route($this->next_rout_upload_confirm, $employment->key)->with(["success" => "مدارک شخصی با موفقیت بارگزاری شد."]);
            }

            return redirect()->route($this->next_rout_upload, $employment->key)->with(["success" => "مدارک شخصی با موفقیت ثبت شد."]);

        }

    }


    public static function viewUploadDocument($employment, $step_id)
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


    public static function ConfirmUploadDocument($employment)
    {
        $upload_document_personal = [];
        $upload_document_address = [];
        $upload_document_academic_degree = [];
        $upload_user_job_information = [];
        $upload_user_educational_courses = [];
        $upload_user_dependents = [];
        $document_personal = self::viewUploadDocument($employment, 1);
        $document_address = self::viewUploadDocument($employment, 2);
        $document_job_information = self::viewUploadDocument($employment, 8);
        $document_educational_course = self::viewUploadDocument($employment, 9);
        $document_dependent = self::viewUploadDocument($employment, 12);
        foreach ($document_personal as $item) {
            $upload_document_personal[1][$item->document_type_id] = false;
        }
        foreach ($document_address as $item) {
            $upload_document_address[2][$item->document_type_id] = false;
        }
        $academic_degree_type_ids = [];

        $academic_degree_type_ids[] = AcademicDegreeType::where('receive_document_step_id', 3)->pluck('id');
        $academic_degree_type_ids[] = AcademicDegreeType::where('receive_document_step_id', 4)->pluck('id');
        $academic_degree_type_ids[] = AcademicDegreeType::where('receive_document_step_id', 5)->pluck('id');
        $academic_degree_type_ids[] = AcademicDegreeType::where('receive_document_step_id', 6)->pluck('id');
        $academic_degree_type_ids[] = AcademicDegreeType::where('receive_document_step_id', 7)->pluck('id');

        $user_academic_degrees = $employment->worker->user_academic_degrees()->whereIn('academic_degree_type_id', collect($academic_degree_type_ids)->flatten()->toArray())->get();
        foreach ($user_academic_degrees as $item) {
            $upload_document_academic_degree[$item->id] = false;
        }
        if ($document_job_information) {
            $user_job_informations = $employment->worker->user_job_informations()->get();
            foreach ($user_job_informations as $item) {
                $upload_user_job_information[8][$item->id] = false;
            }
        }

        if ($document_educational_course) {
            $user_educational_courses = $employment->worker->user_educational_courses()->get();
            foreach ($user_educational_courses as $item) {
                $upload_user_educational_courses[9][$item->id] = false;
            }
        }
        if ($document_dependent) {
            $user_dependents = $employment->worker->user_dependents()->get();
            foreach ($user_dependents as $item) {
                $upload_user_dependents[12][$item->id] = false;
            }
        }

        $session = [
            'upload_document_personal' => $upload_document_personal,
            'upload_document_address' => $upload_document_address,
            'upload_document_academic_degree' => $upload_document_academic_degree,
            'upload_user_job_information' => $upload_user_job_information,
            'upload_user_educational_courses' => $upload_user_educational_courses,
            'upload_user_dependents' => $upload_user_dependents
        ];

        session(['upload_data' => $session]);


    }

//تابع ولیدت اطلاعات
    public function validate_personal($personal_type_id, $cooperation_type_id)
    {
        switch ($personal_type_id) {
            case 1:
                switch ($cooperation_type_id) {
                    case 1:
                    case 11:
                        return [
                            'email' => 'required',
                            'post_id' => 'required',
                            'firstname' => 'required',
                            'lastname' => 'required',
                            'father_name' => 'required',
                            'birth_certificate_number' => 'required',
                            'gender_id' => 'required',
                            'marital_status_id' => 'required',
                            'place_of_birth' => 'required',
                            'date_of_birth' => 'required',
                            'date_of_readiness_to_start_work' => 'required',

                        ];
                        break;
                    case 2:
                        return [
                            'firstname' => 'required',
                            'lastname' => 'required',
                            'gender_id' => 'required',
                            'date_of_birth' => 'required',
                        ];
                        break;
                    case 3:
                        return [
                            'firstname' => 'required',
                            'lastname' => 'required',
                            'gender_id' => 'required',
                            'date_of_birth' => 'required',

                        ];
                        break;
                    default:
                        return [];
                        break;
                }
            case 2:
                switch ($cooperation_type_id) {
                    case 1:
                        return [];
                        break;
                    case 2:
                        return [];
                        break;
                    case 3:
                        return [];
                        break;
                    default:
                        return [];
                        break;

                }
            default:
                return [];
                break;

        }
    }


    public static function checkFileUploded(
        Request $request,
                $file_name,
                $format_list = [
                    "pdf",
                    "xls",
                    "xlsx",
                    "doc",
                    "docx",
                    "png",
                    "jpg",
                    "jpeg",
                    "zip",
                    "txt"
                ])
    {
        $size_byte = 0;
        $max_size_mb = Setting::getIntegerValue("office_automation_max_file_size_in_mb");

        $file = $request->file($file_name);
        if (!$file) {
            return ["result" => true, 'warning' => "فایل انتخاب نشده است"];
        }
        $size_byte += $file->getSize();
        if (!in_array(File::get_file_extension($file->getClientOriginalName()), $format_list)) {
            return ["result" => false, "error" => "فرمت فایل بارگذاری شده قابل قبول نیست"];
        }

        if ($size_byte / 1024 / 1024 > $max_size_mb) {

            return [
                "result" => false,
                "error" => "حداکثر اندازه فایل جهت بارگذاری " . $max_size_mb . " مگابایت می باشد."
            ];

        }


        return ["result" => true];
    }


//این تابع برای زمانی است که کدملی یا شناسه ملی در ای سی وجود داشته باشد.
    public function exist_national_code($key)
    {

        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301])-> // در حال تکمیل
        first();

        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        $empty_post_option = [];
        $empty_post_option = Option::get("empty_posts", 0, $employment->cooperation_type_id ?? 0);
        $get_the_supplier_image = Setting::getIntegerValue("get_the_supplier_image");
        $get_the_customer_image = Setting::getIntegerValue("get_the_customer_image");
        $get_the_contractor_image = Setting::getIntegerValue("get_the_contractor_image");
//        if (count($empty_post_option["items"]) == 1) {
//            return redirect()->route($this->route_path . "index".$employment->key)->withErrors("هیچ پست سازمانی جهت همکاری برای پیشنهاد وجود ندارد.");
//        }

        $worker = Worker::
        where("national_code", $employment->national_code)->
        first();


        return view($this->view_path . "exist_national_code", compact("employment", "empty_post_option", 'get_the_supplier_image'
            , 'get_the_customer_image', 'get_the_contractor_image', "worker"));
    }


    public function submit_exist_national_code(Request $request, $key)
    {

        $employment = Employment::where("key", $key)->
        whereIn("employments.status_id", [4640301])-> // در حال تکمیل
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        //ابتدا نام کاربری و تصویر با لوگو خود را وارد می کند
        $exists_email = Worker::
        where('email', $request->email)->
        where('id', "!=", $employment->user_id ?? 0)->
        exists();
        if (strpos($request->email, ' ') !== false) {
            return back()->withErrors("نام کاربری نمی‌تواند شامل فاصله باشد.");
        }

        if ($exists_email) {
            return back()->withErrors("نام کاربری تکراری می باشد.");
        }
        if (in_array($employment->cooperation_type_id, [1, 11]) && isset($employment->user_id) && !isset($employment->worker->image)) {
            return back()->withErrors("لطفا تصویر پرسنلی را بارگذاری نمایید.");
        }
        $user_image = null;
        $result_file = self::checkFileUploded($request, "user_image_file_id", ["png", 'jpg', 'jpeg']);
        if (!$result_file["result"]) {
            return back()->withErrors($result_file["error"]);
        }
        //صدا زدن ای پی ای سی
        $result_check_national_code = Employment::CheckNationalCode($employment, env("IC_APIKEY"));

        $worker_before = Worker::
        where("national_code", $employment->national_code)->
        first();
        if ($worker_before) {
            $employment->user_id = $worker_before->id;
            $employment->save();
        }
        $exsit_user = Worker::where('id', $employment->user_id)->exists();
        if (!$exsit_user) {
//باید اطلاعات اولیه و ادرس تایید شده باشند چرا که در ای سی وجود دارند
            $employment->status_personal_id = 4641402;
            if ($result_check_national_code['address']) {
                $employment->status_address_id = 4641402;
            } else {
                $employment->status_address_id = 4641404;
            }
            if (isset($result_check_national_code['academic_degree']) && $result_check_national_code['academic_degree']) {
                $employment->status_academic_degree_id = 4641402;
            } else {
                $employment->status_academic_degree_id = 4641404;
            }
            $employment->status_job_information_id = 4641402;
            $employment->status_educational_course_id = 4641402;
            $employment->status_dependent_id = 4641402;

            $result_check_national_code['personal']["email"]=$request->email;
            //اطلاعات اولیه و ادرس را از ای سی در یافت می کنیم
            $user = Worker::create($result_check_national_code['personal']);
            $employment->date_of_readiness_to_start_work = $result_check_national_code['personal']['date_of_readiness_to_start_work'] ?? "";
            $employment->save();
            if (isset($result_check_national_code['address'])) {
                $address = Address::create($result_check_national_code['address']);
            }

            //در صورتی که حقوقی بود باید اطلاعات شرکت ذخیره شود.
            if ($employment->personal_type_id == 2) {
                if (isset($result_check_national_code['personal_company'])) {
                    $company = Company::create($result_check_national_code['personal_company']);
                }
                $employment->company_id = $company->id ?? "";
                $company->user_id = $user->id ?? "";
                $company->company_id_in_ic_system = $result_check_national_code['personal_company']['id'] ?? "";
                $company->save();
            }

            $employment->was_any_info_in_ic = 1;//ایا اطلاعات در ای سی بوده است.
            $employment->user_id = $user->id;

            $user->personal_id_in_ic_system = $result_check_national_code['personal']['id'];
            $user->email = $request->email;
            $employment->save();
            $user->save();
            if (isset($result_check_national_code['address'])) {
                UserAddress::create([
                    "user_id" => ($employment->personal_type_id == 1) ? $user->id : null,
                    "company_id" => ($employment->personal_type_id == 2) ? $company->id : null,
                    "address_id" => $address->id,
                    "is_default" => 1,
                ]);
            }

            if ($request->file('user_image_file_id')) {

                $user_image = File::uploadFile($request->file('user_image_file_id'), $employment->user_id . "_" . Str::random(4) . '.' . File::get_file_extension($request->file('user_image_file_id')->getClientOriginalName()), 20, 'chatify/users-avatar', true);
                $employment = Employment::find($employment->id);
                $employment->worker->image_id = $user_image->id;
                $employment->worker->save();
            }

            Employment::CreateAgent($employment);
            $employment->status_company_id = 4641402;
            $employment->save();

            if (in_array($employment->cooperation_type_id, [1, 11])) {
                $employment->post_id = $request->post_id;
                $employment->date_of_readiness_to_start_work = $request->date_of_readiness_to_start_work;
                $employment->save();
                if (isset($result_check_national_code['academic_degree'])) {
                    foreach ($result_check_national_code['academic_degree'] as $academic_degree) {
                        $user_academic_degree = UserAcademicDegree::create($academic_degree);
                        $user_academic_degree->user_id = $user->id;
                        $user_academic_degree->save();
                    }
                }
                if (isset($result_check_national_code['educational_course'])) {
                    foreach ($result_check_national_code['educational_course'] as $educational_course) {
                        $user_educational_course = UserEducationalCourse::create($educational_course);
                        $user_educational_course->user_id = $user->id;
                        $user_educational_course->save();

                    }
                }
                if (isset($result_check_national_code['dependent'])) {
                    foreach ($result_check_national_code['dependent'] as $dependent) {
                        $user_dependent = UserDependent::create($dependent);
                        $user_dependent->user_id = $user->id;
                        $user_dependent->save();

                    }
                }
                if (isset($result_check_national_code['job_information'])) {
                    foreach ($result_check_national_code['job_information'] as $job_information) {
                        $user_job_information = UserJobInformation::create($job_information);
                        $user_job_information->user_id = $user->id;
                        $user_job_information->save();

                    }
                }


            }
        }
        return redirect()->route('hr.employment.register.personal_info.index', $employment->key);
    }

}
