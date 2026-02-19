<?php

namespace Database\Seeders\OldSeeder;

use App\Models\LineProduct\GoodsKind;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ButtonSeeder extends Seeder
{
    private $data = [
        //
        ["id" => 120, "name" => "hr.post.edit", "menu_id" => 520, "caption" => 'ویرایش پست ها'],
        ["id" => 125, "name" => "hr.post.add_user", "menu_id" => 520, "caption" => 'تخصیص پست'],
        ["id" => 126, "name" => "hr.post.delete_user", "menu_id" => 520, "caption" => 'عزل پست'],
        ["id" => 127, "name" => "hr.post.list_active", "menu_id" => 520, "caption" => 'لیست پست های فعال'],
        ["id" => 128, "name" => "hr.post.create", "menu_id" => 520, "caption" => 'افزودن پست جدید'],

        ["id" => 150, "name" => "hr.post.list_inactive", "menu_id" => 520, "caption" => 'لیست پست های غیر قعال'],
        ["id" => 151, "name" => "hr.post.digital_setting", "menu_id" => 520, "caption" => 'تغییر در قوانین دیجیتال'],
        ["id" => 152, "name" => "hr.post.smart_object_setting", "menu_id" => 520, "caption" => 'تغییر در اشیاء هوشمند '],
        ["id" => 153, "name" => "hr.post.evaluation_setting", "menu_id" => 520, "caption" => ' فرایند ارزیابی عملکرد '],
        ["id" => 154, "name" => "hr.post.employment.index", "menu_id" => 520, "caption" => ' تنظیمات جذب '],
        ["id" => 155, "name" => "hr.post.chat.index", "menu_id" => 520, "caption" => ' تنظیمات گفتگوی برخط'],
        ["id" => 156, "name" => "hr.post.entry_status_permission.index", "menu_id" => 520, "caption" => " تنظیمات ورود به سامانه "],

        ["id" => 130, "name" => "hr.worker.list", "menu_id" => 510, "caption" => 'مشاهده لیست همه شاغلین'],
        ["id" => 131, "name" => "hr.worker.edit", "menu_id" => 510, "caption" => 'ویرایش شاغلین'],
        ["id" => 132, "name" => "hr.worker.create", "menu_id" => 510, "caption" => 'افزودن شاغل جدید'],
        ["id" => 133, "name" => "hr.worker.show_subset_users", "menu_id" => 510, "caption" => 'مشاهده شاغلین زیر مجموعه'],
        ["id" => 140, "name" => "hr.personal.index", "menu_id" => 510, "caption" => 'مشاهده صفحه پرسنلی'],
        ["id" => 141, "name" => "hr.worker.personal_info.index", "menu_id" => 510, "caption" => 'مشاهده اطلاعات پرونده پرسنلی'],


        ["id" => 2000, "caption" => "  ارسال پیامک گروهی", "name" => "utility.notification.dashboard.create", "menu_id" => 2300],

        ["id" => 2501001, "name" => "guarding.dashboard.show_form", "menu_id" => 2501, "caption" => "ثبت خروج بار (برگ خروج)"],
        ["id" => 2502002, "name" => "guarding.reject_product.confirm_from", "menu_id" => 2502, "caption" => "تایید ورود فرم مرجوعی (نگهبانی)"],
        ["id" => 2501003, "name" => "guarding.dashboard.allow_confirm_input_loading", "menu_id" => 2501, "caption" => "ثبت ورود بار (بارنامه)"],
        ["id" => 2501004, "name" => "guarding.dashboard.allow_confirm_output_loading", "menu_id" => 2501, "caption" => "ثبت خروج بار (بارنامه)"],


        ["id" => 280001, "name" => "utility.transport.loading.show_form", "menu_id" => 280, "caption" => "تایید برگ خروج (بارگیری)"],


        ["id" => 615020, "name" => "sales.304020", "menu_id" => 615, "caption" => "تایید کارشناس فروش "],
        ["id" => 615030, "name" => "sales.304030", "menu_id" => 615, "caption" => "تایید پیش فاکتور مشتری "],
        ["id" => 615040, "name" => "sales.304040", "menu_id" => 615, "caption" => "تایید کارشناس وصول مطالبات "],
        ["id" => 615050, "name" => "sales.304050", "menu_id" => 615, "caption" => "تایید مدیر فروش "],
        ["id" => 615060, "name" => "sales.304060", "menu_id" => 615, "caption" => "تایید مدیر مالی "],
        ["id" => 615070, "name" => "sales.304070", "menu_id" => 615, "caption" => "تایید مدیر عامل "],
        ["id" => 615075, "name" => "sales.304075", "menu_id" => 615, "caption" => "تایید هیئت مدیره "],
        ["id" => 615080, "name" => "sales.304080", "menu_id" => 615, "caption" => "تایید پردازش  "],
        ["id" => 615085, "name" => "sales.will_be_processed_later", "menu_id" => 615, "caption" => "پردازش پس از آماده سازی  "],
        ["id" => 615110, "name" => "sales.exit_permission", "menu_id" => 615, "caption" => "ثبت مجوز خروج"],
        ["id" => 615111, "name" => "sales.35060", "menu_id" => 615, "caption" => "  کنسل کردن سفارش (قبل از آماده سازی)"],
        ["id" => 615115, "name" => "sales.pre_statue", "menu_id" => 615, "caption" => "ارجاع به کارتابل قبل"],
        ["id" => 615120, "name" => "sales.go_to_customer", "menu_id" => 615, "caption" => "ارجاع به کارتابل مشتری"],
        [
            "id" => 615121,
            "name" => "sales.go_to_304020",
            "menu_id" => 615,
            "caption" => "ارجاع به کارتابل کارشناس فروش "
        ],
        ["id" => 615125, "name" => "sales.edit_order", "menu_id" => 615, "caption" => "ویرایش سفارش"],
        ["id" => 615130, "name" => "sales.special_off", "menu_id" => 615, "caption" => "ثبت تخفیف خاص "],
        ["id" => 615140, "name" => "sales.order_to_collection", "menu_id" => 615, "caption" => "صدور دستور جمع آوری"],
        ["id" => 615200, "name" => "sales.register_xml", "menu_id" => 615, "caption" => " ثبت  xml سفارش در نوسا "],

        ["id" => 615310, "name" => "sales.show_order_info", "menu_id" => 615, "caption" => " مشاهده مشخصات سفارش "],
        ["id" => 615315, "name" => "sales.show_order_finance_info", "menu_id" => 615, "caption" => " مشاهده مشخصات مالی "],
        ["id" => 615316, "name" => "sales.show_reject_product_form_list", "menu_id" => 615, "caption" => " مشاهده فرم های مرجوعی "],
        ["id" => 615309, "name" => "sales.reject_product.index", "menu_id" => 615, "caption" => " ثبت فرم مرجوعی برای سفارش "],

        ["id" => 615317, "name" => "sales._exist_form_list", "menu_id" => 615, "caption" => " مشاهده فرم های خروج "],
//        [ "id" => 615318, "name" => "sales._confirm_exist_form", "menu_id" => 615, "caption" => " تایید دریافت محموله (مشتری) " ],
        ["id" => 615319, "name" => "sales._confirm_financial_unit", "menu_id" => 615, "caption" => " تایید نهایی برگ خروج از انبار (واحد مالی) "],
        [
            "id" => 615320,
            "name" => "sales.show_order_factor_products",
            "menu_id" => 615,
            "caption" => " مشاهده فاکتور "
        ], [
            "id" => 615325,
            "name" => "sales.show_production_processing",
            "menu_id" => 615,
            "caption" => " مشاهده پردازش سفارش "
        ], [
            "id" => 615326,
            "name" => "sales.show_correspondence",
            "menu_id" => 615,
            "caption" => " مشاهده مکاتبات با مشتری "
        ],
        [
            "id" => 615330,
            "name" => "sales.show_order_factor_customer_info",
            "menu_id" => 615,
            "caption" => " مشاهده آدرس ارسال "
        ],
        ["id" => 615340, "name" => "sales.register_xml", "menu_id" => 615, "caption" => " ثبت xml کد "],
        [
            "id" => 615350,
            "name" => "sales.show_address_on_index",
            "menu_id" => 615,
            "caption" => " مشاهده ستون آدرس "
        ],
        [
            "id" => 615360,
            "name" => "sales.warehouse_inventory_column",
            "menu_id" => 615,
            "caption" => " مشاهده ستون موجودی انبار "
        ],
        [
            "id" => 615370,
            "name" => "sales.reserve_amount",
            "menu_id" => 615,
            "caption" => " مشاهده ستون مقدار رزور "
        ],
        [
            "id" => 615380,
            "name" => "sales.amount_to_order",
            "menu_id" => 615,
            "caption" => " مشاهده ستون مقدار قابل سفارش "
        ],
        ["id" => 615389, "name" => "sales._confirm_demands_form", "menu_id" => 615, "caption" => " تایید وصول مطالبات برگ خروج از انبار"],
        ["id" => 615390, "name" => "sales._confirm_draft_form", "menu_id" => 615, "caption" => " تایید پیش نویس برگ خروج از انبار (واحد مالی)"],
        ["id" => 615391, "name" => "sales._confirm_customer_form", "menu_id" => 615, "caption" => " تایید برگ خروج از طرف مشتری"],
        ["id" => 615395, "name" => "sales.show_order_consumed_products", "menu_id" => 615, "caption" => "مشاهده مشخصات نوع تامین مواد اولیه"],
        ["id" => 615400, "name" => "sales.view_leads_in_warehouse", "menu_id" => 615, "caption" => "مشاهده جزئیات وزن بار موجود در انبار"],


        ["id" => 615450, "name" => "sales.terminate_order", "menu_id" => 615, "caption" => " خاتمه یافته کردن سفارش (بعد از آماده سازی) "],


        ["id" => 970110, "caption" => "افزودن تعرفه جدید", "name" => "accounting.tariff.add_new", "menu_id" => 970],
        ["id" => 970120, "caption" => "آپلود لیست تعرفه ", "name" => "accounting.tariff.upload_list", "menu_id" => 970],
        ["id" => 970130, "caption" => "ویرایش تعرفه ", "name" => "accounting.tariff.edit_info", "menu_id" => 970],
        ["id" => 970140, "caption" => "مشاهده سابقه تعرفه", "name" => "accounting.tariff.view_log", "menu_id" => 970],


        ["id" => 970200, "name" => "wh.dashboard.out.confirm_qr_form", "menu_id" => 275, "caption" => "تایید تحویل کالا از طریق QR"],
        ["id" => 970210, "name" => "wh.transport.dashboard.index", "menu_id" => 270, "caption" => "ثبت بسته بندی حمل و نقل"],
        ["id" => 970220, "name" => "wh.out.dashboard.show_json_data", "menu_id" => 270, "caption" => "مشاده جزئیات درخواست"],
        ["id" => 970230, "name" => "wh.out.dashboard.confirm_and_checkout", "menu_id" => 270, "caption" => "انتخاب و تحویل کالا"],

        ["id" => 970300, "name" => "wh.dashboard.input.confirm_packing", "menu_id" => 265, "caption" => "تایید ورود کالا"],

        ["id" => 970400, "name" => "wh.production_warehouse.dashboard.confirm_packing", "menu_id" => 285, "caption" => "انتخاب و تحویل کالا"],


        ["id" => 120101, "name" => "report.1009.index", "menu_id" => 120004, "caption" => "گزارش 1009- میزان کالای استخراج شده"],
        ["id" => 120102, "name" => "report.1010.index", "menu_id" => 120004, "caption" => "گزارش 1010- شاخص بهره وری"],
        ["id" => 120103, "name" => "report.1011.index", "menu_id" => 120004, "caption" => "گزارش 1011- میزان کالای تولید شده"],


        // کنترل کیفیت
//        ["id" => 710001, "name" => "quality_control.reject_product.confirm_quality.index", "menu_id" => 710, "caption" => "تایید کنترل کیفیت (حذف)"],
        ["id" => 710002, "name" => "quality_control.reject_product.cheek_quality.index", "menu_id" => 710, "caption" => "بررسی کنترل کیفیت"],
        ["id" => 710003, "name" => "quality_control.reject_product.confirm_sale_expert.confirm_reject_product_form", "menu_id" => 710, "caption" => "بررسی و تایید کارشناس فروش "],

        // بسته بندی
        ["id" => 810001, "name" => "fabric_raw.packing_form.index", "menu_id" => 2125, "caption" => "مشاهده جزئیات مصرف مواد اولیه"],
        ["id" => 810002, "name" => "fabric_raw.packing_form.show_actual_cost", "menu_id" => 2125, "caption" => "مشاهده بهای تمام شده"],

// مدیریت پیمانکاران
        ["id" => 910001, "name" => "contractor._confirm_financial_unit", "menu_id" => 2405, "caption" => " تایید نهایی برگ خروج از انبار (واحد مالی) "],
        ["id" => 910002, "name" => "contractor._confirm_draft_form", "menu_id" => 2405, "caption" => " تایید پیش نویس برگ خروج از انبار (واحد مالی)"],

        // تنظیمات سامانه های مالی
        ["id" => 920002, "name" => "wh.financial_software.transaction", "menu_id" => 220, "caption" => "مدیریت تراکنش های سامانه"],

        ["id" => 920001, "name" => "utility.financial_software.setting.index", "menu_id" => 1070, "caption" => "تنظیمات ثبت تراکنش در انبارها"],
        ["id" => 920003, "name" => "utility.financial_software.definition.edit_api", "menu_id" => 1070, "caption" => "تنظیمات API"],

        // داشبورد مدیریت تولید
        ["id" => 930001, "name" => "production.dashboard.list.show_allocation_data", "menu_id" => 111, "caption" => "مشاهده لاگ تخصیص کارت تولید(مازول تخصیص کارت تولید)"],
        ["id" => 2110002, "name" => "production.machine.index.show_consumption_actual_data", "menu_id" => 2110, "caption" => "مشاهده لاگ محاسبات مصرف واقعی(ماژول برگشت مواد اولیه)"],

        // لیست کالا ها
        ["id" => 932001, "name" => "line_product_station.product.create", "menu_id" => 932, "caption" => "افزودن کالای جدید"],
        ["id" => 932002, "name" => "line_product_station.product.allow_edit_product", "menu_id" => 932, "caption" => "دسترسی جهت ویرایش کالا "],

        // انبار گردانی

        ["id" => 286001, "name" => "wh.warehouse_handling.add_packing_form.index", "menu_id" => 286, "caption" => "افزودن بسته بندی ها"],
        ["id" => 286002, "name" => "wh.warehouse_handling.end_of_handling.index", "menu_id" => 286, "caption" => "پایان انبارگردانی"],
        ["id" => 286003, "name" => "wh.warehouse_handling.confirm_step1.index", "menu_id" => 286, "caption" => "تایید مرحله اول"],
        ["id" => 286004, "name" => "wh.warehouse_handling.confirm_step2.index", "menu_id" => 286, "caption" => "تایید مرحله دوم"],
        ["id" => 286005, "name" => "wh.warehouse_handling.confirm_step3.index", "menu_id" => 286, "caption" => "تایید مرحله سوم"],
        ["id" => 286006, "name" => "wh.warehouse_handling.reject.index", "menu_id" => 286, "caption" => "عدم تایید"],
        ["id" => 286007, "name" => "wh.warehouse_handling.new_handling.index", "menu_id" => 286, "caption" => "ایجاد انبارگردانی جدید"],
        ["id" => 286008, "name" => "wh.warehouse_handling.end_of_review.index", "menu_id" => 286, "caption" => "بررسی وضعیت بسته بندی ها"],


        // انواع حامل ها
        ["id" => 983001, "name" => "line_product_station.carrier.carrier_type.update", "menu_id" => 982, "caption" => "تغییر وضعیت حامل ها"],

        // به دلیل اضافه شدن ic حذف گردید
//        ["id" => 983002, "name" => "line_product_station.carrier.carrier_type.create_packing_type", "menu_id" => 983, "caption" => "افزودن انواع بسته بندی"],
        ["id" => 983003, "name" => "line_product_station.carrier.carrier_type.edit_main_property_carrier_type", "menu_id" => 983, "caption" => "ویرایش انواع بسته بندی (مشخصات اصلی) "],

        //انواع بسته بندی
        ["id" => 995001, "name" => "line_product_station.packing.packing_type.create_packing_type", "menu_id" => 995, "caption" => "افزودن انواع بسته بندی"],
//        ["id" => 995002, "name" => "line_product_station.packing.packing_type.edit_main_property_packing_type", "menu_id" => 995, "caption" => "ویرایش انواع بسته بندی (مشخصات اصلی) "],

        //همکاری با ما
        ["id" => 570001, "name" => "hr.employment.admin.personal.registration_of_selection_result.show_all", "menu_id" => 570, "caption" => "مشاهده همه درخواست های در انتظار انجام"],

        // شارژ حساب
        ["id" => 3200001, "name" => "accounting.client.buy.index", "menu_id" => 3202, "caption" => "مجوز افزایش اعتبار"],



        ["id" => 1040001, "name" => "customer_group.definition.admin.edit", "menu_id" => 1040, "caption" => "ویرایش مشتریان"],
        ["id" => 1040002, "name" => "customer_group.definition.admin.edit_software_system", "menu_id" => 1040, "caption" => "ویرایش سامانه جامع مشتریان"],

        ["id" => 990001, "name" => "contractor.definition.dashboard.edit", "menu_id" => 990, "caption" => "ویرایش پیمانکاران "],
        ["id" => 990002, "name" => "contractor.definition.dashboard.edit_software_system", "menu_id" => 990, "caption" => "ویرایش سامانه جامع پیمانکاران "],

        ["id" => 992001, "name" => "supplier.definition.dashboard.edit", "menu_id" => 992, "caption" => "ویرایش تامین کنندگان "],


        ["id" => 260001, "name" => "wh.warehouse_shelving.dashboard.view_qr", "menu_id" => 260, "caption" => "مشاهده صفحه QR قفسه بندی"],
        ["id" => 260002, "name" => "wh.warehouse_shelving.dashboard.add_packing", "menu_id" => 260, "caption" => " افزودن بسته بندی جدید به قفسه"],



    ];
    private $table = 'buttons';

    public function run()
    {
        DB::table($this->table)->where("id", ">", 0)->update(["caption" => "IncorrectButton"]);
        foreach ($this->data as $item) {
            DB::table($this->table)->where("id", $item["id"])->delete();
            DB::table($this->table)->insert($item);
        }
        DB::table($this->table)->where("caption", "IncorrectButton")->delete();


    }
}
