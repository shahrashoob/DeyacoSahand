<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\File\File;
use App\Models\Utility\Option;
use App\Models\Utility\Priority;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Utility\Unit\UnitType;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    //
    public function index()
    {


        $api = Setting::getStringValue("api_key");
        if ($api == "") {
            Setting::UpdateApi("api_key");
        }

        $values = Setting::getValues();

        // وضعیت های تایید برگ خروج برای درخواست دهنده ها
        // مشتری
        $customer_exit_form_status_id = Setting::getStringValue("customer_exit_form_status_id");
        $customer_exit_form_option_status = Option::get("status_exit_form", $customer_exit_form_status_id);

        $send_loading_sms_for_customer_in_exit_form_status_id = Setting::getStringValue("send_loading_sms_for_customer_in_exit_form_status_id");
        $send_sms_customer_exit_form_option_status = Option::get("status_exit_form", $send_loading_sms_for_customer_in_exit_form_status_id);

        $loading_post_id_sms_for_customer_exit_form_status_id = Setting::getStringValue("loading_post_id_sms_for_customer_exit_form_status_id");
        $sms_to_post_id_customer_exit_form_option_status = Option::get("posts", $loading_post_id_sms_for_customer_exit_form_status_id, 0, [], -1);


        // پیمانکار
        $contractor_exit_form_status_id = Setting::getStringValue("contractor_exit_form_status_id");
        $contractor_exit_form_option_status = Option::get("status_exit_form", $contractor_exit_form_status_id);

        $send_loading_sms_for_contractor_in_exit_form_status_id = Setting::getStringValue("send_loading_sms_for_contractor_in_exit_form_status_id");
        $send_sms_contractor_exit_form_option_status = Option::get("status_exit_form", $send_loading_sms_for_contractor_in_exit_form_status_id);


        $loading_post_id_sms_for_contractor_exit_form_status_id = Setting::getStringValue("loading_post_id_sms_for_contractor_exit_form_status_id");
        $sms_to_post_id_contractor_exit_form_option_status = Option::get("posts", $loading_post_id_sms_for_contractor_exit_form_status_id, 0, [], -1);


        // ماشین
        $machine_exit_form_status_id = Setting::getStringValue("machine_exit_form_status_id");
        $machine_exit_form_option_status = Option::get("status_exit_form", $machine_exit_form_status_id);


        $send_loading_sms_for_machine_in_exit_form_status_id = Setting::getStringValue("send_loading_sms_for_machine_in_exit_form_status_id");
        $send_sms_machine_exit_form_option_status = Option::get("status_exit_form", $send_loading_sms_for_machine_in_exit_form_status_id);

        $loading_post_id_sms_for_machine_exit_form_status_id = Setting::getStringValue("loading_post_id_sms_for_machine_exit_form_status_id");
        $sms_to_post_id_machine_exit_form_option_status = Option::get("posts", $loading_post_id_sms_for_machine_exit_form_status_id, 0, [], -1);

        // انبارک
        $warehouse_exit_form_status_id = Setting::getStringValue("warehouse_exit_form_status_id");
        $warehouse_exit_form_option_status = Option::get("status_exit_form", $warehouse_exit_form_status_id, 0, [500000535]);


        $send_loading_sms_for_warehouse_in_exit_form_status_id = Setting::getStringValue("send_loading_sms_for_warehouse_in_exit_form_status_id");
        $send_sms_warehouse_exit_form_option_status = Option::get("status_exit_form", $send_loading_sms_for_warehouse_in_exit_form_status_id);

        $loading_post_id_sms_for_warehouse_exit_form_status_id = Setting::getStringValue("loading_post_id_sms_for_warehouse_exit_form_status_id");
        $sms_to_post_id_warehouse_exit_form_option_status = Option::get("posts", $loading_post_id_sms_for_warehouse_exit_form_status_id, 0, [], -1);


        // پیمانکار
        $supplier_exit_form_status_id = Setting::getStringValue("supplier_exit_form_status_id");
        $supplier_exit_form_option_status = Option::get("status_exit_form", $supplier_exit_form_status_id);

        $send_loading_sms_for_supplier_in_exit_form_status_id = Setting::getStringValue("send_loading_sms_for_supplier_in_exit_form_status_id");
        $send_sms_supplier_exit_form_option_status = Option::get("status_exit_form", $send_loading_sms_for_supplier_in_exit_form_status_id);


        $loading_post_id_sms_for_supplier_exit_form_status_id = Setting::getStringValue("loading_post_id_sms_for_supplier_exit_form_status_id");
        $sms_to_post_id_supplier_exit_form_option_status = Option::get("posts", $loading_post_id_sms_for_supplier_exit_form_status_id, 0, [], -1);


        // متفرقه
        $worker_exit_form_status_id = Setting::getStringValue("worker_exit_form_status_id");
        $worker_exit_form_option_status = Option::get("status_exit_form", $worker_exit_form_status_id, 0, [500000535]);

        // پیامک بارگیری غیرفعال است.
//        $send_loading_sms_for_supplier_in_exit_form_status_id = Setting::getStringValue("send_loading_sms_for_supplier_in_exit_form_status_id");
//        $send_sms_supplier_exit_form_option_status = Option::get("status_exit_form", $send_loading_sms_for_supplier_in_exit_form_status_id);
//
//
//        $loading_post_id_sms_for_supplier_exit_form_status_id = Setting::getStringValue("loading_post_id_sms_for_supplier_exit_form_status_id");
//        $sms_to_post_id_supplier_exit_form_option_status = Option::get("posts", $loading_post_id_sms_for_supplier_exit_form_status_id, 0, [], -1);


        //ارسال پیامک تخصیص به پست های سازمانی
        $send_sms_in_create_allocation_machine_to_post_id1 = Setting::getStringValue("send_sms_in_create_allocation_machine_to_post_id1");
        $send_sms_in_create_allocation_machine_to_post_id1_option = Option::get("posts", $send_sms_in_create_allocation_machine_to_post_id1, 0, [], -1);

        $send_sms_in_create_allocation_machine_to_post_id2 = Setting::getStringValue("send_sms_in_create_allocation_machine_to_post_id2");
        $send_sms_in_create_allocation_machine_to_post_id2_option = Option::get("posts", $send_sms_in_create_allocation_machine_to_post_id2, 0, [], -1);


        $company_country_id = Setting::getIntegerValue("company_country_id");
        $company_country_option = Option::get("country", $company_country_id);

        $company_province_id = Setting::getIntegerValue("company_province_id");
        $company_province_option = Option::get("province", $company_province_id);


        //ارسال پیامک تخصیص به پست های سازمانی
        $send_sms_in_quick_change_packing_post_id = Setting::getStringValue("send_sms_in_quick_change_packing_post_id");
        $send_sms_in_quick_change_packing_post_option = Option::get("posts", $send_sms_in_quick_change_packing_post_id, 0, [], -1);


        // پست های مجاز کنترل کیفیت
        $posts_allows_quality_control= Setting::getStringValue("posts_allows_quality_control");
        $posts_allows_quality_control=json_decode($posts_allows_quality_control,true);
        $posts_allows_quality_control_option = Option::get("posts_multi_select", 0,0,$posts_allows_quality_control);


        $office_automation_priority = Priority::where("priority_type_id", 2)->get();
        $values["office_automation_priority_sms"]["value"] = json_decode($values["office_automation_priority_sms"]["string_value"], true);
        // return ($values["office_automation_priority_sms"]["string_value"][202]);
        $tab_name = "utility_setting";
        return view("utility.setting.index", compact(
            "values",
            "customer_exit_form_option_status",
            "contractor_exit_form_option_status",
            "machine_exit_form_option_status",
            "warehouse_exit_form_option_status",
            "supplier_exit_form_option_status",
            "worker_exit_form_option_status",

            "send_sms_customer_exit_form_option_status",
            "sms_to_post_id_customer_exit_form_option_status",

            "send_sms_contractor_exit_form_option_status",
            "sms_to_post_id_contractor_exit_form_option_status",

            "send_sms_machine_exit_form_option_status",
            "sms_to_post_id_machine_exit_form_option_status",

            "send_sms_warehouse_exit_form_option_status",
            "sms_to_post_id_warehouse_exit_form_option_status",

            "send_sms_supplier_exit_form_option_status",
            "sms_to_post_id_supplier_exit_form_option_status",

            "send_sms_in_create_allocation_machine_to_post_id1_option",
            "send_sms_in_create_allocation_machine_to_post_id2_option",

            "send_sms_in_quick_change_packing_post_option",

            "office_automation_priority",
            "tab_name",
            'company_country_option',
            'company_province_option',
            "posts_allows_quality_control_option"
        ));

    }

    public function update(Request $request, $back_url = "")
    {

//return $request->all();
        $setting = Setting::get();
        foreach ($setting as $item) {
            $key = $item->key;
            if ($key == "company_location" && strlen($request->$key) > 80) {
                return back()->withErrors("لوکیشن  شرکت نمی‌تواند بیشتر از 80 کاراکتر باشد.");
            }
            if ($key == "company_address" && strlen($request->$key) > 80) {
                return back()->withErrors("آدرس شرکت نمی‌تواند بیشتر از 80 کاراکتر باشد.");
            }
            switch ($key) {
                case "is_there_a_sales_system":
                    $item->string_value = "";
                    $item->integer_value = 0;
                    $item->double_value = 0;
                    $item->save();
                    if ($request->$key) {

                        try {
                            \DB::connection("mysqlsale")->getPdo();

//                            $exist_users = $sale_users = Worker::on( "mysqlsale" )->
//                            pluck( "national_code" );
//
//                            $data = Customer::join( "users", "users.id", "user_id" )->
//                            whereNotIn( "users.national_code", $sale_users )->
//                            select( "users.national_code", "firstname", "lastname" )->
//                            get()->toArray();
//
//                            Worker::on( "mysqlsale" )->insert( $data );
//                            foreach ($data as $sale_user){
//
//                            }

                            $item->string_value = $request->$key;
                            $item->integer_value = $request->$key;
                            $item->double_value = $request->$key;
                            $item->save();
                        } catch (\Exception $e) {

                            return back()->withErrors(" اتصال به دیتابیس فروش برقرار نمی باشد، لطفا با واحد پشتیبانی تماس بگیرد.
                                         <br/> " . $e->getMessage());
                        }
                    }


                    break;
                case "registering_batch_with_packaging_number_module": // مازول طراحی و تولید کالا با توجه به بچ تعداد بسته بندی
                      $unit_type=UnitType::find(4);
                      $unit_type->active_status_id=  $request->$key ==0 ? 1210:1200;
                      $unit_type->save();
                 
                default:

                    if (isset($request->$key)) {
                        $item->string_value = $request->$key;
                        $item->integer_value = $request->$key;
                        $item->double_value = $request->$key;
                    }
                    $item->save();
                    break;
            }
        }

        if ($back_url) {
            return redirect()->route($back_url)->with(["success" => "تغییرات با موفقیت ذخیره شد."]);

        }

        return redirect()->back()->with(["success" => "تغییرات با موفقیت ذخیره شد."]);
    }

    public function update_logo(Request $request)
    {


        if (isset($request->logo_file)) {
            $result = File::uploadFile($request->file('logo_file'), $request->file_name, 30, "upload/software_logo");
            $public_file_path = 'assets/images/' . $request->file_name;
            $upload_path = $result['upload_path'];

            $public_path = public_path($public_file_path);
            copy(storage_path('app/' . $upload_path), $public_path);

            return back()->with(["success" => "تصویر  با موفقیت آپلود شد."]);
        }

        return back()->withErrors("خطایی پیش آمده است.");
    }

    public function software_lock()
    {


        $api = Setting::getStringValue("api_key");
        if ($api == "") {
            Setting::UpdateApi("api_key");
        }

        $values = Setting::getValues();


        $company_country_id = Setting::getIntegerValue("company_country_id");
        $company_country_option = Option::get("country", $company_country_id);

        $company_province_id = Setting::getIntegerValue("company_province_id");
        $company_province_option = Option::get("province", $company_province_id);


        $tab_name = "lock_setting";
        return view("utility.setting.software_lock", compact(
            "values",

            "tab_name",
            'company_country_option',
            'company_province_option'
        ));

    }

    public function sms_test()
    {
        $list_setting = Setting::whereIn("id", [1, 8, 115, 121])->get()->keyBy("id");
        $send_sms = $list_setting[8]; // is_active_sms_module
        $credit = $list_setting[115]->integer_value; // اعتبار حساب
        $min_of_charge_for_payment = $list_setting[121]->integer_value; // حداقل اعتباری که سامانه اجازه ارسال پیامک دارد

        // حساب اعتبار دارد
        // اگر پیامک فورس است، نیاز نیست اعتبار چک شود.
        if ($credit < $min_of_charge_for_payment && !$this->is_force) {
            return back()->withErrors("اعتبار حساب جهت ارسال پیامک کافی نمی باشد.");
        }
        // اجازه ارسال دارد
        if ($send_sms->integer_value == 0) {
            return back()->withErrors("تنظیمات ارسال پیامک برای همکار غیر فعال است.");
        }
        $worker=Worker::find(1);
         SMSNotification::Test();
         return back()->with(["success"=>"یک پیامک با موفقیت ارسال شد، درصورتی که به گیرنده با شماره "."0".$worker->mobile." نرسیده است، از طریق پنل پیامکی یا مخابرات پیگیری نمایید."]);
    }

}
