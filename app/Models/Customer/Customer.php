<?php

namespace App\Models\Customer;

use App\Models\Accounting\CostCenter;
use App\Models\Accounting\FinancialOperation\FinancialOperationPattern;
use App\Models\Accounting\SellingType;
use App\Models\Accounting\Tariff\Tariff;
use App\Models\HR\Company\Company;
use App\Models\File\File;
use App\Models\Form\Form;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Station;
use App\Models\Order\Order;
use App\Models\Order\OrderType;
use App\Models\Order\Permision\OrderPermissionCustomer;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\SoftwareSystem\SoftwareSystem;
use App\Models\User;
use App\Models\Utility\Address\Address;
use App\Models\Utility\Address\Province;
use App\Models\HR\Personal\Gender;
use App\Models\Utility\Priority;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;


class Customer extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = [
        "code",
        "caption",
        "tariff_id",
        'company_id',
        "user_id",
        "channel_id",
        "sub_channel_caption",
        "birth_date",
        "gender_id",
        "customer_type_id",
        "economic_number",
        "bail_amount",
        "cash_off_percent",
        "percent_tax_off_in_formal_factor",
        "priority_id",
        "register_code",
        "detailed_code",
        "province_id",
        "national_id",
        "national_code",
        "order_type_id",
        "status_id",
        'cost_center_id',
        'image_id',
        "start_date_of_contract",
        "end_date_of_contract",
        "exit_form_require_draft_permission",
        "exit_form_require_draft_permission_post_id",
        'company_id_in_ic_system',
        "exit_form_require_permission",
        "exit_form_require_permission_post_id",

        "exit_form_guarding_require_permission",
        "exit_form_guarding_require_permission_post_id",
        "exit_form_require_demands_permission",
        "exit_form_require_demands_permission_post_id",
        "exit_form_loading_require_permission",

        'input_form_guarding_require_permission',
        'input_form_loading_require',
        'input_form_quality_control_permission',

        "the_max_day_allowed_to_conform_exit_form_to",
        "the_max_day_for_reject_product",

        "percent_max_informal_purchase",
        "increase_percentage_in_informal_sale",
        "increase_percentage_deadline_per_day",
        "round_fee_in_informal_sale",
        "price_displayed_to_customer_with_tax",

        "checking_carrier_at_delivery_of_product",
        "financial_operation_pattern_id",
        'software_system_id',
        'api_url',
        'api_username',
        'api_password',
        'api_key',
        "financial_operation_pattern_id",
        "parent_id",

        "get_packing_form_details",
        "send_order_sms",
        "send_exit_form_sms",
        "send_register_sms",
        "payment_terms",
        "payment_terms_display_in_per_factor"
    ];


    public function software_system()
    {
        return $this->belongsTo(SoftwareSystem::class);
    }

    public function channelType()
    {
        return $this->belongsTo(ChannelType::class, "channel_id", "id");
    }

    public function customer_type()
    {
        return $this->belongsTo(CustomerType::class);
    }

    public function parent()
    { // شناسه مشتری پدر (یا معرف)
        return $this->belongsTo(Customer::class, "parent_id");
    }

    public function order_type()
    {
        return $this->belongsTo(OrderType::class, 'order_type_id');
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function channel()
    {
        return $this->belongsTo(ChannelType::class, 'channel_id');
    }

    public function image()
    {
        return $this->belongsTo(File::class, "image_id");
    }

    public function company()
    {
        return $this->belongsTo(Company::class, "company_id");
    }

    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }

    public function get_end_date_of_contract()
    {
        return jdate(Carbon::parse($this->end_date_of_contract)->timestamp)->format('Y/m/d');
    }

    public function get_start_date_of_contract()
    {
        return jdate(Carbon::parse($this->start_date_of_contract)->timestamp)->format('Y/m/d');
    }

    public function exit_form_require_draft_permission_post()
    {
        return $this->belongsTo(Post::class, 'exit_form_require_draft_permission_post_id');
    }

    public function exit_form_guarding_require_permission_post()
    {
        return $this->belongsTo(Post::class, 'exit_form_guarding_require_permission_post_id');
    }

    public function exit_form_require_permission_post()
    {
        return $this->belongsTo(Post::class, 'exit_form_require_permission_post_id');
    }

    public function cost_center()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function getDefaultAddress()
    {
        $customer_address = CustomerAddress::where(["customer_id" => $this->id, "is_default" => 1])->first();

        return $customer_address->address ?? null;
    }

    public function fullCaption()
    {
        return $this->caption;
    }

    public function user()
    {
        return $this->belongsTo(Worker::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function order_permission()
    {
        return $this->hasMany(OrderPermissionCustomer::class);
    }

    public function financial_operation_pattern()
    {
        return $this->belongsTo(FinancialOperationPattern::class);
    }

    public function valid_tariff()
    {

        if (!$this->tariff) {
            return [
                "result" => false,
                "error" => "هیچ تعرفه ای برای شما تعریف نشده است، لطفا با واحد فروش تماس بگیرید."
            ];
        }
        if (!($this->tariff->start_datetime <= Carbon::now() && $this->tariff->end_datetime >= Carbon::now())) {
            return [
                "result" => false,
                "error" => "اعتبار تعرفه شما پایان یافته است، لطفا با واحد فروش تماس بگیرید."
            ];
        }

        return [
            "result" => true
        ];
    }

    public function getIC()
    {
        // تابغی همه درخواست دهنده های باید داشته باشند.
        return $this->code;
    }

    public function fullName()
    {
        // ( $this->gender->caption2 ?? "" ) . " " .
        return $this->user->fullname();

    }

    public function UpdateAddress($request, $is_default)
    {
        $address = $this->getDefaultAddress();
        if (isset($address)) {
            $address->update($request->all());
        } else {

            $address = Address::create($request->all());

            $customer_address = new CustomerAddress();
            $customer_address->is_default = $is_default;
            $customer_address->customer_id = $this->id;
            $customer_address->address_id = $address->id;
            $customer_address->version = 1;
            $customer_address->save();
        }
    }

    public function has_order_permission($order_permission_type_id)
    {
        return OrderPermissionCustomer::where([
            "customer_id" => $this->id,
            "order_permission_type_id" => $order_permission_type_id
        ])->exists();
    }

    public function get_order_id_permission()
    {
        return OrderPermissionCustomer::where(["customer_id" => $this->id])->pluck('order_permission_type_id')->toArray();
    }

    public static function findWidthUserId($user_id)
    {
        $customer = Customer::where("user_id", $user_id)->first();

        return $customer ? $customer : false;
    }

    public function addToSaleSystem($national_code, $password)
    {

        $is_there_a_sales_system = Setting::getIntegerValue("is_there_a_sales_system");//ایا سامانه فروش مجزا
        if (!$is_there_a_sales_system) {
            return false;
        }

        $sale_user = User::on("mysqlsale")->
        where("national_code", $national_code)->
        first();

        $data = $this->user->toArray();
        $data["required_reset_password"] = 1;
        $data["email"] = $this->user->email;
        if ($password) {
            $data["password"] = Hash::make($password);
        } else {
            unset($data["password"]);
        }


        if (!$sale_user) {

            $sale_user = User::on("mysqlsale")->create($data);
            PostUser::create(["post_id" => 1100, "user_id" => $sale_user->id]);
        } else {
            $sale_user->update($data);
        }


    }

    public function existInSaleSystem($national_code)
    {

        $is_there_a_sales_system = Setting::getIntegerValue("is_there_a_sales_system");
        if (!$is_there_a_sales_system) {
            return false;
        }

        return $sale_user = User::on("mysqlsale")->
        where("national_code", $national_code)->
        where("national_code", "!=", $this->user->national_code ?? 0)->
        exists();


    }

    // Sale System Function
    public static function getCustomerFromDB(User $user, $k)
    {
        $erp_user = \App\User::on("mysql_erp_" . $k)->where("national_code", $user->national_code)->first();
        if (!$erp_user) {
            return null;
        }

        $customer = Customer::on("mysql_erp_" . $k)->where("user_id", $erp_user->id)->first();
        if (!$customer) {
            null;
        }

        return $customer;
    }

    public function getAllSellingAmount()
    {

        return Order::getAllSellingAmount($this->id);

    }


    public function nextStatusForExistForm($current_status_id)
    {

        // با توجه به اولویت های موجود برای برگ خروج و وضعیت جاری، وضعیت بعدی مشخص می شود.

        switch ($current_status_id) {
            case 0: // گرفتن اولین وضعیت فرم خروج
                if ($this->exit_form_require_demands_permission) {
                    return 500000514; // وصول مطالبات
                } elseif ($this->exit_form_require_draft_permission) {
                    return 500000515; // پیش نویس
                } elseif ($this->exit_form_require_permission) {
                    return 500000520; // نهایی
                } elseif ($this->exit_form_loading_require_permission) {
                    return 500000525; // بارگیری
                } elseif ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000500; // مشتری
                }
                break;

            case 500000514:  // وصول مطالبات
                if ($this->exit_form_require_draft_permission) {
                    return 500000515; // پیش نویس
                } elseif ($this->exit_form_require_permission) {
                    return 500000520; // نهایی
                } elseif ($this->exit_form_loading_require_permission) {
                    return 500000525; // بارگیری
                } elseif ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000500; // مشتری
                }
                break;
            case 500000515:  // پیش نویس
                if ($this->exit_form_require_permission) {
                    return 500000520; // نهایی
                } elseif ($this->exit_form_loading_require_permission) {
                    return 500000525; // بارگیری
                } elseif ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000500; // مشتری
                }
                break;
            case 500000520:  // نهایی
                if ($this->exit_form_loading_require_permission) {
                    return 500000525; // بارگیری
                } elseif ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000500; // مشتری
                }
                break;
            case 500000525:  // بارگیری
                if ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000500; // مشتری
                }
                break;
            case 500000530:   // نگبانی
                return 500000500; // مشتری
                break;
            case 500000500:   // مشتری
                return 500000200; // تایید شده
                break;

        }

        1 / 0;

    }

    public function nextStatusForInputForm($current_status_id, $has_general_item = false)
    {

        // با توجه به اولویت های موجود برای فرم ورود و وضعیت جاری، وضعیت بعدی مشخص می شود.

        switch ($current_status_id) {
            case 0: // گرفتن اولین وضعیت فرم ورود
                if ($this->input_form_guarding_require_permission) {
                    return 500000710; // در انتظار تایید نگهبانی
                } elseif ($this->input_form_quality_control_permission) {
                    return 500000535; // در انتظار تایید کنترل کیفیت
                } else {
                    return $has_general_item ? 500000430 : 500000410; // در انتظار تکمیل اطلاعات
                }
                break;
            case 500000710:  // در انتظار تایید نگهبانی
                if ($this->input_form_quality_control_permission) {
                    return 500000535; // در انتظار تایید کنترل کیفیت
                } else {
                    return $has_general_item ? 500000430 : 500000410; // در انتظار تکمیل اطلاعات
                }
                break;
            case 500000535:  // در انتظار تایید کنترل کیفیت
                return $has_general_item ? 500000430 : 500000410; // در انتظار تکمیل اطلاعات
                break;

        }

        1 / 0;

    }

    public function sendSmsForExistForm($new_exit_form_status_id, Form $form, Order $order)
    {
        // ارسال پیامک با توجه به وضعیت جدید

        $post_id = 0;
        $template = "";
        switch ($new_exit_form_status_id) {
            case 500000514:  // وصول مطالبات
                $post_id = $this->exit_form_require_demands_permission_post_id;
                $template = "exitformrequiredemandspermission";
                break;
            case 500000515:  // پیش نویس
                $post_id = $this->exit_form_require_draft_permission_post_id;
                $template = "exitformrequiredraftpermission";
                break;
            case 500000520:  // نهایی
                $post_id = $this->exit_form_require_permission_post_id;
                $template = "exitformrequirepermission";
                break;
            case 500000530:   // نگبانی
                $post_id = $this->exit_form_guarding_require_permission_post_id;
                $template = "exitformguardingrequirepermission";
                break;
            case 500000500:   // مشتری
                //پیامک مشتری متفاوت است.
                if($this->send_exit_form_sms) { // اگر اجازه ارسال پیامک به مشتری را دارد
                    $template = "ordersmsexistform";

                    $worker = $this->user;

                    $token = $form->getCode();
                    $token2 = $order->code();
                    $token3 = "_APP_NAME_/DCEF/" . $form->id . "/" . $form->getRandom();
                    $token10 = $this->caption;
                    $token20 = "";

                    Notification::send(
                        "00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
                        new SMSNotification($template, $token, $token2, $token3, $token10, $token20)
                    );
                }
                break;
        }

        $post = Post::
        where("id",$post_id)->
        whereNotIn("id",Post::InvalidPost())->
        first();
        if ($post) {

            $workers = PostUser::getCurrentUserByShiftWorkAndLeaveOvertimeByPostId("worker", $post_id);
            $company_name = Setting::getStringValue("company_name");


            $token = $form->getCode();
            $token2 = $order->code();
            $token3 = "_APP_NAME_/DCEF/" . $form->id . "/" . $form->getRandom();
            $token10 = $post->caption;
            $token20 = $company_name;

            foreach ($workers as $worker) {
                Notification::send(
                    "00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
                    new SMSNotification($template, $token, $token2, $token3, $token10, $token20)
                );
            }
        }

    }

    public static function CallApiAddInputFormForCustomer(Customer $customer, ProductRequestForm $productRequestForm, $exit_form, $transport)
    {

        if (!$customer->software_system_id) {
            return [
                "result" => true,
                "message" => "مشتری سامانه ندارد."
            ];
        }
        $exit_form_list_caption = "";
        if ($exit_form) {
            $next_status_id = $productRequestForm->nextStatusForExistForm($exit_form->status_id);
            $exit_form_list_caption = $exit_form->code;
        } else {
            $t_form = $transport->transport_forms()->first();
            if (!$t_form) {
                return [
                    "result" => true,
                    "message" => "هیچ برگ خروجی در بار با شماره " . $transport->code . " وجود ندارد."
                ];
            }
            foreach ($transport->transport_forms as $t_form_item) {
                $exit_form_list_caption .= $t_form_item->form->code . ", ";
            }
            $next_status_id = $productRequestForm->nextStatusForExistForm($t_form->form->status_id);
        }

        if ($next_status_id != 500000500) { // در انتظار تایید درخواست کننده
            return [
                "result" => true,
                "message" => "نیاز به ثبت نمی باشد."
            ];
        }

        $result_api_login = SoftwareSystem::Login($customer->software_system, $customer->api_url, $customer->api_username, $customer->api_password, $customer->api_key);
        if (!$result_api_login["result"]) {
            return [
                "result" => false,
                "error" => $result_api_login["message"]
            ];
        }

        $message = " ثبت برگ خروج از انبار  " . $exit_form_list_caption . " در " . Setting::getStringValue("software_name");
        $result_api_add_input_form_for_contractor = SoftwareSystem::CallAddInputFormForCustomer(
            $customer->software_system,
            $customer->api_url,
            $result_api_login["token"],
            $productRequestForm,
            $exit_form,
            $transport,
            $message

        );
        if (!$result_api_add_input_form_for_contractor["result"]) {
            SoftwareSystem::Logout(
                $customer->software_system,
                $customer->api_url,
                $result_api_login["token"]
            );
            return [
                "result" => false,
                "error" => "خطای " . $customer->software_system->caption . " در " . $customer->caption . ": <br/>" . $result_api_add_input_form_for_contractor["error"] . "<br/>"
            ];
        }

        // اگر دارای نرم افزار جامع باشد و به خطا نخورده باشد، باید خارج شود.
        SoftwareSystem::Logout(
            $customer->software_system,
            $customer->api_url,
            $result_api_login["token"]
        );

        return [
            "result" => true,
            "message" => "عملیات ایجاد فرم ورود برای مشتری موفقیت آمیز بود."
        ];
    }


}

