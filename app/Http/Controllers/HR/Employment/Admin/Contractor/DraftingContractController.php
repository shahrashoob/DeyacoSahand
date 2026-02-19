<?php

namespace App\Http\Controllers\HR\Employment\Admin\Contractor;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Contractor\Definition\DashboardController;
use App\Models\Accounting\CostCenter;
use App\Models\HR\Agent\Agent;
use App\Models\HR\Employment\Employment;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorAddress;
use App\Models\Supplier\Supplier;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class DraftingContractController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.contractor.drafting_contract.",
        "enable_status" => ["129"],
        "button" => ["caption" => "تنظیم پیش نویس قرارداد هوشمند (پیمانکار)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.contractor.drafting_contract.",

    ];
    var $view_path;
    var $route_path;
    protected $dashboard_path = "hr.employment.admin.dashboard.";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
    }


    public function index(Employment $employment)
    {
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $contractor_default_setting = Setting::getStringValue("contractor_default_setting");
        $data_contractor_default_setting = json_decode($contractor_default_setting);

        if (!$data_contractor_default_setting) {
            return back()->withErrors("تنظیمات پیش فرض جهت تعریف یا ویرایش پیمانکاران مشخص نشده است.
            لطفا از منوی تنظیمات/تنظیمات اولیه جهت ثبت تنظیمات پیش فرض پیمانکاران اقدام نمایید.");
        }

        $status_option = Option::get("active_status");
        $cost_center_option = Option::get("cost_center");
        $contractor_type_option = Option::get("contractor_type");


        $post_option_list["exit_form_require_permission_post_id"] = Option::get("posts", $data_contractor_default_setting->exit_form_require_permission_post_id);
        $post_option_list["exit_form_require_draft_permission_post_id"] = Option::get("posts", $data_contractor_default_setting->exit_form_require_draft_permission_post_id);
        $post_option_list["exit_form_guarding_require_permission_post_id"] = Option::get("posts", $data_contractor_default_setting->exit_form_guarding_require_permission_post_id);

        $barcode_algorithm_option = Option::get("barcode_algorithm", 0);
        return view($this->view_path . "index", compact("barcode_algorithm_option",'cost_center_option', 'contractor_type_option',
            'status_option', "employment", "data_contractor_default_setting", "post_option_list"));
    }

    public function submit(Employment $employment, Request $request)
    {

        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $exsit_contractor = Contractor::where( 'user_id', $employment->user_id,)->exists();
        if ($exsit_contractor) {
            return back()->withErrors("این پیمانکار قبلا در سامانه ثبت شده است.");
        }
        //ایجاد پیمانکار
        $contractor = Contractor::create([
            "caption" =>  $employment->personal_type_id == 1 ? $employment->worker->fullname() : $employment->company->caption,
            'user_id' => $employment->user_id,
            'firstname' => $employment->worker->firstname,
            "lastname" => $employment->worker->lastname,
            "national_code" => $employment->national_code,
            "register_code" => ($employment->personal_type_id == 1) ? $employment->worker->national_code : $employment->company->register_code,
            "active_status_id" => 1210,
            "cost_center_id" => -1,
            'personal_type_id' => $employment->personal_type_id,
            'image_id' => $employment->worker->image_id,
            "province_id"=>($employment->personal_type_id == 1) ? $employment->worker->user_address()->first()->address->province_id:
                $employment->company->user_address()->first()->address->province_id,// در صورتی که حقوقی باشد باید ادرس شرکت برداشته شود و در صورتی که حقیقی باشد باید ادرس فرد برداشته شود

        ]);

        ContractorAddress::create([
            'contractor_id' => $contractor->id,
            'address_id' =>($employment->personal_type_id == 1) ? $employment->worker->user_address()->first()->address->id:
                $employment->company->user_address()->first()->address->id,
            "is_default" => 1,
        ]);
        //ذخیره ایدی پیمانکار در کارمندان
        $employment->contractor_id = $contractor->id;
        $request["it_is_coordination_for_sending"] = $request->it_is_coordination_for_sending ? 1 : 0;
        $request["show_packing_forms_in_warehouse"] = $request->show_packing_forms_in_warehouse ? 1 : 0;
        $request["sent_address_place_type_of_transport"] = $request->sent_address_place_type_of_transport ? 1 : 0;
        $request["start_of_work_time"] = new Carbon($request->start_of_work_time_h . ':' . $request->start_of_work_time_m . ':00');
        $request["end_of_work_time"] = new Carbon($request->end_of_work_time_h . ':' . $request->end_of_work_time_m . ':00');


        //اطلاعات مالی وفرم ورود و برگ خروج
        $contractor->update($request->all());
        $contractor->code=$contractor->getCode();
        $contractor->company_id=$employment->personal_type_id == 2 ? $employment->company_id : null;
        $contractor->start_date_of_contract=$contractor->created_at;
        $contractor->save();
        $type = "create";

        DashboardController:: ConfirmInputForm($contractor, $type, $request);//فرم ورود
        DashboardController:: ConfirmExitForm($contractor, $type, $request);//فرم خروج
        DashboardController::CreateExitFormPost($contractor, $type, $request);//پست های فرم خروج

        $employment->status_id = 4640137;
        $employment->save();
        $agent = Agent::where([
            'user_id' => $employment->user_id,
            'contractor_id' => null,
        ])->first();

        if ($agent) {
            $agent->contractor_id = $contractor->id;
            $agent->company_id = $employment->personal_type_id == 2 ? $employment->company_id : null;
            $agent->save();
        } else {
            return back()->withErrors("پیمانکار قبلا به عنوان نماینده پیمانکار " . $contractor->caption . " بوده است و نمی تواند نماینده پیمانکار دیگری باشد.");
        }
        event(new EmploymentLogEvent($employment, 4640035));//ثبت پیش نویس قرارداد هوشمند پیمانکار
        $employment->save();

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "تنظیمات پیش فرض قرارداد هوشمند با موفقیت ثبت گردید."]);

    }

    public function checkPermission(Employment $employment)
    {

        $result = \App\Http\Controllers\HR\Employment\Admin\DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }



}
