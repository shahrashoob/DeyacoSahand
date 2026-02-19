<?php

namespace App\Http\Controllers\HR\Employment\Admin\Customer;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\Definition\AdminController;
use App\Http\Controllers\Supplier\Definition\DashboardController;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Payment\PaymentMethodType;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerAddress;
use App\Models\Customer\CustomerPaymentMethod;
use App\Models\HR\Agent\Agent;
use App\Models\HR\Employment\Employment;
use App\Models\Order\Permision\OrderPermissionCustomer;
use App\Models\Order\Permision\OrderPermissionType;
use App\Models\Supplier\Supplier;
use App\Models\Supplier\SupplierAddress;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class DraftingContractController extends Controller
{
    //این قسمت مربوط به ذخیره اطلاعات مشتری و تاییدهها و فاکتور های پرداخت و برگ خروج می باشد.
    public static $info = [
        "route" => "hr.employment.admin.customer.drafting_contract.",
        "enable_status" => ["122"],
        "button" => ["caption" => "تنظیم پیش نویس قرارداد هوشمند (مشتری)", "class" => "btn-primary"],
        "view_path" => "hr.employment.admin.customer.drafting_contract.",

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

        //در صورتی که تنظیمات پیش فرض ایجاد نشده بود و خالی بود
        $setting = Setting::getStringValue("customer_default_setting");
        $data_customer_default_setting = json_decode($setting, true);

        if (!$data_customer_default_setting) {
            return back()->withErrors("تنظیمات پیش فرض جهت تعریف یا ویرایش مشتریان مشخص نشده است.
            لطفا از منوی تنظیمات/تنظیمات اولیه جهت ثبت تنظیمات پیش فرض تامین مشتریان اقدام نمایید.");
        }
        if (!isset($data_customer_default_setting['input_form'])) {
            return back()->withErrors("تنظیمات پیش فرض  فرم ورود جهت تعریف یا ویرایش مشتریان مشخص نشده است.
            لطفا از منوی تنظیمات/تنظیمات اولیه جهت ثبت تنظیمات پیش فرض تامین مشتریان(تنظیمات فرم ورود) اقدام نمایید.");
        }
        //نوع تایید های مشتری
        $orderPermissionType = OrderPermissionType::orderBy("priority_order")->get();

        $post_option_list = [];
        foreach ($orderPermissionType as $item) {
            $post_option_list[$item->id] = Option::get("posts", isset($data_customer_default_setting["order_permission"][$item->id]['send_sms_for_post_id'])
                ? $data_customer_default_setting["order_permission"][$item->id]['send_sms_for_post_id'] : null);
        }
//پست برگ خروج
        $post_option_list["exit_form_require_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_require_permission_post_id'] ?? 0);
        $post_option_list["exit_form_require_draft_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_require_draft_permission_post_id'] ?? 0);
        $post_option_list["exit_form_guarding_require_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_guarding_require_permission_post_id'] ?? 0);
        $post_option_list["exit_form_require_demands_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_require_demands_permission_post_id'] ?? 0);
        $payment_method_types = PaymentMethodType::get();


        $channel_option = Option::get("channel_type");
        $priority_option = Option::get("priority");
        $tariff_option = Option::get("tariff");
        $order_type_option = Option::get("order_type");
        $financial_operation_pattern_option = Option::get("financial_operation_pattern", null, 1);

        $send_order_sms=Setting::getIntegerValue("send_order_sms_for_customers");
        $send_exit_form_sms=Setting::getIntegerValue("send_exit_form_sms_for_customers");
        $send_register_sms=Setting::getIntegerValue("send_register_sms_for_customers");

        return view($this->view_path . "index", compact("data_customer_default_setting",
            "employment", "orderPermissionType", "payment_method_types", "channel_option", 'priority_option', 'tariff_option', 'order_type_option', 'financial_operation_pattern_option',
            'post_option_list',"send_order_sms","send_exit_form_sms","send_register_sms"));
    }

    public function submit(Employment $employment, Request $request)
    {

        $data = $request->data;
        $result = $this->checkPermission($employment);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $request["bail_amount" ]=str_replace(",","", $request["bail_amount" ]);
        $setting = Setting::getStringValue("customer_default_setting");

        $data_customer_default_setting = json_decode($setting, true);
        if ($request->the_max_day_for_reject_product > $data_customer_default_setting['exit_form']['the_max_day_for_reject_product']) {
            return back()->withErrors("حداکثر زمان (روز) مجاز جهت برگشت کالا توسط مشتری نباید بیشتر از مقدار پیش فرض باشد.");
        }

        if ($request->the_max_day_allowed_to_conform_exit_form_to > $data_customer_default_setting['exit_form']['the_max_day_allowed_to_conform_exit_form_to']) {
            return back()->withErrors("حداکثر زمان (روز) مجاز تایید برگ خروج از انبار توسط مشتری نباید بیشتر از مقدار پیش فرض باشد.");
        }

        $exsit_customer = Customer::where('user_id', $employment->user_id)->exists();
        if ($exsit_customer) {
            return back()->withErrors("این مشتری قبلا در سامانه ثبت شده است.");
        }
        $sum_percent = 0;
        $sum_percent_request = 0;
        $sum_percent_setting = 0;
        //ذخیره اطلاعات پرداخت
        foreach (PaymentMethodType::get() as $item) {
            //استفاده از درخواست
            if ((!$data_customer_default_setting["payment_method_type"][$item->id]['enable']) &&
                isset($data["payment_method_type"][$item->id]["checked"])) {
                $sum_percent_request += $data["payment_method_type"][$item->id]["max_percentage"];
                //استفاده از پیش فرض
            } elseif ($data_customer_default_setting["payment_method_type"][$item->id]['checked'] && $data_customer_default_setting["payment_method_type"][$item->id]['enable']) {
                $sum_percent_setting += $data_customer_default_setting["payment_method_type"][$item->id]["max_percentage"];
            }

        }
        //حداکثر در فاکتور هاباید حداقل 100 باشد
        $sum_percent = $sum_percent_request + $sum_percent_setting;
        if ($sum_percent < 100) {
            $result_percent = 100 - $sum_percent_setting;
            return back()->withErrors("جمع درصد های پرداخت باید حداقل " . $result_percent . " باشد.");
        }

//ایجاد مشتری
        $customer = Customer::create([
            "caption" => $employment->personal_type_id == 1 ? $employment->worker->fullname() : $employment->company->caption,
            'user_id' => $employment->user_id,
            'gender_id' => $employment->worker->gender_id??1,
            "birth_date" => $employment->worker->date_of_birth,
            "national_code" => $employment->national_code,
            "register_code" => ($employment->personal_type_id == 1) ? $employment->worker->birth_certificate_number : $employment->register_code,
            "active_status_id" => 1210,
            "cost_center_id" => -1,
            'image_id' => $employment->worker->image_id,
            'customer_type_id' => $employment->personal_type_id,
            "province_id" => ($employment->personal_type_id == 1) ? $employment->worker->user_address()->first()->address->province_id :
                $employment->company->user_address()->first()->address->province_id,
            'company_id' => $employment->personal_type_id == 2 ? $employment->company_id : null,
            'send_order_sms' => $request->send_order_sms?1:0,
            'send_exit_form_sms' => $request->send_exit_form_sms?1:0,
            'send_register_sms' => $request->send_register_sms?1:0,
            'payment_terms_display_in_per_factor' => $request->payment_terms_display_in_per_factor?1:0,

        ]);
        $customer->update($request->all());
        $customer->price_displayed_to_customer_with_tax = $request->price_displayed_to_customer_with_tax ? 1 : 0;
        $customer->round_fee_in_informal_sale = $request->round_fee_in_informal_sale ? 1 : 0;
        $customer->start_date_of_contract = $customer->created_at;
        $customer->save();

        //آدرس مشتری
        CustomerAddress::create([
            'customer_id' => $customer->id,
            'address_id' => ($employment->personal_type_id == 1) ? $employment->worker->user_address()->first()->address->id :
                $employment->company->user_address()->first()->address->id,
            "is_default" => 1,
        ]);
        //ذخیره ایدی مشتری در کارمندان
        $employment->customer_id = $customer->id;
        //ایجاد فرم برگ ورود
        AdminController::CreateInputForm($data_customer_default_setting, $customer, $request);
        //ایجاد فرم برگ خروج
        AdminController::CreateExitForm($data_customer_default_setting, $customer, $request);
        //ذخیره پست در برگ خروج
        AdminController::CreateExitFormPost($data_customer_default_setting, $customer, $request);
        //ذخیره تاییدیه های مشتری
        self::CreateOrderPermission($data_customer_default_setting, $customer, $request);

        // ذخیره روش پرداخت
        AdminController::CreateCustomerPaymentMethod($data_customer_default_setting, $customer, $request);
        $customer->save();
        $employment->save();

        $agent = Agent::where([
            'user_id' => $employment->user_id,
            'customer_id' => null,
        ])->first();

        if ($agent) {
            $agent->customer_id = $customer->id;
            $agent->company_id = $employment->personal_type_id == 2 ? $employment->company_id : null;
            $agent->save();

        } else {
            return back()->withErrors("مشتری قبلا به عنوان نماینده مشتری " . $customer->caption . " بوده است و نمی تواند نماینده مشتری دیگری باشد.");
        }


        //وضعیت بعدی در تنظیمات پیش فرض مشتری
        Employment::NextStatus($employment);

        return redirect()->route($this->dashboard_path . "view", $employment)->with(["success" => " پیش نویس قرارداد هوشمند با موفقیت تنظیم گردید."]);

    }

    public function checkPermission(Employment $employment)
    {

        $result = \App\Http\Controllers\HR\Employment\Admin\DashboardController::checkPermissionConditions($employment, self::$info);
        return $result;
    }

    public static function CreateOrderPermission($data_customer_default_setting, Customer $customer, Request $request)
    {
        //ذخیره تاییدیه های مشتری
        $data = $request["data"];
        $orderPermissionType = OrderPermissionType::orderBy("priority_order")->pluck("id")->toArray();

        $list = [];
        if ($request["data"]) {
            foreach ($orderPermissionType as $key) {
                $id = "order_permission_" . $key;
                $post_id = $request->$id ?? null;
                if (isset($data_customer_default_setting["order_permission"][$key]) && (!$data_customer_default_setting["order_permission"][$key]['enable'])
                    && isset($data["order_permission"][$key]) && $data["order_permission"][$key] == 1) {
//ذخیره درخواست ها
                    $list[] = new OrderPermissionCustomer(
                        [
                            "order_permission_type_id" => $key,
                            "send_sms_for_post_id" => $post_id == 0 ? null : $post_id,
                            "customer_id" => $customer->id
                        ]);
                } elseif ($data_customer_default_setting["order_permission"][$key]['enable'] && $data_customer_default_setting["order_permission"][$key]['checked']) {
                    //استفاده از پیش فرض
                    $list[] = new OrderPermissionCustomer(
                        [

                            "order_permission_type_id" => $key,
                            "send_sms_for_post_id" => $data_customer_default_setting["order_permission"][$key]['send_sms_for_post_id'],
                            "customer_id" => $customer->id
                        ]);
                }

            }
        }

        $customer->order_permission()->delete();
        $customer->order_permission()->saveMany($list);

    }

    public static function CreateCostCenter($employment)
    {
        $company_have_separate_financial_software = Setting::getIntegerValue("company_have_separate_financial_software");
        $company_have_separate_warehousing_software = Setting::getIntegerValue("company_have_separate_warehousing_software");
        $customer_draft_contract_confirm = Setting::getIntegerValue("customer_draft_contract_confirm");
        $customer_draft_contract_required_init_confirm = Setting::getIntegerValue("customer_draft_contract_required_init_confirm");
        $customer_draft_contract_required_final_confirm = Setting::getIntegerValue("customer_draft_contract_required_final_confirm");
        $customer = $employment->customer;
        //آیا شرکت نرم افزار مالی مجزا دارد؟مرکز هزینه را طبق این ایجاد می کنیم

        if ($company_have_separate_financial_software || $company_have_separate_warehousing_software) {
            $employment->status_id = 4640126; // در انتظار ثبت مرکز مشتری
        }
        if (!$company_have_separate_warehousing_software) {

            $cost_center = CostCenter::create([
                "code" => 0,
                "caption" => $employment->customer->caption,
                "status_id" => 1200,
            ]);
            $cost_center->code = "001" . "/" . $cost_center->id;
            $cost_center->save();

            $customer->cost_center_id = $cost_center->id;
            $customer->code = $cost_center->code;
            $customer->save();
        }
        if (!$company_have_separate_financial_software && !$company_have_separate_warehousing_software) {
            $cost_center = CostCenter::create([
                "code" => 0,
                "caption" => $employment->customer->caption,
                "status_id" => 1200,
            ]);
            $cost_center->code = "001" . "/" . $cost_center->id;
            $cost_center->save();
            $customer->cost_center_id = $cost_center->id;
            $customer->code = $cost_center->code;
            $customer->save();
            Employment::AddUserToPost($employment);

//در صورتی که قراداد داشت می تواند تحویل دهد.
            if ($customer_draft_contract_confirm && ($customer_draft_contract_required_init_confirm || $customer_draft_contract_required_final_confirm)) {
                $employment->status_id = 4640127;//در انتظار تحویل مدارک به بایگانی

            } else {
                $employment->status_id = 4640106;//آغاز همکاری
                $employment->customer->status_id = 1200;
                $employment->customer->save();
            }
            event(new EmploymentLogEvent($employment, 4640031));//ثبت مرکز هزینه

        }

        $employment->save();
    }

}
