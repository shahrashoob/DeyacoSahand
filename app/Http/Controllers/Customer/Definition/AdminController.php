<?php

namespace App\Http\Controllers\Customer\Definition;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Payment\PaymentMethodType;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerAddress;
use App\Models\Customer\CustomerPaymentMethod;
use App\Models\File\File;
use App\Models\HR\Agent\Agent;
use App\Models\HR\Agent\AgentType;
use App\Models\HR\User\UserAddress;
use App\Models\Order\Permision\OrderPermissionCustomer;
use App\Models\Order\Permision\OrderPermissionType;
use App\Models\Post\PostUser;
use App\Models\Supplier\Supplier;
use App\Models\User;
use App\Models\Utility\Address\Address;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use function back;
use function redirect;
use function session;
use function view;

class AdminController extends Controller
{
    //

    public $route_path = "customer_group.definition.admin.";
    public $view_path = "customer.definition.admin.";
    public static $output_form = [
        "exit_form_require_draft_permission",
        "exit_form_require_permission",
        "exit_form_loading_require_permission",
        "exit_form_guarding_require_permission",
        "checking_carrier_at_delivery_of_product",
        "exit_form_require_demands_permission",

    ];
    public static $input_form = [
        "input_form_guarding_require_permission",
        "input_form_loading_require",
        "input_form_quality_control_permission",
    ];

    public function index(Request $request)
    {

        $result = $this->checkSaleConnection();
        if ($result != "") {
            return $result;
        }

        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
        } else {
            $search = session("search_customer");
            $order_by = session("order_by_customer");
        }
        session(["search_customer" => $search, "order_by_customer" => $order_by]);

        $post_user = Auth::user()->posts->first();
        $edit_permission = $post_user->checkButtonPermission("customer_group.definition.admin.edit");
        $edit_permission_software_system = $post_user->checkButtonPermission("customer_group.definition.admin.edit_software_system");
        $list = Customer::where("code", "like", "%" . $search . "%")->orWhere("caption", "like", "%" . $search . "%")->paginate(50);
        $order_by_Option = Option::OrderBy("customer", $order_by);

        return view($this->view_path . "index", compact("list", "edit_permission_software_system", "search", "order_by", "order_by_Option", 'edit_permission'));

    }


    public function create()
    {

        $result = $this->checkSaleConnection();
        if ($result != "") {
            return $result;
        }
        $post_user = Auth::user()->posts->first();
        $edit_permission = $post_user->checkButtonPermission("customer_group.definition.admin.edit");
        if (!$edit_permission) {
            return back()->withErrors('انجام عملیات برای شما امکان پذیر نمی باشد');
        }

        $setting = Setting::getStringValue("customer_default_setting");
        $data_customer_default_setting = json_decode($setting, true);

        if (!$data_customer_default_setting) {
            return back()->withErrors("تنظیمات پیش فرض جهت تعریف یا ویرایش مشتری مشخص نشده است.
            لطفا از منوی تنظیمات/تنظیمات اولیه جهت ثبت تنظیمات پیش فرض مشتری اقدام نمایید.");
        }

        $orderPermissionType = OrderPermissionType::orderBy("priority_order")->get();

        $post_option_list = [];
        foreach ($orderPermissionType as $item) {
            $post_option_list[$item->id] = Option::get("posts", isset($data_customer_default_setting["order_permission"][$item->id]['send_sms_for_post_id'])
                ? $data_customer_default_setting["order_permission"][$item->id]['send_sms_for_post_id'] : null);
        }


        $post_option_list["exit_form_require_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_require_permission_post_id'] ?? 0);
        $post_option_list["exit_form_require_draft_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_require_draft_permission_post_id'] ?? 0);
        $post_option_list["exit_form_guarding_require_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_guarding_require_permission_post_id'] ?? 0);


        $customer = new Customer();
//        $orderPermissionType = OrderPermissionType::orderBy("priority_order")->get();
        $company_option = Option::get("company");
        $worker_option = Option::get("worker_cooperation_type", 0, 1100);
        $tariff_option = Option::get("tariff", $customer->tariff_id);
        $gender_option = Option::get("gender", $customer->gender_id);
        $customer_type_option = Option::get("customer_type");
        $allow_to_insert = 0;
        $channel_option = Option::get("channel_type", $customer->channel_id);
        $priority_option = Option::get("priority", $customer->priority_id);
        $province_option = Option::get("province", $customer->province_id);
        $order_type_option = Option::get("order_type", $customer->order_type_id);
        $software_system_option = Option::get("software_system");
        $financial_operation_pattern_option = Option::get("financial_operation_pattern", $customer->financial_operation_pattern_id, 1);
        $company_have_separate_financial_software = Setting::getIntegerValue("company_have_separate_financial_software");// نرم افزار مالی

        $country_option = Option::get("country", 112);

        $payment_method_types = PaymentMethodType::get();
        $get_the_customer_image = Setting::getIntegerValue("get_the_customer_image");
        $mode = "create";
        $model = ["name" => "customer", "route" => "import.customer.upload", "caption" => "فایل لیست مشتریان "];

        return view($this->view_path . "create", compact(
                "model", "country_option", "order_type_option",
                "province_option", "priority_option",
                "channel_option", "customer", "gender_option", "customer_type_option",
                "tariff_option", "orderPermissionType",
                "payment_method_types", "post_option_list",
                "financial_operation_pattern_option",
                'data_customer_default_setting', 'mode', 'software_system_option', 'get_the_customer_image', 'company_option', 'worker_option', 'allow_to_insert', 'company_have_separate_financial_software'


            )
        );

    }


    public function store(Request $request)
    {

        $post_user = Auth::user()->posts->first();
        $edit_permission = $post_user->checkButtonPermission("customer_group.definition.admin.edit");
        if (!$edit_permission) {
            return back()->withErrors('امکان دسترسی به این صفحه نامعتبر است.');
        }
        $customer = new Customer();
        $setting = Setting::getStringValue("customer_default_setting");
        $data_customer_default_setting = json_decode($setting, true);
        $data = $request->data;
        $sum_percent = 0;
        $sum_percent_request = 0;
        $sum_percent_setting = 0;
//         ذخیره اطلاعات پرداخت
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
        $sum_percent = $sum_percent_request + $sum_percent_setting;
        if ($sum_percent < 100) {
            $result_percent = 100 - $sum_percent_setting;
            return back()->withErrors("جمع درصد های پرداخت باید حداقل " . $result_percent . " باشد.");
        }

        $exsit_customer = Customer::where('user_id', $request->user_id)->exists();
        if ($exsit_customer) {
            return back()->withErrors("این مشتری قبلا در سامانه ثبت نام نموده است");
        }

        $exsit_agent = Agent::where('user_id', $request->user_id)->exists();
        if ($exsit_agent) {
            return back()->withErrors("این مشتری قبلا در سامانه به عنوان نماینده ثبت نام نموده است");
        }

        if ($request->customer_type_id == 2) {
            $exsit_company = Customer::where('company_id', $request->company_id)->exists();
            if ($exsit_company) {
                return back()->withErrors("این شرکت قبلا در سامانه به عنوان مشتری ثبت نام نموده است");
            }
        }

        $user_image = null;
        $result_file = \App\Http\Controllers\Supplier\Definition\DashboardController::checkFileUploded($request, "user_image_file_id", ["png", 'jpg', 'jpeg']);
        if (!$result_file["result"]) {
            return back()->withErrors($result_file["error"]);
        }
//        $username_count = Worker::where("email", "like", $request->email)->exists();
//        if ($username_count) {
//            return back()->withErrors("نام کاربری  مشابه در سیستم وجود دارد");
//        }
        $password = "";
        if ($request["password"] != "" && $request["password"] == $request["confirm_password"]) {
            $password = $request["password"];
            $request["password"] = Hash::make($password);
            $request["required_reset_password"] = 1;

        } else {
            unset($request["password"]);
        }

        if ($request->file('user_image_file_id')) {
            $user_image = File::uploadFile($request->file('user_image_file_id'), $customer->id . "_" . Str::random(4) . '.' . File::get_file_extension($request->file('user_image_file_id')->getClientOriginalName()), 20, 'chatify/users-avatar', true);

            $customer->image_id = $user_image->id;
            $customer->save();
        }

        $request["price_displayed_to_customer_with_tax"] = $request->price_displayed_to_customer_with_tax ? 1 : 0;
        $request["round_fee_in_informal_sale"] = $request->round_fee_in_informal_sale ? 1 : 0;

        $request["send_order_sms"] = $request->send_order_sms ? 1 : 0;
        $request["send_exit_form_sms"] = $request->send_exit_form_sms ? 1 : 0;
        $request["send_register_sms"] = $request->send_register_sms ? 1 : 0;

        $request["cooperation_type_id"] = 3; // مشتری

//        $user = Worker::create($request->all());
//        $user_id = $user->id;
        $request["bail_amount"] = str_replace(",", "", $request["bail_amount"]);
        $customer->user_id = $request->user_id;
        $customer->save();
        $customer->update($request->all());
        if ($request->customer_type_id == 2) {
            $customer->user_id = $customer->company->user_id;
            $customer->caption = $customer->company->caption;
        } else {
            $customer->caption = $customer->user->fullname();
        }
        $customer->province_id = ($customer->customer_type_id == 1) ? $customer->user->user_address()->first()->address->province_id :
            $customer->company->user_address()->first()->address->province_id;
        $customer->save();


        PostUser::create(["post_id" => 1100, "user_id" => $customer->user_id]);


        //ایجاد فرم برگ ورود
        self::CreateInputForm($data_customer_default_setting, $customer, $request);
        //ایجاد فرم برگ خروج
        self::CreateExitForm($data_customer_default_setting, $customer, $request);
        //ایجاد فرم برگ خروج
        self::CreateExitFormPost($data_customer_default_setting, $customer, $request);
        //ذخیره تاییدیه های مشتری
        self::CreateOrderPermission($data_customer_default_setting, $customer, $request);


//        $customer->UpdateAddress($request, 1);


        CustomerAddress::create([
            'is_default' => 1,
            'customer_id' => $customer->id,
            'address_id' => ($customer->customer_type_id == 1) ? $customer->user->user_address()->first()->address->id :
                $customer->company->user_address()->first()->address->id,
        ]);
        $address = $customer->getDefaultAddress();
        $customer->addToSaleSystem($customer->user->national_code, $password ?? "");

        // ذخیره روش پرداخت
        self::CreateCustomerPaymentMethod($data_customer_default_setting, $customer, $request);

        if ($request->send_register_sms) {
            $token3 = "_APP_NAME_";
            Notification::send("00" . ($address->mobile_country->area_code ?? "98") . $address->mobile,
                new SMSNotification(
                    "createcustomer",
                    $customer->user->email,
                    $password,
                    $token3,
                    $customer->fullName()
                ));
        }

        return redirect()->route($this->route_path . "index")->with(["success" => "یک مشتری با موفقیت اضافه شده، لطفا اطلاعات تاییدیه ها را کامل کنید."]);

    }


    public function edit(Customer $customer)
    {

        $result = $this->checkSaleConnection();
        if ($result != "") {
            return $result;
        }
        $setting = Setting::getStringValue("customer_default_setting");
        $data_customer_default_setting = json_decode($setting, true);
        if (!$data_customer_default_setting) {
            return back()->withErrors("تنظیمات پیش فرض جهت تعریف یا ویرایش مشتری مشخص نشده است.
            لطفا از منوی تنظیمات/تنظیمات اولیه جهت ثبت تنظیمات پیش فرض مشتری اقدام نمایید.");
        }
        $company_option = Option::get("company", $customer->company_id);
        $worker_option = Option::get("worker_cooperation_type", $customer->user_id, 1100);
        $orderPermissionType = OrderPermissionType::orderBy("priority_order")->get();
        $tariff_option = Option::get("tariff", $customer->tariff_id);
        $gender_option = Option::get("gender", $customer->gender_id);
        $customer_type_option = Option::get("customer_type", $customer->customer_type_id ?? 1);
        $channel_option = Option::get("channel_type", $customer->channel_id);
        $priority_option = Option::get("priority", $customer->priority_id);
        $province_option = Option::get("province", $customer->province_id);
        $order_type_option = Option::get("order_type", $customer->order_type_id);
        $financial_operation_pattern_option = Option::get("financial_operation_pattern", $customer->financial_operation_pattern_id, 1);
        $agent_list = Agent::where('customer_id', $customer->id)->get();
        $get_the_customer_image = Setting::getIntegerValue("get_the_customer_image");
        $software_system_option = Option::get("software_system", $customer->software_system_id);

        $address = $customer->getDefaultAddress();
        $country_option = Option::get("country", $address->country->id ?? 112);
        $allow_to_insert = 1;
        $company_have_separate_financial_software = Setting::getIntegerValue("company_have_separate_financial_software");// نرم افزار مالی
        $payment_method_types = PaymentMethodType::get();
        $payment_method_min_percentage = PaymentMethodType::leftJoin("customer_payment_method", "payment_method_types.id", "payment_method_type_id")->
        where("customer_id", $customer->id)->
        pluck("min_percentage", "payment_method_types.id")->toArray();

        $payment_method_max_percentage = PaymentMethodType::leftJoin("customer_payment_method", "payment_method_types.id", "payment_method_type_id")->
        where("customer_id", $customer->id)->
        pluck("max_percentage", "payment_method_types.id")->toArray();

        $payment_method_max_check_delivery_time_in_days = PaymentMethodType::leftJoin("customer_payment_method", "payment_method_types.id", "payment_method_type_id")->
        where("customer_id", $customer->id)->
        pluck("max_check_delivery_time_in_days", "payment_method_types.id")->toArray();


        foreach (self::$output_form as $item) {
            $data["exit_form"][$item]["value"] = $customer->$item ?? 0;
            $data["exit_form"][$item]["enable"] = 0;
        }

        $post_option_list = [];
        $order_permission_customer_list = OrderPermissionCustomer::where("customer_id", $customer->id)->pluck("send_sms_for_post_id", "order_permission_type_id")->toArray();
        foreach ($orderPermissionType as $item) {
            $post_option_list[$item->id] = Option::get("posts", isset($order_permission_customer_list[$item->id]) ? $order_permission_customer_list[$item->id] : 0);
        }


        $data_customer_default_setting['exit_form']['exit_form_require_permission_post_id'] = $customer->exit_form_require_permission_post_id ?? 0;
        $data_customer_default_setting['exit_form']['exit_form_require_draft_permission_post_id'] = $customer->exit_form_require_draft_permission_post_id ?? 0;
        $data_customer_default_setting['exit_form']['exit_form_guarding_require_permission_post_id'] = $customer->exit_form_guarding_require_permission_post_id ?? 0;
        $data_customer_default_setting['exit_form']['exit_form_require_demands_permission_post_id'] = $customer->exit_form_require_demands_permission_post_id ?? 0;


        $post_option_list["exit_form_require_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_require_permission_post_id']);
        $post_option_list["exit_form_require_draft_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_require_draft_permission_post_id']);
        $post_option_list["exit_form_guarding_require_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_guarding_require_permission_post_id']);
        $post_option_list["exit_form_require_demands_permission_post_id"] = Option::get("posts", $data_customer_default_setting['exit_form']['exit_form_require_demands_permission_post_id']);
        $mode = "edit";
        return view($this->view_path . "edit", compact("order_type_option",
            "payment_method_max_check_delivery_time_in_days",
            "payment_method_min_percentage", "payment_method_max_percentage", "post_option_list",
            "payment_method_types", "country_option", "address",
            "province_option", "priority_option", "channel_option", "customer",
            "gender_option", "customer_type_option", "tariff_option",
            "orderPermissionType", "financial_operation_pattern_option", 'data_customer_default_setting',
            'company_option', 'mode', 'agent_list', 'get_the_customer_image', 'worker_option', 'allow_to_insert', 'company_have_separate_financial_software', 'software_system_option'));
    }


    public function update(Request $request, Customer $customer)
    {

        $data = $request["data"];
        $list = [];
        if ($request["data"]) {
            foreach ($data["order_permission"] as $key => $item) {
                $id = "order_permission_" . $key;
                $post_id = $request->$id ?? null;
                $list[] = new OrderPermissionCustomer(
                    [
                        "order_permission_type_id" => $key,
                        "send_sms_for_post_id" => $post_id == 0 ? null : $post_id,
                        "customer_id" => $customer->id
                    ]);
            }
        }

        $customer->order_permission()->delete();

        $customer->order_permission()->saveMany($list);

        $request["input_form_guarding_require_permission"] = $request->input_form_guarding_require_permission ? 1 : 0;
        $request["input_form_loading_require"] = $request->input_form_loading_require ? 1 : 0;
        $request["input_form_quality_control_permission"] = $request->input_form_quality_control_permission ? 1 : 0;
        $request["exit_form_require_permission"] = $request->exit_form_require_permission ? 1 : 0;
        $request["exit_form_require_draft_permission"] = $request->exit_form_require_draft_permission ? 1 : 0;
        $request["exit_form_guarding_require_permission"] = $request->exit_form_guarding_require_permission ? 1 : 0;
        $request["exit_form_loading_require_permission"] = $request->exit_form_loading_require_permission ? 1 : 0;
        $request["price_displayed_to_customer_with_tax"] = $request->price_displayed_to_customer_with_tax ? 1 : 0;
        $request["exit_form_require_demands_permission"] = $request->exit_form_require_demands_permission ? 1 : 0;
        $request["checking_carrier_at_delivery_of_product"] = $request->checking_carrier_at_delivery_of_product ? 1 : 0;
        $request["round_fee_in_informal_sale"] = $request->round_fee_in_informal_sale ? 1 : 0;
        $request["get_packing_form_details"] = $request->get_packing_form_details ? 1 : 0;
        $request["send_order_sms"] = $request->send_order_sms ? 1 : 0;
        $request["send_exit_form_sms"] = $request->send_exit_form_sms ? 1 : 0;
        $request["send_register_sms"] = $request->send_register_sms ? 1 : 0;
        $request["payment_terms_display_in_per_factor"] = $request->payment_terms_display_in_per_factor ? 1 : 0;

        $request["cooperation_type_id"] = 3; // مشتری

        $request["bail_amount"] = str_replace(",", "", $request["bail_amount"]);

        $old_national_code = $customer->national_code;

//        $national_code_count = User::where("national_code", "like", $request->national_code)->
//        where("id", "!=", $customer->user_id ?? 0)->exists();
//        if ($national_code_count) {
//            return back()->withErrors("کد ملی مشابه در سیستم وجود دارد");
//        }
//
//        if ($customer->existInSaleSystem($request->national_code)) {
//            return back()->withErrors("کد ملی مشابه در  سیستم فروش وجود دارد.");
//        }


//        $username_count = Worker::where("email", "like", $request->email)->
//        where("id", "!=", $customer->user_id ?? 0)->exists();
//        if ($username_count) {
//            return back()->withErrors("نام کاربری  مشابه در سیستم وجود دارد");
//        }
        $user_image = null;
        $result_file = \App\Http\Controllers\Supplier\Definition\DashboardController::checkFileUploded($request, "user_image_file_id", ["png", 'jpg', 'jpeg']);
        if (!$result_file["result"]) {
            return back()->withErrors($result_file["error"]);
        }


//        $username_count = Worker::where("email", "like", $request->email)->
//        where("id", "!=", $customer->user_id ?? 0)->exists();
//        if ($username_count) {
//            return back()->withErrors("نام کاربری  مشابه در سیستم وجود دارد");
//        }
        if ($request["password"] != "" && $request["password"] == $request["confirm_password"]) {
            $password = $request['password'];
            $request["password"] = Hash::make($password);
            $request["required_reset_password"] = 1;

        } else {
            unset($request["password"]);
        }

        if ($customer->user_id) {
            $customer->user->update($request->all());
        } else {
            $user = Worker::create($request->all());
            $customer->user_id = $user->id;
            $customer->save();
        }

        if (!PostUser::where(["post_id" => 1100, "user_id" => $customer->user_id])->exists()) {
            PostUser::create(["post_id" => 1100, "user_id" => $customer->user_id]);
        }
        if ($request->file('user_image_file_id')) {
            $user_image = File::uploadFile($request->file('user_image_file_id'), $customer->id . "_" . Str::random(4) . '.' . File::get_file_extension($request->file('user_image_file_id')->getClientOriginalName()), 20, 'chatify/users-avatar', true);
        }

        $customer->update($request->all());

        // ذخیره آدرس
        $customer->UpdateAddress($request, 1);
        $customer->update($request->all());

//         ذخیره آدرس
//        $customer->UpdateAddress($request, 1);
        if (isset($password) && $request->send_register_sms) {
            $address = $customer->getDefaultAddress();
            Notification::send("00" . ($address->mobile_country->area_code ?? "98") . $address->mobile,
                new SMSNotification(
                    "changepasscustomer",
                    $customer->user->email,
                    $password,
                    null,
                    $customer->fullName()
                ));

        }

        $customer->addToSaleSystem($old_national_code, $password ?? "");

        // return $data;
        $sum_percent = 0;
        // ذخیره اطلاعات پرداخت
        foreach (PaymentMethodType::get() as $item) {
            if (isset($data["payment_method_type"][$item->id])) {
                $sum_percent += $data["payment_method_type"]["max_percentage"][$item->id];
            }
        }
        if ($sum_percent < 100) {
            return back()->withErrors("جمع درصد های پرداخت باید حداقل 100 باشد.");
        }

        CustomerPaymentMethod::where("customer_id", $customer->id)->delete();
        foreach (PaymentMethodType::get() as $item) {
            if (isset($data["payment_method_type"][$item->id])) {
                $min_percent = $data["payment_method_type"]["min_percentage"][$item->id];
                $max_percent = $data["payment_method_type"]["max_percentage"][$item->id];
                $check_delivery = isset($data["payment_method_type"]["max_check_delivery_time_in_days"][$item->id]) ?
                    $data["payment_method_type"]["max_check_delivery_time_in_days"][$item->id] : 0;
                CustomerPaymentMethod::create([
                    "customer_id" => $customer->id,
                    "payment_method_type_id" => $item->id,
                    "min_percentage" => $min_percent,
                    "max_percentage" => $max_percent,
                    "max_check_delivery_time_in_days" => $check_delivery
                ]);

            }
        }

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات مشتری با موفقیت بروز رسانی شد."]);
    }


    public function edit_software_system(Customer $customer)
    {
        $post_user = Auth::user()->posts->first();
        $edit_permission_software_system = $post_user->checkButtonPermission("customer_group.definition.admin.edit_software_system");
        if (!$edit_permission_software_system) {
            return back()->withErrors('امکان دسترسی به این صفحه نامعتبر است.');
        }
        $software_system_option = Option::get("software_system", $customer->software_system_id);

        return view($this->view_path . "edit_software_system", compact('software_system_option', 'customer'));

    }

    public function update_software_system(Request $request, Customer $customer)
    {

        $post_user = Auth::user()->posts->first();
        $edit_permission_software_system = $post_user->checkButtonPermission("customer_group.definition.admin.edit_software_system");
        if (!$edit_permission_software_system) {
            return back()->withErrors('انجام عملیات برای شما امکان پذیر نمی باشد.');
        }
        $customer->update($request->all());
        if ($request->software_system_id == 0) {// در صورتی که در درخواست ویرایش فاقد سامانه انتخاب کرد این ها باید نال باشند
            $customer->api_url = null;
            $customer->api_username = null;
            $customer->api_password = null;
            $customer->api_key = null;
            $customer->image_id = $user_image->id ?? "";
            $customer->save();
        }
        return redirect()->route($this->route_path . "index")->with(["success" => "تنظمات سامانه جامع مشتری با موفقیت بروز رسانی شد."]);
    }

//با توجه به اینکه نمایندگان در در تب جدا ایجاد شده است برای شرکت ها در اینجا فعلا حذف خواهد شد
    public function add_agent(Customer $customer)
    {
        $agent_list = Agent::where('customer_id', $customer->id)->get();
        $agent_type_option = Option::get("agent_type");
        $gender_option = Option::get("gender");
        $province_option = Option::get("province");
        $country_option = Option::get("country");
        return view($this->view_path . "add_agent", compact("customer", 'agent_list', "country_option", "province_option", "gender_option", "agent_type_option"));
    }

    public function submit_agent(Customer $customer, Request $request)
    {

        $agent_list = Agent::where('agent_type_id', $request->agent_type_id)->where('customer_id', $customer->id)->get();
        $agent_type = AgentType::where('id', $request->agent_type_id)->first();
        if ($agent_list->count() >= $agent_type->number_of_agent) {
            return back()->withErrors("ثبت بیش از " . $agent_type->number_of_agent . " نماینده امکان پذیر نمی باشد.");
        }
        $username_count = Worker::where("email", $request->email)->exists();
        if ($username_count) {
            return back()->withErrors("نام کاربری  مشابه در سیستم وجود دارد");
        }
        $national_code_count = Worker::where("national_code", $request->national_code)->exists();
        if ($national_code_count) {
            return back()->withErrors("کد ملی مشابه در سیستم وجود دارد");
        }
        if ($customer->existInSaleSystem($request->national_code)) {
            return back()->withErrors("کد ملی مشابه در  سیستم فروش وجود دارد.");
        }


        $user = Worker::create($request->all());
        $address = Address::create($request->all());
        UserAddress::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'is_default' => 1,
        ]);
        Agent::create([
            'customer_id' => $customer->id,
            'agent_type_id' => $request->agent_type_id,
            'has_the_right_to_sign' => $request->has_the_right_to_sign ? 1 : 0,
            'user_id' => $user->id,
        ]);

        return redirect()->back()->with(["success" => "اطلاعات نماینده با موفقیت ثبت شد."]);
    }


    public function edit_agent(Customer $customer, Agent $agent)
    {
        $agent_list = Agent::where('customer_id', $customer->id)->get();
        $address = $agent->company->user_address()->first()->address;
        $agent_type_option = Option::get("agent_type", $agent->agent_type_id);
        $gender_option = Option::get("gender", $agent->worker->gender_id);
        $province_option = Option::get("province", $customer->customer_type_id == 1 ? $agent->worker->user_address()->first()->address->province_id : $agent->company->user_address()->first()->address->province_id);
        $country_option = Option::get("country", $customer->customer_type_id == 1 ? $agent->worker->user_address()->first()->address->country_id : $agent->company->user_address()->first()->address->country_id);
        return view($this->view_path . "edit_agent", compact("customer", 'agent', 'address', 'agent_list', "country_option", "province_option", "gender_option", "agent_type_option"));
    }

    public function update_agent(Customer $customer, Agent $agent, Request $request)
    {

        $agent_list = Agent::where('agent_type_id', "like", $request->agent_type_id)->where('customer_id', "like", $customer->id)->where("id", "!=", $agent->id ?? 0)->get();
        $agent_type = AgentType::where('id', $request->agent_type_id)->first();
        if ($agent_list->count() >= $agent_type->number_of_agent) {
            return back()->withErrors("ثبت بیش از " . $agent_type->number_of_agent . " نماینده امکان پذیر نمی باشد.");
        }
        $national_code_count = Worker::where("national_code", "like", $request->national_code)->
        where("id", "!=", $agent->user_id ?? 0)->exists();
        if ($national_code_count) {
            return back()->withErrors("کد ملی مشابه در سیستم وجود دارد");
        }
        $username_count = Worker::where("email", "like", $request->email)->
        where("id", "!=", $agent->user_id ?? 0)->exists();
        if ($username_count) {
            return back()->withErrors("نام کاربری  مشابه در سیستم وجود دارد");
        }
        $agent->worker->update($request->all());
        $agent->worker->user_address()->first()->address->update($request->all());
        $agent->agent_type_id = $request->agent_type_id;
        $agent->has_the_right_to_sign = $request->has_the_right_to_sign ? 1 : 0;
        $agent->save();

        return redirect()->route($this->route_path . "edit", $customer)->with(["success" => "اطلاعات نماینده با موفقیت بروز رسانی شد."]);
    }

    public function checkSaleConnection()
    {
        $is_there_a_sales_system = Setting::getIntegerValue("is_there_a_sales_system");
        if ($is_there_a_sales_system) {
            try {
                \DB::connection("mysqlsale")->getPdo();

                return "";

            } catch (\Exception $e) {

                return back()->
                withErrors("اتصال به دیتابیس فروش برقرار نمی باشد، لطفا با پشتیبانی تماس بگیرید." . "<br/>" . $e->getMessage());
            }
        }
    }


    public static function CreateOrderPermission($data_customer_default_setting, Customer $customer, Request $request)
    {
        $data = $request["data"];
        $orderPermissionType = OrderPermissionType::orderBy("priority_order")->pluck("id")->toArray();

        $list = [];
        if ($request["data"]) {
            foreach ($orderPermissionType as $key) {
                $id = "order_permission_" . $key;
                $post_id = $request->$id ?? null;
                if (isset($data_customer_default_setting["order_permission"][$key])
                    && (!$data_customer_default_setting["order_permission"][$key]['enable']) &&
                    isset($data["order_permission"][$key])) {
//ذخیره درخواست ها
                    $list[] = new OrderPermissionCustomer(
                        [
                            "order_permission_type_id" => $key,
                            "send_sms_for_post_id" => $post_id == 0 ? null : $post_id,
                            "customer_id" => $customer->id
                        ]);
                } elseif ($data_customer_default_setting["order_permission"][$key]['enable']) {
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


    public static function CreateExitForm($data_customer_default_setting, Customer $customer, Request $request)
    {
        foreach (self::$output_form as $item) {

            if (isset($data_customer_default_setting['exit_form'][$item]) && (!$data_customer_default_setting['exit_form'][$item]['enable']) && isset($request[$item])) {
                $customer->$item = $request->$item ? 1 : 0;

            } elseif (isset($data_customer_default_setting["exit_form"][$item]['enable']) && $data_customer_default_setting["exit_form"][$item]['enable']) {
                $customer->$item = $data_customer_default_setting['exit_form'][$item]['value'] ? 1 : 0;

            } else {
                $customer->$item = 0;
            }
        }
        $customer->save();
    }

    public static function CreateInputForm($data_customer_default_setting, Customer $customer, Request $request)
    {
        foreach (self::$input_form as $item) {

            if (isset($data_customer_default_setting['input_form'][$item]) && (!$data_customer_default_setting['input_form'][$item]['enable']) && isset($request[$item])) {
                $customer->$item = $request->$item ? 1 : 0;

            } elseif (isset($data_customer_default_setting["input_form"][$item]['enable']) && $data_customer_default_setting["input_form"][$item]['enable']) {
                $customer->$item = $data_customer_default_setting['input_form'][$item]['value'] ? 1 : 0;

            } else {
                $customer->$item = 0;
            }
        }
        $customer->save();
    }

    public static function CreateExitFormPost($data_customer_default_setting, Customer $customer, Request $request)
    {
        if (isset($data_customer_default_setting['exit_form']['exit_form_require_draft_permission']) &&
            (!$data_customer_default_setting['exit_form']['exit_form_require_draft_permission']['enable']) && isset($request['exit_form_require_draft_permission'])) {
            $customer->exit_form_require_draft_permission_post_id = $request->exit_form_require_draft_permission_post_id;

        } elseif ($data_customer_default_setting["exit_form"]['exit_form_require_draft_permission']['enable'] &&
            $data_customer_default_setting["exit_form"]['exit_form_require_draft_permission']['enable']) {

            $customer->exit_form_require_draft_permission_post_id = isset($data_customer_default_setting["exit_form"]["exit_form_require_draft_permission_post_id"])
                ? $data_customer_default_setting["exit_form"]["exit_form_require_draft_permission_post_id"] : null;
        }
        if (isset($data_customer_default_setting['exit_form']['exit_form_require_permission']) &&
            (!$data_customer_default_setting['exit_form']['exit_form_require_permission']['enable']) && isset($request['exit_form_require_permission'])) {
            $customer->exit_form_require_permission_post_id = $request->exit_form_require_permission_post_id;

        } elseif ($data_customer_default_setting["exit_form"]['exit_form_require_permission']['enable'] &&
            $data_customer_default_setting["exit_form"]['exit_form_require_permission']['enable']) {

            $customer->exit_form_require_permission_post_id = isset($data_customer_default_setting["exit_form"]["exit_form_require_permission_post_id"]) ?
                $data_customer_default_setting["exit_form"]["exit_form_require_permission_post_id"] : null;
        }
        if (isset($data_customer_default_setting['exit_form']['exit_form_guarding_require_permission']) &&
            (!$data_customer_default_setting['exit_form']['exit_form_guarding_require_permission']['enable']) && isset($request['exit_form_guarding_require_permission'])) {
            $customer->exit_form_guarding_require_permission_post_id = $request->exit_form_guarding_require_permission_post_id;

        } elseif ($data_customer_default_setting["exit_form"]['exit_form_guarding_require_permission']['enable'] &&
            $data_customer_default_setting["exit_form"]['exit_form_guarding_require_permission']['enable']) {

            $customer->exit_form_guarding_require_permission_post_id = isset($data_customer_default_setting["exit_form"]["exit_form_guarding_require_permission_post_id"]) ?
                $data_customer_default_setting["exit_form"]["exit_form_guarding_require_permission_post_id"] : null;
        }
    }


    public static function CreateCustomerPaymentMethod($data_customer_default_setting, Customer $customer, Request $request)
    {

        $data = $request->data;
        CustomerPaymentMethod::where("customer_id", $customer->id)->delete();
        foreach (PaymentMethodType::get() as $item) {
            if ((!$data_customer_default_setting["payment_method_type"][$item->id]['enable']) &&
                isset($data["payment_method_type"][$item->id]["checked"])) {

                $min_percent = $data["payment_method_type"][$item->id]["min_percentage"] ?? 0;
                $max_percent = $data["payment_method_type"][$item->id]["max_percentage"] ?? 0;
                $check_delivery = isset($data["payment_method_type"][$item->id]["max_check_delivery_time_in_days"]) ?
                    $data["payment_method_type"][$item->id]["max_check_delivery_time_in_days"] : 0;
                CustomerPaymentMethod::create([
                    "customer_id" => $customer->id,
                    "payment_method_type_id" => $item->id,
                    "min_percentage" => $min_percent,
                    "max_percentage" => $max_percent,
                    "max_check_delivery_time_in_days" => $check_delivery
                ]);
            } elseif ($data_customer_default_setting["payment_method_type"][$item->id]['checked'] && $data_customer_default_setting["payment_method_type"][$item->id]['enable']) {
                //استفاده از پیش فرض
                $min_percent = $data_customer_default_setting["payment_method_type"][$item->id]["min_percentage"] ?? 0;
                $max_percent = $data_customer_default_setting["payment_method_type"][$item->id]["max_percentage"] ?? 0;
                $check_delivery = isset($data_customer_default_setting["payment_method_type"][$item->id]["max_check_delivery_time_in_days"]) ?
                    $data_customer_default_setting["payment_method_type"][$item->id]["max_check_delivery_time_in_days"] : 0;
                CustomerPaymentMethod::create([
                    "customer_id" => $customer->id,
                    "payment_method_type_id" => $item->id,
                    "min_percentage" => $min_percent,
                    "max_percentage" => $max_percent,
                    "max_check_delivery_time_in_days" => $check_delivery
                ]);
            }

        }
    }

}
