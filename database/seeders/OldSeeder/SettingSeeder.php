<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $data = [
        //
        ["id" => 1, "key" => 'company_name', "caption" => "نام شرکت", "string_value" => "شرکت جدید"],
        ["id" => 2, "key" => 'software_name', "caption" => "نام سامانه", "string_value" => "سامانه جامع ERP "],
        ["id" => 3, "key" => 'industrial_type_id', "caption" => "نوع صنعت", "string_value" => 1],
        ["id" => 4, "key" => 'version', "caption" => "ورژن", "string_value" => 1],
        [
            "id" => 5,
            "key" => 'delivery_to_warehouse_without_register_lot_number_is_allowed',
            "caption" => "  تحویل کالا به انبار بدون ثبت کد لات نرم افزار مالی ",
            "integer_value" => 1
        ],
        [
            "id" => 6,
            "key" => 'delivery_to_warehouse_without_register_lot_number_text',
            "caption" => "  توضیحات تحویل کالا به انبار بدون ثبت کد لات نرم افزار مالی ",
            "string_value" => " جهت دریافت کد نوسا با شماره ********-035 داخلی *** تماس بگیرید."
        ],
        [
            "id" => 7,
            "key" => 'max_of_post_for_user',
            "caption" => "حداکثر تعداد پست که می توان به یک فرد تخصیص داد ",
            "integer_value" => 1
        ],
        [
            "id" => 8,
            "key" => 'is_active_sms_module',
            "caption" => "وضعیت ارسال پیامک برای همکاران",
            "integer_value" => 0
        ],
        ["id" => 9, "key" => 'unlimited_number', "caption" => "مقدار نامحدود", "integer_value" => 1000000000],
        ["id" => 10, "key" => 'show_product_image', "caption" => "نمایش تصویر کالا ", "integer_value" => 0],
        [
            "id" => 11,
            "key" => 'effective_inventory_in_order',
            "caption" => "آیا موجودی انبار در حداکثر سفارش مشتری تاثیر گذار باشد؟ ",
            "integer_value" => 0
        ],
//        [ "id"            => 14,
//          "key"           => 'fabric_raw_checked_initial_shrinkage_percent',
//          "caption"       => "آیا  درستی درصد جمع شدگی اولیه چک شود؟ (داشبورد تولید بافندگی) ",
//          "integer_value" => 0
//        ],
//        [ "id"            => 15,
//          "key"           => 'fabric_raw_checked_second_shrinkage_percent',
//          "caption"       => "آیا  درستی درصد جمع شدگی ثانویه چک شود؟ (داشبورد تولید بافندگی) ",
//          "integer_value" => 0
//        ],
//        [ "id"            => 16,
//          "key"           => 'fabric_raw_checked_general_shrinkage_percent',
//          "caption"       => "آیا  درستی درصد جمع شدگی کلی چک شود؟ (داشبورد تولید بافندگی) ",
//          "integer_value" => 0
//        ],
        [
            "id" => 17,
            "key" => 'is_there_a_sales_system',
            "caption" => "آیا  سامانه فروش مجزا برای سیستم تعریف شده است؟",
            "integer_value" => 0
        ],
        [
            "id" => 18,
            "key" => 'has_grading_and_control',
            "caption" => "آیا  کارخانه دارای سیستم درجه بندی و کنترل می باشد؟",
            "integer_value" => 0
        ],
        [
            "id" => 19,
            "key" => 'check_contour_with_time',
            "caption" => "بررسی اینکه  کنتور وارد شده با توجه به زمان معتبر است؟",
            "integer_value" => 1
        ],
//        [
//            "id"            => 20,
//            "key"           => 'days_for_calculate_allocation_type',
//            "caption"       => "تعداد روز جهت محاسبه میانگین زمان تولید در ماژول محاسبه زمان تخصیص",
//            "integer_value" => 30
//        ],
        [
            "id" => 21,
            "key" => 'warps_remaining_checked',
            "caption" => "آیا بیشتر بودن متراژ چله از متراژ اولیه در زمان استخراج چله چک شود؟",
            "integer_value" => 1
        ],
        ["id" => 22, "key" => 'static_ip', "caption" => "IP ثابت", "string_value" => ""],
        [
            "id" => 23,
            "key" => 'diff_of_production_and_allocation_in_the_end_of_production',
            "caption" => "درصد مجاز  کمتر بودن اختلاف کالای تولید شده با مقدار تخصیص کارت تولید/دستور پیمان در زمان پایان تولید",
            "integer_value" => "95"
        ],
        [
            "id" => 24,
            "key" => 'transport_loading_header_text',
            "caption" => "متن بارگیری (بسته بندی ویژه ارسال بار)",
            "string_value" => ""
        ],
        ["id" => 25, "key" => 'national_code', "caption" => "شناسه ملی شرکت", "string_value" => ""],
        ["id" => 26, "key" => 'economic_number', "caption" => "شماره اقتصادی شرکت", "string_value" => ""],
        ["id" => 27, "key" => 'text_footer_per_factor', "caption" => "توضیحات پیش فاکتور ها", "string_value" => ""],
        [
            "id" => 28,
            "key" => 'show_efficiency_to_operator',
            "caption" => "آیا شاخص عملکرد به اپراتور مسئول نمایش داده شود؟",
            "string_value" => "0"
        ],
        ["id" => 29, "key" => 'financial_year_start', "caption" => "آغاز سال مالی", "string_value" => "3/21"],
        ["id" => 30, "key" => 'financial_year_end', "caption" => "پایان سال مالی", "string_value" => "3/21"],
        [
            "id" => 31,
            "key" => 'minutes_before_leaving_the_organization_employee_can_they_announce_readiness',
            "caption" => "شاغلین حداکثر چند دقیقه قبل از خروج از سازمان، می توانند اعلام آمادگی کنند؟(دقیقه) ",
            "integer_value" => "15"
        ],

        ["id" => 32, "key" => 'hr_sing_in_sms', "caption" => "ارسال پیامک ورود به سازمان ", "integer_value" => "1"],
        ["id" => 33, "key" => 'hr_sign_out_sms', "caption" => "ارسال پیامک خروج از سازمان ", "integer_value" => "1"],
        [
            "id" => 34,
            "key" => 'hr_leave_confirm_replace_sms',
            "caption" => "ارسال پیامک تایید جانشینی برای مرخصی ",
            "integer_value" => "1"
        ],
        [
            "id" => 35,
            "key" => 'hr_leave_conform_parent_sms',
            "caption" => "ارسال پیامک تایید مافوق برای مرخصی ",
            "integer_value" => "1"
        ],
        [
            "id" => 36,
            "key" => 'hr_leave_set_comment_replace_sms',
            "caption" => "ارسال پیامک ثبت توضیحات برای درخواست دهنده مرخصی/اضافه کاری/ماموریت",
            "integer_value" => "1"
        ],
        [
            "id" => 37,
            "key" => 'hr_overtime_conform_parent_sms',
            "caption" => "ارسال پیامک تایید مافوق برای اضافه کاری ",
            "integer_value" => "1"
        ],
        [
            "id" => 38,
            "key" => 'hr_mission_conform_parent_sms',
            "caption" => "ارسال پیامک تایید مافوق برای ماموریت ",
            "integer_value" => "1"
        ],

        [
            "id" => 39,
            "key" => 'send_sms_in_create_production',
            "caption" => "ارسال پیامک صدور کارت تولید/دستور پیمان به پست های ناظر سامانه ",
            "integer_value" => "1"
        ],
        [
            "id" => 40,
            "key" => 'sale_formal_status_list',
            "caption" => "فقط فاکتور هایی که سفارش آنها در وضعیت های زیر است، برای فیلد  مجموع سفارش های رسمی|غیر رسمی  در نظر گرفته می شوند.",
            "string_value" => "[0]"
        ],

        [
            "id" => 41,
            "key" => 'product_creation_unit_address',
            "caption" => "آدرس واحد طراحی کالا",
            "string_value" => "آدرس واحد طراحی کالای شرکت"
        ],
        [
            "id" => 42,
            "key" => 'product_creation_unit_phone',
            "caption" => "تلفن تماس واحد طراحی کالا",
            "string_value" => "تلفن تماس واحد طراحی کالای شرکت"
        ],

        //تنظیمات برگ خروج

        [
            "id" => 43,
            "key" => 'customer_exit_form_status_id',
            "caption" => "ثبت تراکنش برگ خروج از انبار برای مشتریان در رویداد زیر ثبت شود",
            "string_value" => "500000520"
        ],
        [
            "id" => 44,
            "key" => 'contractor_exit_form_status_id',
            "caption" => "ثبت تراکنش برگ خروج از انبار برای پیمانکاران در رویداد زیر ثبت شود",
            "string_value" => "500000500"
        ],
        [
            "id" => 45,
            "key" => 'machine_exit_form_status_id',
            "caption" => "ثبت تراکنش برگ خروج از انبار برای ماشین ها در رویداد زیر ثبت شود",
            "string_value" => "500000500"
        ],

        [
            "id" => 46,
            "key" => 'send_loading_sms_for_customer_in_exit_form_status_id',
            "caption" => "پیامک بارگیری برای مشتریان در رویداد زیر ارسال شود",
            "string_value" => "500000520"
        ],
        [
            "id" => 47,
            "key" => 'send_loading_sms_for_contractor_in_exit_form_status_id',
            "caption" => "پیامک بارگیری برای پیمانکاران در رویداد زیر ارسال شود",
            "string_value" => "500000520"
        ],
        [
            "id" => 48,
            "key" => 'send_loading_sms_for_machine_in_exit_form_status_id',
            "caption" => "پیامک بارگیری برای ماشین ها در رویداد زیر ارسال شود",
            "string_value" => "500000520"
        ],


        [
            "id" => 49,
            "key" => 'loading_post_id_sms_for_customer_exit_form_status_id',
            "caption" => "پیامک بارگیری برای مشتریان به پست زیر ارسال شود",
            "string_value" => "0"
        ],
        [
            "id" => 50,
            "key" => 'loading_post_id_sms_for_contractor_exit_form_status_id',
            "caption" => "پیامک بارگیری برای پیمانکاران به پست زیر ارسال شود",
            "string_value" => "0"
        ],
        [
            "id" => 51,
            "key" => 'loading_post_id_sms_for_machine_exit_form_status_id',
            "caption" => "پیامک بارگیری برای ماشین ها به پست زیر ارسال شود",
            "string_value" => "0"
        ],
        //**************
        [
            "id" => 52,
            "key" => 'reject_product_description',
            "caption" => "توضیحات برای مشتری که قصد مرجوع کردن کالاها را دارد",
            "string_value" => "لطفا از سالم بودن بسته بندی هایی که تیک دارد، اطمینان داشته باشید."
        ],
        [
            "id" => 53,
            "key" => 'percent_allow_to_reject_packing_form',
            "caption" => "برای مرجوع کردن کالا حداقل چند درصد از بسته بندی باید باقی مانده باشد",
            "integer_value" => "70"
        ],
        [
            "id" => 54,
            "key" => 'cheek_allocation_amount_with_production_amount',
            "caption" => "بررسی اینکه مقدار تخصیص داده شده به ماشین، تولید شده است یا خیر",
            "integer_value" => 1
        ],
        [
            "id" => 55,
            "key" => 'reject_product_in_send_product',
            "caption" => "توضیحات برای مشتری در زمان ارسال کالای مرجوعی",
            "string_value" => "لطفا فایل مرجوعی را از لینک زیر دانلود و پرینت نمایید و به انضمام کالا ها به کارخانه مرجوع نمایید."
        ],

        // درصد جمع شدگی
        [
            "id" => 56,
            "key" => 'min_shrinkage_percent',
            "caption" => "حداقل درصد جمع شدگی اولیه قابل قبول ",
            "double_value" => "-5"
        ],
        [
            "id" => 57,
            "key" => 'max_shrinkage_percent',
            "caption" => "حداکثر درصد جمع شدگی اولیه قابل قبول ",
            "double_value" => "10"
        ],

        [
            "id" => 58,
            "key" => 'send_sms_in_create_allocation_machine_to_post_id1',
            "caption" => "ارسال پیامک تخصیص کارت تولید به پست های سازمانی (پست 1) ",
            "integer_value" => "0"
        ],

        [
            "id" => 59,
            "key" => 'send_sms_in_create_allocation_machine_to_post_id2',
            "caption" => "ارسال پیامک تخصیص کارت تولید به پست های سازمانی (پست 2) ",
            "integer_value" => "0"
        ],

## اتوماسیون اداری

        [
            "id" => 60,
            "key" => 'office_automation_max_file_size_in_mb',
            "caption" => "حداکثر مقدار قابل قبول برای آپلود فایل ",
            "integer_value" => "5"
        ],
        [
            "id" => 61,
            "key" => 'office_automation_priority_sms',
            "caption" => "وضعیت  ارسال پیامک های میزکار (اتوماسیون اداری) ",
            "string_value" => "{}"
        ],
        [
            "id" => 63,
            "key" => 'company_name_en',
            "caption" => "نام انگلیسی شرکت",
            "string_value" => "English Company Name"
        ],

        ################## برگ خروج انبارک

        [
            "id" => 64,
            "key" => 'warehouse_exit_form_status_id',
            "caption" => "ثبت تراکنش برگ خروج از انبار برای انبارک در رویداد زیر ثبت شود",
            "string_value" => "500000535"
        ],

        [
            "id" => 65,
            "key" => 'send_loading_sms_for_warehouse_in_exit_form_status_id',
            "caption" => "پیامک بارگیری برای انبارک در رویداد زیر ارسال شود",
            "string_value" => "0"
        ],

        [
            "id" => 66,
            "key" => 'loading_post_id_sms_for_warehouse_exit_form_status_id',
            "caption" => "پیامک بارگیری برای انبارک ها به پست زیر ارسال شود",
            "string_value" => "0"
        ],

        [
            "id" => 67,
            "key" => 'smart_object_server_ip',
            "caption" => "آدرس سرور اشیاء هوشمند",
            "string_value" => "192.168.180.190"
        ],
        [
            "id" => 68,
            "key" => 'smart_object_server_port',
            "caption" => "پورت سرور اشیاء هوشمند",
            "string_value" => "5000"
        ],
        [
            "id" => 69,
            "key" => 'max_diff_of_production_and_allocation_in_the_end_of_production',
            "caption" => "درصد مجاز  بیشتر بودن اختلاف کالای تولید شده با مقدار تخصیص کارت تولید/دستور پیمان در زمان پایان تولید",
            "integer_value" => "10"
        ],

        [
            "id" => 70,
            "key" => 'hr_replacement_confirm_parent_sms',
            "caption" => "ارسال پیامک تایید مافوق برای جابجایی شیفت ",
            "integer_value" => "1"
        ],
        [
            "id" => 71,
            "key" => 'hr_replacement_confirm_replace_sms',
            "caption" => "ارسال پیامک تایید جانشینی برای جابجایی شیفت ",
            "integer_value" => "1"
        ],

        [
            "id" => 72,
            "key" => 'qr_one_time_token_time',
            "caption" => "زمان اعتبار بارکد یکبار مصرف (ثانیه) ",
            "integer_value" => "300"
        ],


        [
            "id" => 73,
            "key" => 'legal_working_hours_in_minute',
            "caption" => "ساعت کار قانونی در روز (دقیقه) - حذف",
            "integer_value" => "440"
        ],
        [
            "id" => 74,
            "key" => 'round_off_for_total_price_in_sale',
            "caption" => " تخفیف رند کردن مبلغ کل تا چند رقم اعشار اعمال شود (1: یک رقم اعشار، 0: همه اعشار، -1: یکان، -2: دهگان و ...)",
            "integer_value" => "6"
        ],

        [
            "id" => 75,
            "key" => 'repetitive_passing',
            "caption" => "زمان تشخیص تردد تکراری (ثانیه)",
            "integer_value" => "60"
        ],


        [
            "id" => 76,
            "key" => 'sale_series_type_1',
            "caption" => "سری فاکتور فروش های رسمی",
            "integer_value" => "1"
        ],
        [
            "id" => 77,
            "key" => 'sale_series_type_2',
            "caption" => "سری فاکتور فروش های غیررسمی",
            "integer_value" => "1"
        ],
        [
            "id" => 78,
            "key" => 'max_time_for_supplier_to_register_factor_info',
            "caption" => "حداکثر زمان جهت ثبت فاکتور برای تامین کنندگان(روز)",
            "integer_value" => "20"
        ],

        [
            "id" => 79,
            "key" => 'send_loading_sms_for_supplier_in_exit_form_status_id',
            "caption" => "پیامک بارگیری برای تامین کنندگان(تحویل امانی - قرض) در رویداد زیر ارسال شود",
            "string_value" => "500000520"
        ],

        [
            "id" => 80,
            "key" => 'loading_post_id_sms_for_supplier_exit_form_status_id',
            "caption" => "پیامک بارگیری برای تامین کنندگان (تحویل امانی - قرض) به پست زیر ارسال شود",
            "string_value" => "0"
        ],

        [
            "id" => 81,
            "key" => 'supplier_exit_form_status_id',
            "caption" => "ثبت تراکنش برگ خروج از انبار برای تامین کنندگان (تحویل امانی - قرض) در رویداد زیر ثبت شود",
            "string_value" => "500000500"
        ],


        [
            "id" => 82,
            "key" => 'origin_country_id',
            "caption" => " محل اجرای پروژه - کشور مبدا ",
            "integer_value" => 112
        ],

        [
            "id" => 83,
            "key" => 'company_address',
            "caption" => "آدرس شرکت",
            "string_value" => "آدرس شرکت"
        ],
        [
            "id" => 84,
            "key" => 'company_location',
            "caption" => "لینک لوکیشن شرکت",
            "string_value" => "لینک لوکیشن شرکت"
        ],
        [
            "id" => 85,
            "key" => 'supplier_default_setting',
            "caption" => "مشخصات پیش فرض تامین کننده ها",
            "string_value" => ""
        ],
        [
            "id" => 86,
            "key" => 'supplier_draft_contract_required_init_confirm',
            "caption" => "آیا پیش نویس قرارداد هوشمند برای تامین کنندگان نیاز به تایید اولیه دارد؟",
            "integer_value" => 0
        ],
        [
            "id" => 87,
            "key" => 'supplier_draft_contract_post_ids_for_init_confirm',
            "caption" => "کد پست سازمانی جهت تایید اولیه قراداد هوشمند تامین کنندگان",
            "string_value" => ""
        ],
        [
            "id" => 88,
            "key" => 'supplier_draft_contract_required_final_confirm',
            "caption" => "آیا پیش نویس قرارداد هوشمند برای تامین کنندگان نیاز به تایید نهایی دارد؟",
            "integer_value" => 0
        ],
        [
            "id" => 89,
            "key" => 'supplier_draft_contract_post_ids_for_final_confirm',
            "caption" => "کد پست سازمانی جهت تایید نهایی قراداد هوشمند تامین کنندگان",
            "string_value" => ""
        ],
        [
            "id" => 90,
            "key" => 'company_have_separate_financial_software',
            "caption" => "آیا شرکت نرم افزار مالی مجزا دارد؟",
            "integer_value" => 1
        ],
        [
            "id" => 91,
            "key" => 'post_id_for_contract',
            "caption" => "کد پست سازمانی جهت عقد قرارداد ",
            "integer_value" => 1001
        ],
        [
            "id" => 92,
            "key" => 'name_of_work_medicine_doctor',
            "caption" => "نام دکتر طب کار",
            "string_value" => "نام دکتر طب کار"
        ],
        [
            "id" => 93,
            "key" => 'address_of_work_medicine_doctor',
            "caption" => "نشانی طب کار",
            "string_value" => "نشانی طب کار"
        ],
        [
            "id" => 94,
            "key" => 'customer_default_setting',
            "caption" => "مشخصات پیش فرض مشتری ها",
            "string_value" => ""
        ],
        [
            "id" => 95,
            "key" => 'app_debug',
            "caption" => "آیا خطای برنامه نویسی به کاربر نمایش داده شود؟",
            "integer_value" => 1
        ],
        [
            "id" => 96,
            "key" => 'get_the_supplier_image',
            "caption" => "آیا دریافت تصویر تامین کنندگان الزامی است؟",
            "integer_value" => 0
        ],
        [
            "id" => 97,
            "key" => 'company_have_separate_warehousing_software',
            "caption" => "آیا شرکت نرم افزار انبارداری مجزا دارد؟",
            "integer_value" => 1
        ],

        [
            "id" => 98,
            "key" => 'api_key',
            "caption" => "API Key",
            "string_value" => ""
        ],
        [
            "id" => 99,
            "key" => 'customer_draft_contract_required_init_confirm',
            "caption" => "آیا پیش نویس قرارداد هوشمند برای مشتریان نیاز به تایید اولیه دارد؟",
            "integer_value" => 1
        ],
        [
            "id" => 100,
            "key" => 'customer_draft_contract_post_ids_for_init_confirm',
            "caption" => "پست سازمانی جهت تایید اولیه قراداد هوشمند مشتریان",
            "string_value" => ""
        ],
        [
            "id" => 101,
            "key" => 'customer_draft_contract_required_final_confirm',
            "caption" => "آیا پیش نویس قرارداد هوشمند برای مشتریان نیاز به تایید نهایی دارد؟",
            "integer_value" => 1
        ],
        [
            "id" => 102,
            "key" => 'customer_draft_contract_post_ids_for_final_confirm',
            "caption" => " پست سازمانی جهت تایید نهایی قراداد هوشمند مشتریان",
            "string_value" => ""
        ],
        [
            "id" => 103,
            "key" => 'get_the_customer_image',
            "caption" => "آیا دریافت تصویر مشتریان الزامی است؟",
            "integer_value" => 0
        ],
        [
            "id" => 104,
            "key" => 'customer_draft_contract_confirm',
            "caption" => "آیا برای مشتریان نیاز به تایید قراداد می باشد؟",
            "integer_value" => 1
        ],
        [
            "id" => 105,
            "key" => 'supplier_draft_contract_confirm',
            "caption" => "آیا برای تامین کنندگان نیاز به تایید قراداد می باشد؟",
            "integer_value" => 1
        ],
        [
            "id" => 106,
            "key" => 'contractor_default_setting',
            "caption" => "مشخصات پیش فرض پیمانکاران",
            "string_value" => ""
        ],
        [
            "id" => 107,
            "key" => 'contractor_draft_contract_required_init_confirm',
            "caption" => "آیا پیش نویس قرارداد هوشمند برای پیمانکاران نیاز به تایید اولیه دارد؟",
            "integer_value" => 1
        ],
        [
            "id" => 108,
            "key" => 'contractor_draft_contract_post_ids_for_init_confirm',
            "caption" => "پست سازمانی جهت تایید اولیه قراداد هوشمند پیمانکاران",
            "string_value" => ""
        ],
        [
            "id" => 109,
            "key" => 'contractor_draft_contract_required_final_confirm',
            "caption" => "آیا پیش نویس قرارداد هوشمند برای پیمانکاران نیاز به تایید نهایی دارد؟",
            "integer_value" => 1
        ],
        [
            "id" => 110,
            "key" => 'contractor_draft_contract_post_ids_for_final_confirm',
            "caption" => " پست سازمانی جهت تایید نهایی قراداد هوشمند پیمانکاران",
            "string_value" => ""
        ],
        [
            "id" => 111,
            "key" => 'get_the_contractor_image',
            "caption" => "آیا دریافت تصویر پیمانکاران الزامی است؟",
            "integer_value" => 0
        ],
        [
            "id" => 112,
            "key" => 'contractor_draft_contract_confirm',
            "caption" => "آیا برای پیمانکاران نیاز به تایید قراداد می باشد؟",
            "integer_value" => 1
        ],
        [
            "id" => 113,
            "key" => 'how_months_should_reward_calculated_in_salary',
            "caption" => "هر چند ماه یکبار عیدی و سنوات در فیش حقوقی محاسبه گردد؟",
            "string_value" => ""
        ],
        [
            "id" => 114,
            "key" => 'min_of_charge',
            "caption" => "حداقل مبلغ شارژ حساب (ریال)",
            "integer_value" => 5000000
        ],
        [
            "id" => 115,
            "key" => 'credit',
            "caption" => "اعتبار حساب (ریال)",
            "integer_value" => 0
        ],
        [
            "id" => 116,
            "key" => 'is_allow_to_edit_database_info',
            "caption" => " آیا  امکان ویرایش اطلاعات منظومه داده امکان پذیر است؟",
            "integer_value" => 0
        ],
        ["id" => 117, "key" => 'a_in_ax_b_of_sms_cost', "caption" => "ضریب هزینه ارسال پیامک ", "double_value" => 1],
        ["id" => 118, "key" => 'b_in_ax_b_of_sms_cost', "caption" => "هزینه ثابت هر پیامک (ریال) ", "integer_value" => 0],
        [ "id" => 119, "key" => 'min_of_charge_for_reminder', "caption" => "حداقل اعتباری که به مشتری پیامک افزایش اعتبار ارسال می شود (ریال) ", "integer_value" => 500000 ],
        [ "id" => 120, "key" => 'min_of_charge_for_send_sms', "caption" => "حداقل اعتباری که سامانه اجازه ارسال پیامک دارد (ریال) ", "integer_value" => 50000 ],
        [ "id" => 121, "key" => 'min_of_charge_for_payment', "caption" => "حداقل اعتباری که باید افزایش اعتبار انجام شود (ریال) ", "integer_value" => 0 ],

        ["id" => 122, "key" => 'company_country_id', "caption" => "کشور محل شرکت", "integer_value" =>112],
        ["id" => 123, "key" => 'company_province_id', "caption" => "استان  محل شرکت", "integer_value" => ""],
        ["id" => 124, "key" => 'company_city_name', "caption" => "شهر محل شرکت","string_value" => ""],
        ["id" => 125, "key" => 'company_phone_number', "caption" => "تلفن محل شرکت", "string_value" => ""],
        ["id" => 126, "key" => 'company_postal_code', "caption" => "کد پستی  محل شرکت", "string_value" => ""],
        ["id" => 127, "key" => 'deduction_of_thousand_rials', "caption" => "کسر هزار ریال", "integer_value" => 0],

        ["id" => 128, "key" => 'opening_text_of_office_automation', "caption" => "متن سلام نامه ی رسمی","string_value" => ""],
        ["id" => 129, "key" => 'thanks_text_of_office_automation', "caption" => "متن تشکر نامه رسمی","string_value" => ""],
        ["id" => 130, "key" => 'office_automation_size_types_id', "caption" => "سایز نامه ی رسمی", "integer_value" => 1],
        ["id" => 131, "key" => 'top_margins_in_office_automation', "caption" => " حاشیه از بالا در نامه رسمی", "integer_value" => 2],
        ["id" => 132, "key" => 'bottom_margins_in_office_automation', "caption" => "حاشیه از پایین در نامه رسمی", "integer_value" => 2],
        ["id" => 133, "key" => 'right_margins_in_office_automation', "caption" => "حاشیه از راست در نامه رسمی", "integer_value" => 2],
        ["id" => 134, "key" => 'left_margins_in_office_automation', "caption" => " حاشیه از چپ در نامه رسمی", "integer_value" => 2],


        ["id" => 135, "key" => 'receive_the_consumer_price_in_the_pricing', "caption" => "آیا در قیمت گذاری قیمت مصرف کننده دریافت گردد", "integer_value" => 0],

        ["id" => 136, "key" => 'max_show_packing_form_code_in_special_license', "caption" => "حداکثر تعداد مجاز نمایش بارکد بسته بندی های خوانده نشده در مجوز 1014", "integer_value" => 3],

        ["id" => 137, "key" => 'tax_calculation_percentage', "caption" => "درصد محاسبه مالیات", "integer_value" => 10],

        ["id" => 138, "key" => 'max_time_allowed_for_percent_in_company', "caption" => "حداکثر مدت زمان حضور در سازمان (ساعت) ", "integer_value" => 16],

        ["id" => 139, "key" => 'request_update_at', "caption" => "زمان و ساعت درخواست بروز رسانی (ویژه دوره پیاده سازی)", "string_value" => ""],
        ["id" => 140, "key" => 'has_active_contract', "caption" => "آیا شرکت با دیاکو قرارداد فعال دارد ؟", "integer_value" => 1],

        ["id" => 141, "key" => 'max_day_for_special_license_15', "caption" => "حداکثر زمان مرجوع نمودن کالا پس از تایید مجوز مرجوعی توسط مشتریان (روز)", "integer_value" => 7],

        [
            "id" => 142,
            "key" => 'company_have_it_unit',
            "caption" => "آیا شرکت دارای واحد آیتی می باشد؟",
            "integer_value" => 0
        ],
        [
            "id" => 143,
            "key" => 'transport_item_label_type',
            "caption" => "نوع چاپ برچسب بسته بندی حمل و نقل (1: برچسب 9*13 2: 6*9",
            "integer_value" => 1
        ],
        [
            "id" => 144,
            "key" => 'order_loading_status_id',
            "caption" => "برای ارسال سفارش های مشتری، نیاز به مجوز بارگیری می باشد؟ (بله: 460000100 - خیر: 460000200)",
            "integer_value" => 460000100
        ],
        [
            "id" => 145,
            "key" => 'classified_absence_from_regular_working_hours',
            "caption" => "آیا غیبت داخلی از ساعت کار عادی تفکیک شود؟ (این شامل غیبت قانونی نمی باشد و در هر صورت فرد باید ساعت کار قانونی را تکمیل نماید) ",
            "integer_value" => 0
        ],
        [
            "id" => 146,
            "key" => 'support_end_date',
            "caption" => "تاریخ پایان پشتیبانی (مثال: 2025/01/01)",
            "string_value" => "2025/01/01"
        ],
        [
            "id" => 147,
            "key" => 'legal_leave_in_month',
            "caption" => "مرخصی قانونی در ماه (دقیقه)",
            "integer_value" => "953" // 26 روز کاری به جز جمعه ها
        ],
        [
            "id" => 148,
            "key" => 'template_entry_sign_in',
            "caption" => "قالب پیامک ورود ( پیامک عادی:hrentrysignin - پیامک کوتاه: hrentrysignin2) ",
            "string_value" => "hrentrysignin"
        ],
        [
            "id" => 149,
            "key" => 'template_entry_sign_out',
            "caption" => "قالب پیامک خروج  ( پیامک عادی:hrentrysignout - پیامک کوتاه: hrentrysignout2)",
            "string_value" => "hrentrysignout"
        ],
        [
            "id" => 150,
            "key" => 'checking_product_change_in_entry',
            "caption" => "چک کردن و هشدار تغییر کالا در داشبورد ورود به انبار",
            "integer_value" => "0"
        ],

        [
            "id" => 151,
            "key" => 'max_of_charge',
            "caption" => "حداکثر مبلغ شارژ حساب (ریال)",
            "integer_value" => 1000000000
        ],


        [
            "id" => 152,
            "key" => 'worker_exit_form_status_id',
            "caption" => "ثبت تراکنش برگ خروج از انبار برای خروج متفرقه(ویژه دوره پیاده سازی) در رویداد زیر ثبت شود",
            "string_value" => "500000535"
        ],


        [
            "id" => 158,
            "key" => 'auto_exit_option_algorithm',
            "caption" => "الگوریتم هوشمند ثبت خروج برای پرسنل در صورت عدم ثبت خروج",
            "integer_value" => 3
        ],


        [
            "id" => 159,
            "key" => 'registering_batch_with_packaging_number_module',
            "caption" => "مازول طراحی و تولید کالا با توجه به بچ تعداد بسته بندی ",
            "integer_value" => 0
        ],


        [
            "id" => 160,
            "key" => 'check_iso_form_posts_permission',
            "caption" => "آیا شرط ایزو جهت دسترسی به پست های سازمانی بررسی شود؟",
            "integer_value" => 1
        ],
        [
            "id" => 161,
            "key" => 'posts_allows_quality_control',
            "caption" => "پست های سازمانی که می توانند کار کنترل کیفیت را انجام دهند",
            "string_value" => "[]"
        ],
        [
            "id" => 162,
            "key" => 'sale_planing_status_list',
            "caption" => "فقط فاکتور هایی که سفارش آنها در وضعیت های زیر است، برای برنامه ریزی تولید در زمان ثبت سفارش در نظر گرفته می شوند.",
            "string_value" => "[-1]"
        ],
        [
            "id" => 163,
            "key" => 'property1_show_in_production_dashboard',
            "caption" => "آیا ستون مشخصات اصلی (رسته کالایی) در داشبود مدیریت تولید نمایش داد شود؟",
            "integer_value" => "0"
        ],
        [
            "id" => 164,
            "key" => 'property2_show_in_production_dashboard',
            "caption" => "آیا ستون مشخصات فرعی (رسته کالایی) در داشبود مدیریت تولید نمایش داد شود؟",
            "integer_value" => "0"
        ],
        [
            "id" => 165,
            "key" => 'dashboard_type_of_machines',
            "caption" => "نوع داشبورد ماشین ها ", // ( 0 - پیش فرض و 1 - داشبورد ستونی نوع 1)
            "integer_value" => "0"
        ],

        [
            "id" => 166,
            "key" => 'type_of_pass_RefN_and_SalesKindCode',
            "caption" => "روش پاس دادن متغیر نوع فروش ( 1- فقط نوع فروش رسمی و غیر رسمی 2-دسته بندی فروش در منتغیر *** و نوع فروش در متغیر  RefN ", // ( 0 - پیش فرض و 1 - داشبورد ستونی نوع 1)
            "integer_value" => "1"
        ],


        [
            "id" => 167,
            "key" => 'does_sales_need_to_register_a_loading_permit_form',
            "caption" => "آیا انبار جهت خروج کالا، نیاز به ثبت فرم مجوز بارگیری توسط فروش دارد؟", // ( 0 - پیش فرض و 1 - داشبورد ستونی نوع 1)
            "integer_value" => "0"
        ],

        [
            "id" => 168,
            "key" => 'does_sales_view_inventory_on_the_way',
            "caption" => "آیا واحد فروش بتواند موجودی در راه را (در صفحه ثبت مجوز بارگیری) ببیند؟",
            "integer_value" => "0"
        ],

        [
            "id" => 169,
            "key" => 'does_sales_set_permission_for_inventory_on_the_way',
            "caption" => "آیا واحد فروش بتواند براساس موجودی در راه  (در صفحه ثبت مجوز بارگیری) مجوز صادر کند؟",
            "integer_value" => "0"
        ],

        [
            "id" => 170,
            "key" => 'method_of_calculating_active_inventory_on_sales',
            "caption" => "روش محاسبه موجودی فعال  (در صفحه ثبت مجوز بارگیری) چگونه است؟ ", //0: مجودی کل 1: موجودی بسته بندی های مجاز
            "integer_value" => "0"
        ],

        [
            "id" => 171,
            "key" =>"type_of_status_id_after_confirm_product_request_form_in_permission",
            "caption" => 'وضعیت درخواست های خروج از انبار از نوع مجوز بارگیری (دستور واحد فروش)، پس از تایید برگ خروج  ', // 0: محاسبه توسط سیستم 1: تحویل شده
            "integer_value" => "0"
        ],

        [
            "id" => 172,
            "key" =>"show_product_caption_in_pre_factor",
            "caption" => 'آیا نام کالا در پیش فاکتور نمایش داده شود؟',
            "integer_value" => "1"
        ],

        [
            "id" => 173,
            "key" =>"show_packing_type_caption_in_pre_factor",
            "caption" => 'آیا عنوان نوع بسته بندی در پیش فاکتور نمایش داده شود؟',
            "integer_value" => "1"
        ],

        [
            "id" => 174,
            "key" =>"show_packing_type_code_in_pre_factor",
            "caption" => 'آیا کد نوع بسته بندی در پیش فاکتور نمایش داده شود؟',
            "integer_value" => "1"
        ],

        [
            "id" => 175,
            "key" =>"show_property_1_in_pre_factor",
            "caption" => 'آیا مشخصه اول کالا (با توجه به رسته کالایی) در پیش فاکتور نمایش داده شود؟',
            "integer_value" => "0"
        ],

        [
            "id" => 176,
            "key" =>"show_property_2_in_pre_factor",
            "caption" => 'آیا مشخصه دوم کالا (با توجه به رسته کالایی) در پیش فاکتور نمایش داده شود؟',
            "integer_value" => "0"
        ],

        [
            "id" => 177,
            "key" =>"show_property_3_in_pre_factor",
            "caption" => 'آیا مشخصه سوم کالا (با توجه به رسته کالایی) در پیش فاکتور نمایش داده شود؟',
            "integer_value" => "0"
        ],

        [
            "id" => 178,
            "key" =>"send_order_sms_for_customers",
            "caption" => 'آیا به صورت پیش فرض پیامک های ثبت سفارش  برای مشتری ارسال شود؟',
            "integer_value" => "0"
        ],

        [
            "id" => 179,
            "key" =>"send_exit_form_sms_for_customers",
            "caption" => 'آیا به صورت پیش فرض پیامک های  برگ خروج برای مشتری ارسال شود؟',
            "integer_value" => "0"
        ],

        [
            "id" => 180,
            "key" =>"send_register_sms_for_customers",
            "caption" => 'آیا به صورت پیش فرض پیامک های  ثبت نام برای مشتری ارسال شود؟',
            "integer_value" => "0"
        ],

        [
            "id" => 181,
            "key" => 'production_channel_type_show_in_production_dashboard',
            "caption" => "آیا نام و رنگ کانال تولید  در داشبود مدیریت تولید نمایش داد شود؟",
            "integer_value" => "0"
        ],



        [
            "id" => 182,
            "key" => 'real_time_show_report_bar',
            "caption" => " - حذف آیا نمودار سفارشات نمایش داده شود؟",
            "integer_value" => "0"
        ],

        [
            "id" => 183,
            "key" => 'real_time_top_order_delay_show',
            "caption" => "آیا لیست n سفارشی که بیشترین مدت زمان تعویق را دارد، نمایش داده شود؟",
            "integer_value" => "0"
        ],

        [
            "id" => 184,
            "key" => 'real_time_top_order_delay_number',
            "caption" => "چه تعداد  سفارشی که بیشترین مدت زمان تعویق را دارد، در گزارش نمایش داده شود؟",
            "integer_value" => "10"
        ],

        [
            "id" => 185,
            "key" => 'real_time_top_customer_number',
            "caption" => "چه تعداد  مشتریان برتر، در گزارش نمایش داده شود؟"."<br/>"."مشتریان برتر: مشتریانی که در x روز گذشته بیشترین حجم سفارش را داشته اند.",
            "integer_value" => "10"
        ],

        [
            "id" => 186,
            "key" => 'real_time_top_customer_days',
            "caption" => "تعداد روزهای بررسی سفارشات مشتریان برتر",
            "integer_value" => "30"
        ],

        [
            "id" => 187,
            "key" => 'real_time_top_customer_show',
            "caption" => "نمایش لیست مشتریان برتر در داشبورد لحظه ای",
            "integer_value" => "1"
        ],

        [
            "id" => 188,
            "key" => 'allow_show_bar_weigh_in_real_time_dashboard',
            "caption" => "مقدار سفارش ها و کالای تولید شده - تن",
            "integer_value" => "1"
        ],

        [
            "id" => 189,
            "key" => 'allow_show_bar_price_in_real_time_dashboard',
            "caption" => "مقدار سفارش ها و کالای تولید شده - میلیارد ریال",
            "integer_value" => "1"
        ],

        [
            "id" => 190,
            "key" => 'allow_show_line_weight_in_real_time_dashboard',
            "caption" => "روند فروش وزنی - تن",
            "integer_value" => "1"
        ],

        [
            "id" => 191,
            "key" => 'allow_show_line_price_in_real_time_dashboard',
            "caption" => " روند فروش - میلیارد ریال",
            "integer_value" => "1"
        ],


        [
            "id" => 192,
            "key" => 'send_sms_in_quick_change_packing_post_id',
            "caption" => " پست جهت ارسال پیامک تغییر بسته بندی سریع در انبار ",
            "integer_value" => "0"
        ],


        [
            "id" => 193,
            "key" => 'default_shipping_method_id',
            "caption" => " روش ارسال بار (پیش فرض) ",
            "integer_value" => "1"
        ],

        [
            "id" => 194,
            "key" => 'default_delivery_point_type_id',
            "caption" => " محل تحویل بار (پیش فرض) ",
            "integer_value" => "1"
        ],

        [
            "id" => 195,
            "key" => 'default_delivery_point_type_id',
            "caption" => " محل تحویل بار (پیش فرض) ",
            "integer_value" => "1"
        ],

        [
            "id" => 196,
            "key" => 'allow_show_polar_in_real_time_dashboard',
            "caption" => " نمودار قطبی ",
            "integer_value" => "1"
        ],

        [
            "id" => 197,
            "key" => 'allow_get_shipping_method_in_buy',
            "caption" => " آیا در زمان ثبت سفارش، مشخصات ارسال بار (بیمه، نوع خودرو، روش ارسال بار و ...) دریافت شود؟",
            "integer_value" => "0"
        ],

        [
            "id" => 198,
            "key" => 'allow_get_shipping_method_in_product_permission',
            "caption" => " آیا در زمان ثبت مجوز خروج، مشخصات ارسال بار (بیمه، نوع خودرو، روش ارسال بار و ...) دریافت شود؟",
            "integer_value" => "0"
        ],

        [
            "id" => 199,
            "key" => 'allow_show_customer_caption_in_production_dashboard',
            "caption" => "آیا نام مشتری در داشبورد تولید نمایش داده شود؟",
            "integer_value" => "0"
        ],


        [
            "id" => 200,
            "key" => 'check_material_flow_in_machine_allocation',
            "caption" => "آیا در زمان تخصیص یک به یک بودن گراف جریان همبافتی چک شود؟",
            "integer_value" => "1"
        ],



    ];
    private $table = "settings";

    public function run()
    {

        foreach ($this->data as $item) {

            if (!DB::table($this->table)->where(["key" => $item["key"]])->exists()) {
                DB::table($this->table)->insert($item);
            } else {
                DB::table($this->table)->where("id", $item["id"])->update([
                    "caption" => $item["caption"]
                ]);
            }
        }
    }
}
