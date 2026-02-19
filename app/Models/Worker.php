<?php

namespace App\Models;

use App\Models\Accounting\CostCenter;
use App\Models\File\File;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\Personal\Gender;
use App\Models\HR\Personal\MilitaryInformation;
use App\Models\HR\Personal\Nationality;
use App\Models\HR\Personal\PersonalType;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\HR\User\UserAddress;
use App\Models\HR\User\UserBankAccount;
use App\Models\HR\User\UserDependent;
use App\Models\HR\User\UserEducationalCourse;
use App\Models\HR\User\UserJobInformation;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Post\PostUser;
use App\Models\Production\Production;
use App\Models\SoftwareSystem\SoftwareSystem;
use App\Models\Utility\Address\Country;
use App\Models\HR\LeaveOvertime\LeaveOvertime;
use App\Models\HR\User\CooperationType;
use App\Models\Utility\Printer\Printer;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\MultipartStream;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\True_;

class Worker extends Model
{
    use HasFactory;

    protected $table = "users";
    protected $fillable = [
        "personal_id_in_ic_system",
        "lastname",
        "national_code",
        "firstname",
        "name",
        "email",
        "password",
        "required_reset_password",
        "mobile_country_id",
        "mobile",
        "country_id",
        "father_name",
        "end_date_of_contract",
        "entry_permit_status_id",
        "exit_permit_status_id",
        "status_id",
        "image_id",
        "cooperation_type_id",
        "register_status_id",
        "country_of_nationality_id",
        "one_time_token_status_id",
        "personal_type_id",
        "gender_id",
        'nationality_id',
        'marital_status_id',
        'date_of_starting_work',
        'date_of_birth',
        'place_of_birth',
        "birth_certificate_number",
        "insurance_number",
        'start_date_of_contract',
        'right_to_work',
        'military_information_id',
        'absorption_type_id',
        'internet_account_username',
        'detailed_code',
        'cost_center_id',
        'is_possible_to_work_remotely',

    ];

    public function posts()
    {
        return $this->hasMany(PostUser::class, "user_id");
    }

    public function fullname($type = "")
    {
        $text = $this->firstname . " " . $this->lastname;
        if ($type == "with_gender_2" && $this->gender) {
            $text = $this->gender->caption2 . " " . $text;
        }
        return $text;
    }

    public function fullCaption()
    {

        return $this->fullname();
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function mobile_country()
    {
        return $this->belongsTo(Country::class, "mobile_country_id");
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function marital_status()
    {
        return $this->belongsTo(Status::class, 'marital_status_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function personal_type()
    {
        return $this->belongsTo(PersonalType::class);
    }

    public function user_job_informations()
    {
        return $this->hasMany(UserJobInformation::class, 'user_id');
    }

    public function user_dependents()
    {
        return $this->hasMany(UserDependent::class, 'user_id');
    }

    public function user_educational_courses()
    {
        return $this->hasMany(UserEducationalCourse::class, 'user_id');
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function cooperation_type()
    {
        return $this->belongsTo(CooperationType::class);
    }

    public function cost_center()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    public function user_address()
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    public function user_academic_degrees()
    {
        return $this->hasMany(UserAcademicDegree::class, 'user_id');
    }

    public function user_bank_accounts()
    {
        return $this->hasMany(UserBankAccount::class, 'user_id');
    }

    public function get_date_of_birth()
    {
        if ($this->date_of_birth)
            return jdate(Carbon::parse($this->date_of_birth)->timestamp)->format('Y/m/d');

    }

    public function get_end_date_of_contract()
    {
        if ($this->end_date_of_contract)
            return jdate(Carbon::parse($this->end_date_of_contract)->timestamp)->format('Y/m/d');

    }

    public function get_start_date_of_contract()
    {
        if ($this->start_date_of_contract)
            return jdate(Carbon::parse($this->start_date_of_contract)->timestamp)->format('Y/m/d');

    }

    public function get_date_of_starting_work()
    { // تاریخ شروع به کار
        return jdate(Carbon::parse($this->date_of_starting_work)->timestamp)->format('Y/m/d');

    }

    public function entry_permit_status()
    {
        return $this->belongsTo(Status::class, "entry_permit_status_id");
    }

    public function military_information()
    {
        return $this->belongsTo(MilitaryInformation::class,);
    }

    public function exit_permit_status()
    {
        return $this->belongsTo(Status::class, "exit_permit_status_id");
    }

    public function default_label_printer()
    {
        return $this->belongsTo(Printer::class, "default_label_printer_id");
    }

    public function default_printer()
    {
        return $this->belongsTo(Printer::class, "default_printer_id");
    }

    public function end_date_of_contract()
    {
        if ($this->end_date_of_contract) {
            return jdate(Carbon::parse($this->end_date_of_contract)->timestamp)->format('Y/m/d ');
        }

    }

    public static function get_caption_of_birth_certificate_number($nationality_id)
    {
        if ($nationality_id == 2) {
            return "شماره شناسایی کشور";
        }
        return "شماره شناسنامه";
    }

    public function image()
    {
        return $this->belongsTo(File::class, "image_id");
    }

    public function getRandom()
    {

        if ($this->random == null) {
            $this->random = Str::random(7);
            $this->save();
        }

        return $this->random;
    }

    public static function GetIdFromNationalCode($nationalcode)
    {

        $worker = Line::where("nationalcode", "like", $nationalcode)->first();

        return isset($worker) ? $worker : null;
    }

    public function presentInOrganization()
    {
        if (in_array($this->status_id, [4620001, 4620002])) {
            return true;
        }

        return false;
    }


    public function getIC()
    {
        // تابغی همه درخواست دهنده های باید داشته باشند.
        return $this->cost_center->code;
    }

    public static function GetRegisterAfterTackingCount($user_id, $leave_overtime_group_id)
    {
        // تعداد مرخصی/ماموریت های اضطراری در یک سال مالی
        $financial_year = Setting::FinancialYear();
        $start_date_time = $financial_year["start_date_time"];
        $last_date_time = $financial_year["last_date_time"];

        return LeaveOvertime::join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
        where([
            "user_id" => $user_id,
            "leave_overtime_group_id" => $leave_overtime_group_id,
            "register_after_tacking" => 1
        ])->
        where("start_datetime", ">", $start_date_time)->
        where("start_datetime", "<=", $last_date_time)->
        whereNotIn("status_id", [4630004, 4630005, 4630008,])->
        where("start_datetime", ">=", Carbon::now()->addYear(-1))->
        count();
    }

    public static function CreatePersonalInIc(Employment $employment, $token)
    {
        $settings = [
            'base_uri' => env('IC_URL') . '/api/',
            'headers' => [
            ],
            'query' => [
                'firstname' => $employment->worker->firstname,
                'lastname' => $employment->worker->lastname,
                'email' => $employment->worker->email,
                'national_code' => $employment->worker->national_code,
                'date_of_birth' => $employment->worker->date_of_birth,
                'birth_certificate_number' => $employment->worker->birth_certificate_number ?? "",
                'personal_type_id' => $employment->personal_type_id,
                'nationality_id' => $employment->nationality_id,
                'gender_id' => $employment->worker->gender_id,
                'father_name' => $employment->worker->father_name ?? "",
                'place_of_birth' => $employment->worker->place_of_birth ?? "",
                'date_of_starting_work' => $employment->date_of_readiness_to_start_work ?? "",
                'marital_status_id' => $employment->worker->marital_status_id,
                'cooperation_type_id' => $employment->cooperation_type_id,
                'date_of_readiness_to_start_work' => $employment->date_of_readiness_to_start_work,
                'mobile' => $employment->mobile,
                "country_id" => $employment->country_id,
                'token' => $token,
                "insurance_number" => $employment->worker->insurance_number,
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


    public static function CallUserImageApi(Employment $employment, $url)
    {
        $client = new Client();

        $url = $url . "/api/personal_image";
        $vars = [];
        $headers = [];

        if ($employment->worker->image_id && Storage::exists($employment->worker->image->path)) {
            // باز کردن یک جریان به فایل

            $stream = fopen(storage_path('app/' . $employment->worker->image->path), 'r');
            $vars[] = [
                'name' => 'user_image_file',
                'contents' => $stream,
            ];
        }
        $vars[] = [
            'name' => 'token',
            'contents' => env("IC_APIKEY"),
        ];
        $vars[] = [
            'name' => 'personal_id',
            'contents' => $employment->worker->personal_id_in_ic_system,
        ];

        // ساختن بدنه درخواست
        $body = new MultipartStream($vars);

        // ایجاد یک درخواست POST با فایل
        $request = new \GuzzleHttp\Psr7\Request('POST', $url, $headers, $body);

        // ارسال درخواست
        $response = $client->send($request, ['verify' => false]);

        // گرفتن بدنه پاسخ
        $body = $response->getBody();
        $body = json_decode($body, 1);
        return $body;


    }

    public static function CheckUserName($user_name)
    {
        if ($user_name == "") {
            return ["result" => false, "error" => "نام کاربری نامعتبر است."];
        }
        if (strpos($user_name , ' ') !== false) {
            return ["result" => false, "error" => "نام کاربری نمی‌تواند شامل فاصله باشد."];
        }
        if (strlen($user_name) < 8) {
            return ["result" => false, "error" => "نام کاربری باید حداقل 8 کاراکتر شامل اعداد یا حروف باشد."];
        }
        if (strlen($user_name) > 20) {
            return ["result" => false, "error" => "نام کاربری باید حداکثر 20 کاراکتر شامل اعداد یا حروف باشد."];
        }
        if (str_contains($user_name, "_")) {
            return ["result" => false, "error" => "نام کاربری نباید شامل کاراکتر _ (زیرخط) باشد"];
        }
        if (str_contains($user_name, "-")) {
            return ["result" => false, "error" => "نام کاربری نباید شامل کاراکتر - (میان خط) باشد"];
        }
        if (str_contains($user_name, "/")) {
            return ["result" => false, "error" => "نام کاربری نباید شامل کاراکتر / (اسلش) باشد"];
        }
        if (str_contains($user_name, "\\")) {
            return ["result" => false, "error" => "نام کاربری نباید شامل کاراکتر \ (اسلش) باشد"];
        }
        if (str_contains($user_name, "|")) {
            return ["result" => false, "error" => "نام کاربری نباید شامل کاراکتر |  باشد"];
        }
        return ["result" => true];
    }
}
