<?php

namespace App\Http\Controllers\HR\Employment\Admin\Supplier;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Supplier\Definition\DashboardController;
use App\Models\Accounting\CostCenter;
use App\Models\Customer\Customer;
use App\Models\HR\Agent\Agent;
use App\Models\HR\Employment\Employment;
use App\Models\Supplier\Supplier;
use App\Models\Supplier\SupplierAddress;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class DraftingContractController extends Controller
{
    public static $info = [
        "route" => "hr.employment.admin.supplier.drafting_contract.",
        "enable_status" => ["110"],
        "button" => ["caption" => "تنظیم پیش نویس قرارداد هوشمند (تامین کننده)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.supplier.drafting_contract.",

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
        $supplier_default_setting = Setting::getStringValue("supplier_default_setting");
        $data_supplier_default_setting = json_decode($supplier_default_setting);

        if (!$data_supplier_default_setting) {
            return back()->withErrors("تنظیمات پیش فرض جهت تعریف یا ویرایش تامین کنندگان مشخص نشده است.
            لطفا از منوی تنظیمات/تنظیمات اولیه جهت ثبت تنظیمات پیش فرض تامین کنندگان اقدام نمایید.");
        }

        $status_option = Option::get("active_status");
        $cost_center_option = Option::get("cost_center");
        $supplier_type_option = Option::get("supplier_type");


        $post_option_list["exit_form_require_permission_post_id"] = Option::get("posts", $data_supplier_default_setting->exit_form_require_permission_post_id);
        $post_option_list["exit_form_require_draft_permission_post_id"] = Option::get("posts", $data_supplier_default_setting->exit_form_require_draft_permission_post_id);
        $post_option_list["exit_form_guarding_require_permission_post_id"] = Option::get("posts", $data_supplier_default_setting->exit_form_guarding_require_permission_post_id);

        return view($this->view_path . "index", compact('cost_center_option', 'supplier_type_option',
            'status_option', "employment", "data_supplier_default_setting", "post_option_list"));
    }

    public function submit(Employment $employment, Request $request)
    {

        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $exsit_supplier = Supplier::where( 'user_id', $employment->user_id,)->exists();
        if ($exsit_supplier) {
            return back()->withErrors("این تامین کننده قبلا در سامانه ثبت شده است.");
        }
        $supplier_draft_contract_required_init_confirm = Setting::getIntegerValue("supplier_draft_contract_required_init_confirm");
        $supplier_draft_contract_required_final_confirm = Setting::getIntegerValue("supplier_draft_contract_required_final_confirm");
        $supplier_draft_contract_confirm = Setting::getIntegerValue("supplier_draft_contract_confirm");

        //ایجاد تامین کننده
        $supplier = Supplier::create([
            "caption" => $employment->personal_type_id == 1 ? $employment->worker->fullname() : $employment->company->caption,
            'user_id' => $employment->user_id,
            'firstname' => $employment->worker->firstname,
            "lastname" => $employment->worker->lastname,
            "national_code" => $employment->national_code,
            "register_code" => ($employment->personal_type_id == 1) ? $employment->worker->national_code : $employment->company->register_code,
            "supplier_type_id" => $employment->nationality_id == 1 ? 1 : 2,//اگر تامین کننده ایرانی بود داخل کشور و اگر خارجی بود خارج از کشور
            "active_status_id" => 1210,
            "cost_center_id" => -1,
            'image_id' => $employment->worker->image_id,
            'personal_type_id' => $employment->personal_type_id,
        ]);

        SupplierAddress::create([
            'supplier_id' => $supplier->id,
            'address_id' => ($employment->personal_type_id == 1) ? $employment->worker->user_address()->first()->address->id :
                $employment->company->user_address()->first()->address->id,
            "is_default" => 1,
        ]);
        //ذخیره ایدی تایمن کننده در کارمندان
        $employment->supplier_id = $supplier->id;
        $supplier->company_id = $employment->personal_type_id == 2 ? $employment->company_id : null;
        $supplier->start_date_of_contract=$supplier->created_at;
        $supplier->save();
        //اطلاعات مالی وفرم ورود و برگ خروج
        $supplier->update($request->all());
        $supplier->can_i_borrow_from_this_supplier = $request->can_i_borrow_from_this_supplier ? 1 : 0;
        $type = "create";
        DashboardController:: ConfirmInputForm($supplier, $type, $request);
        DashboardController:: ConfirmExitForm($supplier, $type, $request);
        DashboardController::CreateExitFormPost($supplier, $type, $request);



        $agent = Agent::where([
            'user_id' => $employment->user_id,
            'supplier_id' => null,
        ])->first();

        if ($agent) {
            $agent->supplier_id = $supplier->id;
            $agent->company_id = $employment->personal_type_id == 2 ? $employment->company_id : null;
            $agent->save();
        } else {
            return back()->withErrors("تامین کننده قبلا به عنوان نماینده تامین کننده " . $supplier->caption . " بوده است و نمی تواند نماینده تامین کننده دیگری باشد.");
        }
        $employment->status_id = 4640138;
        $employment->save();
        Employment::SendSmsNextStatusForPost($employment);
        event(new EmploymentLogEvent($employment, 4640014));//ثبت پیش نویس قرارداد هوشمند تامین کنندگان
        $employment->save();

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => "تنظیمات پیش فرض قرارد هوشمند با موفقیت ثبت گردید."]);

    }

    public function checkPermission(Employment $employment)
    {

        $result = \App\Http\Controllers\HR\Employment\Admin\DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }




}
