<?php

namespace App\Models\HR\Employment;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\HR\Employment\Admin\ConfirmInfoController;


use App\Http\Controllers\HR\Employment\Admin\Customer\DraftingContractInitController;
use App\Http\Controllers\HR\Employment\Admin\Supplier\ConfirmDraftInformationController;
use App\Http\Controllers\HR\Employment\Register\ConfirmInformationController;
use App\Http\Controllers\HR\Employment\Register\Customer\AgentController;
use App\Models\HR\Company\Company;
use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\HR\Agent\Agent;
use App\Models\HR\Personal\Nationality;
use App\Models\HR\Personal\PersonalType;
use App\Models\HR\Selection\SelectionPostSetting;
use App\Models\HR\User\CooperationType;
use App\Models\Post\Post;
use App\Models\Post\PostDocumentReceiveStepConfirm;
use App\Models\Post\PostUser;
use App\Models\Supplier\Supplier;
use App\Models\Utility\Address\Country;
use App\Models\Utility\Notification\SMSMessage;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class Employment extends Model
{
    use HasFactory;

    protected $fillable = [
        "supplier_id",
        "user_id",
        "post_id",
        "shift_work_group_type_id",
        "current_selection_id",
        "status_id",
        'current_priority_number',
        "cooperation_type_id",
        "personal_type_id",
        "nationality_id",
        "national_code",
        'was_any_info_in_ic',
        "country_id",
        "date_of_readiness_to_start_work",
        "mobile_country_id",
        "mobile",
        'status_personal_id',
        "status_address_id",
        "status_academic_degree_id",
        "status_job_information_id",
        "status_educational_course_id",
        "status_upload_document_id",
        'status_dependent_id',
        'contract_register_id',
        'status_company_id',
        'customer_id',
        "register_code",
        "contractor_id",
        'company_id_in_ic_system',
        'company_id'
    ];
    protected $table = 'employments';

    public function fullname()
    {
        switch ($this->personal_type_id) {
            case 1:
                return $this->worker->fullname("with_gender_2");
                break;
            case 2:
                return $this->company->caption . " (" . $this->company->worker->fullname("with_gender_2") . ")";
                break;
        }
    }

    public function fullNationalCode()
    {
        switch ($this->personal_type_id) {
            case 1:
                return "کد ملی " . $this->customer->national_code;
                break;
            case 2:
                return "شناسه ملی" . $this->customer->national_code;
                break;
        }
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function cooperation_type()
    {
        return $this->belongsTo(CooperationType::class);
    }

    public function personal_type()
    {
        return $this->belongsTo(PersonalType::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function mobile_country()
    {
        return $this->belongsTo(Country::class, "mobile_country_id");
    }

    public function employment_selections()
    {
        return $this->hasMany(EmploymentSelection::class)->orderBy('priority_number');
    }

    public function employment_document_types()
    {
        return $this->hasMany(EmploymentDocumentType::class);
    }

    public function employment_selection_indicator_values()
    {
        return $this->hasMany(EmploymentSelectionIndicatorValue::class);
    }

    public function logs()
    {
        return $this->hasMany(EmploymentLog::class)->orderBy('id');;
    }

    public function create_date()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('Y/m/d');

    }

    public function get_date_of_readiness_to_start_work()
    {
        if ($this->date_of_readiness_to_start_work)
            return jdate(Carbon::parse($this->date_of_readiness_to_start_work)->timestamp)->format('Y/m/d');

    }

    public static function get_caption_of_birth_certificate_number($nationality_id)
    {

        if ($nationality_id == 2) {
            return "شماره شناسایی کشور";
        }
        return "شماره شناسنامه";
    }

    public function get_status()
    {
        switch ($this->status_id) {
            case 4640102;
            case 4640103;

                $employment_selection = $this->employment_selections()->
                where([
                    'priority_number' => $this->current_priority_number,
                    'status_id' => $this->status_id,
                ])->
                first();
                return $this->status->caption . " " . ($employment_selection->selection->caption ?? "") . "(" . $employment_selection->employment_selection_selectors()->first()->post->caption . ")";
                break;
            default:
                return $this->status->caption ?? "";
        }
    }

    //تایید گزینش کنندگان
    public static function TheRecruitmentProcessIsValid(Employment $employment)
    {
        $coopration = in_array($employment->cooperation_type_id, [1, 11]);

        if (!$coopration) {
            return [
                'result' => true,
            ];
        }
        $selection_post_settings = SelectionPostSetting::
        join("selections", "selections.id", "selection_post_settings.selection_id")->
        leftJoin("selection_selectors", "selection_selectors.post_id", "selection_post_settings.post_id")->
        where("selection_post_settings.post_id", $employment->post->id)->
        groupBy("selection_post_settings.selection_id")->
        selectRaw("count(selection_selectors.id) as count,selections.caption")->
        get();

        if ($selection_post_settings->count() == 0) {
            return [
                'result' => false,
                'error' => " تنظیمات جذب برای " . $employment->post->caption . " نامعتبر است،هیچ گزینشی برای این پست وجود ندارد. لطفا با منابع انسانی تماس بگیرید."
            ];
        }

        foreach ($selection_post_settings as $item) {
            if ($item->count == 0) {
                return [
                    'result' => false,
                    'error' => "در " . $item->caption . " هیچ گزینش کننده ای انتخاب نشده است.لطفا با منابع انسانی تماس بگیرید."
                ];
            }
        }
        return [
            'result' => true,
        ];
    }


    public function getCaptionNationalCode()
    {
        switch ($this->personal_type_id) {
            case 1: // حقیقی
                switch ($this->nationality_id) {
                    case 1: // ایران
                        return "کد ملی";
                    case 2: // خارحی
                        return "کد فراگیر";
                }

            case 2: // حقوقی

                switch ($this->nationality_id) {
                    case 1: // ایران
                        return " شناسه ملی";
                    case 2: // خارحی
                        return "کد فراگیر شرکت";
                }

        }

        return "شناسه یکتا";
    }

    public static function CheckNationalCode(Employment $employment, $token)
    {
        $settings = [
            'base_uri' => env('IC_URL') . '/api/',
            'headers' => [
            ],
            'query' => [
                'national_code' => $employment->national_code,
                'personal_type_id' => $employment->personal_type_id,
                "cooperation_type_id" => $employment->cooperation_type_id,
                'token' => $token,
            ]
        ];

        $client = new \GuzzleHttp\Client($settings);

        $request = $client->request(
            'POST',
            "check-national-code",
        );


        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

    public static function PersonalApplication($personal_id, $cooperation_type_id, $personal_type_id, $nationality_id, $app_name, $token)
    {
        $settings = [
            'base_uri' => env('IC_URL') . '/api/',
            'headers' => [
            ],
            'query' => [
                'personal_id' => $personal_id,
                'cooperation_type_id' => $cooperation_type_id,
                'personal_type_id' => $personal_type_id,
                'nationality_id' => $nationality_id,
                'app_name' => $app_name,
                'token' => $token,
            ]
        ];

        $client = new \GuzzleHttp\Client($settings);

        $request = $client->request(
            'POST',
            "personal-application",
        );


        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

    public static function CreatePersonal($request, $token)
    {
        $settings = [
            'base_uri' => env('IC_URL') . '/api/',
            'headers' => [
            ],
            'query' => [
                'firstname' => $request["firstname"],
                'lastname' => $request["lastname"],
                'mobile' => $request["mobile"],
                'country_id' => $request["country_id"],
                'national_code' => $request["national_code"],
                'date_of_birth' => $request["date_of_birth"],
                'gender_id' => $request["gender_id"],
                'personal_type_id' => $request["personal_type_id"],
                'nationality_id' => $request["nationality_id"],
                'company_name' => $request["company_name"] ?? "",
                'email' => $request["email"],
                'city_name' => $request['city_name'],
                'province_id' => $request['province_id'],
                'postal_code' => $request['postal_code'],
                'phone' => $request['phone'],
                'address' => $request['address'],
                'state_id' => 0,
                'city_id' => 0,
                'marital_status_id' => $request['marital_status_id'] ?? "",
                'father_name' => $request['father_name'] ?? "",
                'place_of_birth' => $request['place_of_birth'] ?? "",
                'insurance_number' => $request['insurance_number'] ?? "",
                'date_of_starting_work' => $request['date_of_starting_work'] ?? "",
                'academic_degree_type_id' => $request['academic_degree_type_id'] ?? "",
                'name_of_academic_degree' => $request['name_of_academic_degree'] ?? "",
                'average' => $request['average'] ?? "",
                'feild_of_academic_degree' => $request['feild_of_academic_degree'] ?? "",
                'token' => $token,
            ]
        ];
        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',
            "create-personal"
        );
        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

    public static function AddUserToPost(Employment $employment)
    {
        switch ($employment->cooperation_type_id) {
            case 1: // همکار تمام وقت
            case 11: // همکار پاره وقت
                $post = $employment->post;
                $worker = $employment->worker;
                $count_post_user = PostUser::where("post_id", $post->id)->
                groupBy("shift_work_id")->
                selectRaw("count(id) as count_shift_work, shift_work_id")->
                pluck("count_shift_work", "shift_work_id")->
                toArray();
                $shift_work_id = 0;
                if (!$post->does_it_have_shift_work || !$post->shift) {
                    $shift_work_id = 1; // اگر شیفت وجود ندارد یا پست شیفت ندارد، آن را انتخاب می کنیم.
                } else {
                    for ($k = 1; $k <= $post->shift->number_of_shift_work; $k++) {
                        if (!isset($count_post_user[$k])) {
                            $shift_work_id = $k;

                        }
                        if (isset($count_post_user[$k]) &&
                            $count_post_user[$k] < $post->max_person_number_in_shift_work
                        ) {
                            $shift_work_id = $k;
                        }
                        if ($shift_work_id > 0) {
                            break;
                        }
                    }
                }
                if ($shift_work_id > 0) {
                    // اضافه کردن فرد به پست سازمانی
                    $result = PostUser::PostAddUser($post, $worker, $shift_work_id);
                    if (!$result["result"]) {
                        $message = "در زمان تایید درخواست همکاری برای " . $worker->fullname() . "با گروه شیفت" . $shift_work_id . ":" . $result["error"];
                        SMSMessage::ExceptionError($message);
                    }

                } else {
                    $message = "در زمان تایید درخواست همکاری برای " . $worker->fullname() . " شیفت سازمانی به درستی تشخصی داده نشده است. ";
                    SMSMessage::ExceptionError($message);
                }
                self::SendSmsUserPassword($employment);

                $worker->status_id = 4620012; // در انتظار شروع همکاری
                $worker->save();
                if ($employment->status_id == 4640115) {
                    event(new EmploymentLogEvent($employment, 4640013, null, null, $employment->user_id));//تعیین پست سازمانی
                } else {
                    event(new EmploymentLogEvent($employment, 4640013));//تعیین پست سازمانی
                }

                break;
            case 6: // تامین کننده
            case 61: // نماینده تامین کننده
                PostUser::create([
                    "user_id" => $employment->worker->id,
                    "post_id" => 1300,
                    "shift_work_id" => 0
                ]);

                $employment->worker->status_id = 4620008;//خارج از سازمان
                $employment->worker->save();
                self::SendSmsEmploymentPassword($employment, 'تامین کننده گرامی');
                break;

            case 3: //مشتری
            case 31:
                PostUser::create([
                    "user_id" => $employment->worker->id,
                    "post_id" => 1100,
                    "shift_work_id" => 0
                ]);

                $employment->worker->status_id = 4620008;//خارج از سازمان
                $employment->worker->save();

                self::SendSmsEmploymentPassword($employment, "مشتری گرامی");
                break;
            case 2:
            case 21:
                PostUser::create([
                    "user_id" => $employment->worker->id,
                    "post_id" => 1200,
                    "shift_work_id" => 0
                ]);
                $employment->worker->status_id = 4620008;//خارج از سازمان
                $employment->worker->save();
                self::SendSmsEmploymentPassword($employment, 'پیمانکارگرامی');
                break;
        }
    }

    public
    static function SendSmsUserPassword(Employment $employment)
    {
        if ($employment->cooperation_type_id == 3) {
            $rlt = self::SendSmsForCustomer($employment);
            if ($rlt == false) {
                return true;
            }
        }
        $worker = $employment->worker;
        $newPassword = \Illuminate\Support\Str::random(8);
        $hashedPassword = Hash::make($newPassword);

        $worker->cooperation_type_id = $employment->cooperation_type_id;
        $worker->password = $hashedPassword;
        $worker->required_reset_password = 1;
        $worker->save();
        $token3 = "_APP_NAME_";
        Notification::send(
            "00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
            new SMSNotification("createuser", $worker->email, $newPassword, $token3, $worker->fullname()));

    }

    static function SendSmsEmploymentPassword(Employment $employment, $title)
    {
        if ($employment->cooperation_type_id == 3 && $employment->customer && $employment->customer->send_register_sms == 0) {
            return true; // نیازی به ارسال پیامک نیست.
        }
        $worker = $employment->worker;
        $newPassword = \Illuminate\Support\Str::random(8);
        $hashedPassword = Hash::make($newPassword);

        $worker->cooperation_type_id = $employment->cooperation_type_id;
        $worker->password = $hashedPassword;
        $worker->required_reset_password = 1;
        $worker->save();
        $token3 = "_APP_NAME_";
        $worker_fullname = $worker->fullname("with_gender_2");
        $token10 = $title . " " . $worker_fullname;
        // اگر مشتری حقوقی باشد، نام شرکت را هم اضافه می کنیم.
        if ($employment->customer && $employment->customer->company) {
            $token10 = $worker_fullname
                . ".مدیرعامل.محترم." . $employment->customer->company->caption;
        }
        Notification::send(
            "00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
            new SMSNotification("employmentcustomercreate", $worker->email, $newPassword, $token3, $token10));

    }

    public
    static function NextStatus(Employment $employment)
    {
        switch ($employment->status_id) {

            case 4640100://در انتظار تایید مشخصات

                $count_status = 0;
                if ($employment->status_personal_id == 4641401) {
                    $count_status++;
                }
                if ($employment->status_address_id == 4641401) {
                    $count_status++;
                }
                if ($employment->status_job_information_id == 4641401) {
                    $count_status++;
                }
                if ($employment->status_academic_degree_id == 4641401) {
                    $count_status++;
                }
                if ($employment->status_dependent_id == 4641401) {
                    $count_status++;
                }
                if ($employment->status_educational_course_id == 4641401) {
                    $count_status++;
                }

                if ($employment->status_id == 4640100 && $count_status == 0) {

                    //تعینن گزینش کنندگان برای هرپست
                    $recruitment_process_is_valid = Employment::TheRecruitmentProcessIsValid($employment);
                    if (!$recruitment_process_is_valid['result']) {
                        return [
                            "result" => false,
                            'error' => $recruitment_process_is_valid['error']
                        ];
                    }

                    ConfirmInfoController::confirmEmployeeInformation($employment);

                    $employment->status_id = 4640102;
                    $employment->save();
                    Employment::SendSmsNextStatusForPost($employment);
                }
                return [
                    "result" => true,
                ];
                break;
            case 4640301://در حال تکمیل فرم درخواست

                $post_document_receive_step = PostDocumentReceiveStepConfirm::where([
                    "post_id" => $employment->post_id,
                    "confirm_type" => 1,
                ])->exists();


                if (($employment->was_any_info_in_ic == 1)) {//در صورتی که در ای سی وجود داشته باشد باید در انتظار هماهنگی گزینش قرار بگیرد
                    //تعینن گزینش کنندگان برای هرپست
                    $recruitment_process_is_valid = Employment::TheRecruitmentProcessIsValid($employment);
                    if (!$recruitment_process_is_valid['result']) {
                        return [
                            "result" => false,
                            'error' => $recruitment_process_is_valid['error']
                        ];

                    }
                    ConfirmInfoController::confirmEmployeeInformation($employment);

                    $employment->status_id = 4640102; // در انتظارهماهنگی

                    event(new EmploymentLogEvent($employment, 4640001, null, null, $employment->user_id));
                    $status_token = "در انتظار هماهنگی مصاحبه";
                    $template = "employmentregister";

                    ConfirmInformationController::SendSmsConfirmInfo($employment, $status_token, $template);
                } else if ($post_document_receive_step) {//در صورتی که در ایس ی وجود نداشته باشد و مدارک مرحبه اول برای بارگزاری وجود داشته باشد باید در انتظار تایید مشخصات مرحله اول قرار بگیرد.
                    $employment->status_id = 4640100; // در انتظارتایید مشخصات مرحله اول

                    $employment->worker->status_id = 4620013; // // در انتظار بررسی مشخصات فردی
                    $employment->worker->save();

                    event(new EmploymentLogEvent($employment, 4640001, null, null, $employment->user_id));
                } else {
                    //تعینن گزینش کنندگان برای هرپست
                    $recruitment_process_is_valid = Employment::TheRecruitmentProcessIsValid($employment);
                    if (!$recruitment_process_is_valid['result']) {
                        return [
                            "result" => false,
                            'error' => $recruitment_process_is_valid['error']
                        ];

                    }
                    ConfirmInfoController::confirmEmployeeInformation($employment);

                    $employment->status_id = 4640102; // در انتظارهماهنگی

                    event(new EmploymentLogEvent($employment, 4640001, null, null, $employment->user_id));
                    $status_token = "در انتظار هماهنگی مصاحبه";
                    $template = "employmentregister";

                    ConfirmInformationController::SendSmsConfirmInfo($employment, $status_token, $template);
                }
                $employment->save();
                return [
                    "result" => true,
                ];
            case 4640107://در حال بارگزرای مدارک

                $employment->status_id = 4640116; // در انتظار تایید مشخصات مرحله دوم
                $employment->save();
                Employment::SendSmsNextStatusForPost($employment);
                event(new EmploymentLogEvent($employment, 4640019, null, null, $employment->user_id));
                return [
                    "result" => true,
                ];


//                }

                break;
            case 4640116://در حال تایید مشخصات مرحله دوم
                $count_status = 0;
                if ($employment->status_personal_id == 4641401) {
                    $count_status++;
                }
                if ($employment->status_address_id == 4641401) {
                    $count_status++;
                }
                if ($employment->status_job_information_id == 4641401) {
                    $count_status++;
                }
                if ($employment->status_educational_course_id == 4641401) {
                    $count_status++;
                }
                if ($employment->status_academic_degree_id == 4641401) {
                    $count_status++;
                }
                if ($employment->status_dependent_id == 4641401) {
                    $count_status++;
                }
                if ($employment->status_id == 4640116 && $count_status == 0) {

                    //اگر حساب رسمی دارد باید طب کارراتایید کند

                    if ($employment->worker->absorption_type_id == 1) {
                        $employment->status_id = 4640118; // در انتظارتایید طب کار
                    } else {
                        $employment->status_id = 4640121; // در انتظارثبت اطلاعات بانکی
                    }
                    $employment->save();

                }

                return [
                    "result" => true,
                ];
                break;
            case 4640108://در انتظار اصلاح فرم در خواست
                if ($employment->status_address_id == 4641403) { // عدم تایید
                    $employment->status_address_id = 4641401; // در انتظار بررسی
                }
                if ($employment->status_personal_id == 4641403) {
                    $employment->status_personal_id = 4641401;
                }
                if ($employment->status_job_information_id == 4641403) {
                    $employment->status_job_information_id = 4641401;
                }
                if ($employment->status_educational_course_id == 4641403) {
                    $employment->status_educational_course_id = 4641401;
                }
                if ($employment->status_academic_degree_id == 4641403) {
                    $employment->status_academic_degree_id = 4641401;
                }
                if ($employment->status_dependent_id == 4641403) {
                    $employment->status_dependent_id = 4641401;
                }

                $employment_log = $employment->logs()->where('event_id', 4640002)->count();
                if ($employment_log == 1) {
                    switch ($employment->cooperation_type_id) {
                        case 1:
                        case 11:
                        case 4:
                        case 5:
                            $employment->status_id = 4640116; //در انتظار بررسی مشخصات مرحله دوم
                            break;
                        case 2:
                            $employment->status_id = 4640140;//  در انتظار تایید مشخصات مرحله اول (پیمانکار)
                            break;
                        case 3:
                            $employment->status_id = 4640139; // در انتظار تایید مشخصات مرحله اول (مشتری)
                            break;
                        case 6:
                            $employment->status_id = 4640141; // در انتظار تایید مشخصات مرحله اول (تامین کننده)
                            break;

                    }

                    $employment->save();
                    event(new EmploymentLogEvent($employment, 4640022, null, null, $employment->user_id));
                } else {
                    switch ($employment->cooperation_type_id) {
                        case 1:
                        case 11:
                        case 4:
                        case 5:
                            $employment->status_id = 4640100; //در انتظار بررسی مشخصات مرحله اول
                            break;
                        case 2:
                            $employment->status_id = 4640140;//  در انتظار تایید مشخصات مرحله اول (پیمانکار)
                            break;
                        case 3:
                            $employment->status_id = 4640139; // در انتظار تایید مشخصات مرحله اول (مشتری)
                            break;
                        case 6:
                            $employment->status_id = 4640141; // در انتظار تایید مشخصات مرحله اول (تامین کننده)
                            break;

                    }

                    $employment->save();
                    event(new EmploymentLogEvent($employment, 4640022, null, null, $employment->user_id));
                }


                return [
                    "result" => true,
                ];
                break;
            case  4640136: // در انتظار تایید پیش نویس اطلاعات مشتری

                $customer_draft_contract_confirm = Setting::getIntegerValue("customer_draft_contract_confirm");
                $customer_draft_contract_required_init_confirm = Setting::getIntegerValue("customer_draft_contract_required_init_confirm");
                $customer_draft_contract_required_final_confirm = Setting::getIntegerValue("customer_draft_contract_required_final_confirm");


                if ($customer_draft_contract_confirm) {
                    if ($customer_draft_contract_required_init_confirm) {

                        $employment->status_id = 4640124;//در انتظار تایید اولیه قرارداد هوشمند
                        Employment::SendSmsNextStatusForPost($employment);
                    } elseif ($customer_draft_contract_required_final_confirm) {
                        $employment->status_id = 4640125;//در انتظار تایید نهایی قرارداد هوشمند
                        Employment::SendSmsNextStatusForPost($employment);
                    } else {

                        $employment->status_id = 4640123;//در انتظار تایید قرارداد توسط مشتری
                        self::SendSmsConfirmContract($employment, 'employmentconfirmcontract');
                    }
                } else {
                    \App\Http\Controllers\HR\Employment\Admin\Customer\DraftingContractController::CreateCostCenter($employment);//ثبت مرکز هزینه
                }
                $employment->save();
                event(new EmploymentLogEvent($employment, 4640043));//تایید پیش ن.یس اطلاعات

                return [
                    "result" => true,
                ];
                break;

            case  4640122: // در انتظار تنظیم پیشنویس قرادادمشتری
                $employment->status_id = 4640136;
                $employment->save();
                event(new EmploymentLogEvent($employment, 4640028));//ثبت پیش نویس قرارداد هوشمند مشتریان

                return [
                    "result" => true,
                ];
                break;
            case  4640124://تایید اولیه قراداد مشتری
                $customer_draft_contract_confirm = Setting::getIntegerValue("customer_draft_contract_confirm");
                $customer_draft_contract_required_final_confirm = Setting::getIntegerValue("customer_draft_contract_required_final_confirm");

                if ($customer_draft_contract_confirm) {
                    if ($customer_draft_contract_required_final_confirm) {
                        $employment->status_id = 4640125;//در انتظار تایید نهایی قرارداد هوشمند
                    } else {

                        $employment->status_id = 4640123;//در انتظار تایید قرارداد توسط مشتری
                        self::SendSmsConfirmContract($employment, 'employmentconfirmcontract');
                    }
                } else {
                    \App\Http\Controllers\HR\Employment\Admin\Customer\DraftingContractController::CreateCostCenter($employment);//ثبت مرکز هزینه
                }
                $employment->save();
                event(new EmploymentLogEvent($employment, 4640029));// تایید اولیه قرارداد هوشمند مشتری


                return [
                    "result" => true,
                ];
                break;
            case 4640111://در انتظار پیش نویس اولیه قرارداد تامین کننده
                $supplier_draft_contract_required_final_confirm = Setting::getIntegerValue("supplier_draft_contract_required_final_confirm");
                $supplier_draft_contract_confirm = Setting::getIntegerValue("supplier_draft_contract_confirm");
                if ($supplier_draft_contract_confirm) {
                    if ($supplier_draft_contract_required_final_confirm) {
                        Employment::SendSmsNextStatusForPost($employment);
                        $employment->status_id = 4640112;//در انتظار تایید نهایی قرارداد هوشمند

                    } elseif (!$supplier_draft_contract_required_final_confirm) {
                        $employment->status_id = 4640113;//در انتظار تایید قرارداد توسط تامین کننده
                        Employment::SendSmsConfirmContract($employment, 'employmentconfirmcontract');
                    }
                } else {
                    \App\Http\Controllers\HR\Employment\Admin\Supplier\ConfirmDraftInformationController::CreateCostCenterSupplier($employment);//ثبت مرکز هزینه
                }

                $employment->save();
                event(new EmploymentLogEvent($employment, 4640015));//ثبت اولیه قرارداد هوشمند تامین کنندگان
                return [
                    "result" => true,
                ];
                break;
            case 4640130://در انتظار پیش نویس اولیه قرارداد پیمانکار
                $contractor_draft_contract_required_final_confirm = Setting::getIntegerValue("contractor_draft_contract_required_final_confirm");
                $contractor_draft_contract_confirm = Setting::getIntegerValue("contractor_draft_contract_confirm");
                if ($contractor_draft_contract_confirm) {
                    if ($contractor_draft_contract_required_final_confirm) {
                        $employment->status_id = 4640131;//در انتظار تایید نهایی قرارداد هوشمند
                        Employment::SendSmsNextStatusForPost($employment);

                    } elseif (!$contractor_draft_contract_required_final_confirm) {
                        $employment->status_id = 4640132;//در انتظار تایید قرارداد توسط پیمانکار
                        Employment::SendSmsConfirmContract($employment, 'employmentconfirmcontract');
                    }
                } else {
                    \App\Http\Controllers\HR\Employment\Admin\Contractor\ConfirmDraftInformationController::CreateCostCenterContractor($employment);//ثبت مرکز هزینه
                }

                $employment->save();
                event(new EmploymentLogEvent($employment, 4640036));//تایید اولیه  پیش نویس قرارداد هوشمند (پیمانکار)
                return [
                    "result" => true,
                ];
                break;

        }
    }

    public
    static function CreateAgent(Employment $employment)
    {


        switch ($employment->cooperation_type_id) {
            case 3:
            case 2:
            case 6:
                $exist_Agent = Agent::where('user_id', $employment->user_id)->firstOrCreate(
                    [
                        'user_id' => $employment->user_id,
                    ],
                    [
                        'employment_id' => $employment->id,
                        'agent_type_id' => $employment->personal_type_id == 1 ? 3 : 1,// حقیقی نماینده است و حقوقی مدیر عامل در نوع نماینده قرار می گیرد
                        'has_the_right_to_sign' => 1,
                        'active_code' => Str::random(15),
                        'status_id' => 4642002,
                        'company_id' => $employment->personal_type_id == 2 ? $employment->company_id : null,

                    ]);
//                AgentController::SendSmsForAgent($employment, $exist_Agent);
                $employment->status_company_id = 4641401; // در انتظار بررسی
                $employment->save();
                break;
        }

    }


    public static function SendSmsConfirmContract(Employment $employment, $template)
    {
        if ($employment->cooperation_type_id == 3 && $employment->customer && $employment->customer->send_register_sms == 0) {
            return true; // نیازی به ارسال پیامک نیست.
        }
        $software_name = Setting::getStringValue("software_name");
        $token10 = $employment->worker->fullname("with_gender_2");
        $token = $software_name;
        $token2 = $employment->cooperation_type->caption . " " . $employment->personal_type->caption;
        $token3 = "_APP_NAME_" . "/DCESLink/" . $employment->key;
        $token20 = null;
        Notification::send("00" . ($employment->mobile_country->area_code ?? "98") . $employment->mobile,
            new SMSNotification($template,
                $token,
                $token2,
                $token3,
                $token10,
                $token20));

    }

    public static function SendSmsNextStatusForPost(Employment $employment)
    {
        if ($employment->cooperation_type_id == 3) {
            $rlt = self::SendSmsForCustomer($employment);
            if ($rlt == false) {
                return true;
            }
        }
        $employment_notification = EmploymentNotificationSetting::where('status_id', $employment->status_id)->whereNotNull('post_id')->first();
        if ($employment_notification) {
            $post_user = PostUser::where('post_id', $employment_notification->post_id)->get();
            foreach ($post_user as $item) {
                $token10 = $item->worker->fullname("with_gender_2");
                $token = "_APP_NAME_";
                $token2 = Setting::getStringValue("software_name");
                $token3 = $employment->status->caption;
                $token20 = $employment->worker->fullname();

                Notification::send("00" . ($item->worker->mobile_country->area_code ?? "98") . $item->worker->mobile,
                    new SMSNotification('employmentnotification',
                        $token,
                        $token2,
                        $token3,
                        $token10,
                        $token20));
            }
        }
    }

    public static function SendSmsForCustomer(Employment $employment)
    {
        if ($employment->customer) {
            if ($employment->customer->send_register_sms == 0) {
                return false; // نیازی به ارسال پیامک نیست.
            }
        } else { // اگر هنوز مشتری وجود ندارد به صورت پیش فرض از تنظیمات سیستم می گیریم.
            $send_register_sms_for_customers = Setting::getIntegerValue('send_register_sms_for_customers');
            if ($send_register_sms_for_customers == 0) { // اگر به صورت پیش فرض باید پیامک  برای مشتری ارسال شود.
                return false; // نیازی به ارسال پیامک نیست.
            }
        }
        return true;
    }
}
