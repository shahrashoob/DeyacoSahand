<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder {
    private $data = [
        //

       
//  ["id" => 110, "caption" => ' داشبورد  تولید ', "route" => "production.list", "menu_type_id" => 100, "show_in_navbar" => 1],
        [
            "id"             => 115,
            "caption"        => ' داشبورد تولید چله کشی ',
            "route"          => "warps.production_form.dashboard.index",
            "menu_type_id"   => 110,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 120,
            "caption"        => ' داشبورد  ریسندگی ',
            "route"          => "yarn.dashboard.index",
            "menu_type_id"   => 115,
            "show_in_navbar" => 1
        ],
        [ "id" => 121, "caption" => '  دوک گذاری ', "route" => "page1", "menu_type_id" => 115, "show_in_navbar" => 1 ],
        [ "id" => 122, "caption" => '  داف کردن ', "route" => "page21", "menu_type_id" => 115, "show_in_navbar" => 1 ],
//        ["id" => 123, "caption" => ' داشبورد  ریسندگی ', "route" => "yarn.dashboard.index", "menu_type_id" => 115, "show_in_navbar" => 1],
        [
            "id"             => 130,
            "caption"        => ' داشبورد  تکمیل ',
            "route"          => "dashboard",
            "menu_type_id"   => 125,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 111,
            "caption"        => ' داشبورد  مدیریت تولید ',
            "route"          => "production.dashboard.list",
            "menu_type_id"   => 120,
            "show_in_navbar" => 1,
            "query_string"=>"view_form_menu=1"
        ],

        [
            "id"             => 112,
            "caption"        => ' داشبورد  مدیریت تولید با جزئیات ',
            "route"          => "production.dashboard.index_details",
            "menu_type_id"   => 120,
            "show_in_navbar" => 1,
              "query_string"=>"view_form_menu=1"
        ],
        [
            "id"             => 2100,
            "caption"        => 'داشبورد تولید کالا',
            "route"          => "production.production_form.index",
            "menu_type_id"   => 120,
            "show_in_navbar" => 1,
            // "order_number"=>2
        ],
        [
            "id"             => 2110,
            "caption"        => ' لیست ماشین ها',
            "route"          => "production.machine.index",
            "menu_type_id"   => 120,
            "show_in_navbar" => 1,
            //"order_number"=>3
        ],
//        [
//            "id"             => 2120,
//            "caption"        => 'داشبورد طراحی',
//            "route"          => "fabric_raw.design_form.dashboard.index",
//            "menu_type_id"   => 120,
//            "show_in_navbar" => 1,
//            // "order_number"=>4
//        ],
        [
            "id"             => 120001,
            "caption"        => '1005- گزارش وضعیت ماشین آلات',
            "route"          => "report.1005.index",
            "menu_type_id"   => 120,
            "show_in_navbar" => 1,
            // "order_number"=>5
        ],
        [
            "id"             => 120003,
            "caption"        => '1007- گزارش راندمان ماشین آلات',
            "route"          => "report.1007.index",
            "menu_type_id"   => 120,
            "show_in_navbar" => 1,
            // "order_number"=>6
        ],

        [
            "id"             => 120002,
            "caption"        => '1006- گزارش کنترل کیفیت',
            "route"          => "report.1006.index",
            "menu_type_id"   => 120,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 120004,
            "caption"        => 'داشبورد مقطعی مدیریت',
            "route"          => "report.cross_sectional_management.dashboard.index",
            "menu_type_id"   => 1800,
            "show_in_navbar" => 1
        ],

//        [
//            "id"             => 2124,
//            "caption"        => 'پارچه های در انتظار بسته بندی',
//            "route"          => "fabric_raw.packing.fabric_waiting_for_packing.index",
//            "menu_type_id"   => 127,
//            "show_in_navbar" => 1
//        ],
        [
            "id"             => 2125,
            "caption"        => 'داشبورد بسته بندی ',
            "route"          => "fabric_raw.packing_form.index",
            "menu_type_id"   => 127,
            "show_in_navbar" => 1
        ],


//        ["id" => 210, "caption" => '  داشبورد  انبار مواد اولیه ', "route" => "wh.cd.list", "menu_type_id" => 200, "show_in_navbar" => 1],
        [
            "id"             => 211,
            "caption"        => '    انبار مواد اولیه جدید',
            "route"          => "wh.material.list",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 215,
            "caption"        => '  داشبورد  انبار کالا ',
            "route"          => "wh.product.list",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 220,
            "caption"        => 'انتقال به نرم افزار مالی ',
            "route"          => "wh.financial_software.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 240,
            "caption"        => 'داشبورد پالت ها',
            "route"          => "wh.pallet.dashboard.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 245,
            "caption"        => 'فرم ورود به انبار (ویژه دوره پیاده سازی) ',
            "route"          => "wh.input.entry_form.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 246,
            "caption"        => 'فرم خروج از انبار (ویژه دوره پیاده سازی) ',
            "route"          => "wh.out.exit_form_implementation2.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 247,
            "caption"        => 'درخواست خروج از انبار(ویژه دوره پیاده سازی) ',
            "route"          => "wh.out.product_request_form_implementation.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 248,
            "caption"        => '  داشبورد  مجوز بارگیری  ',
            "route"          => "wh.out.download.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],
//        [
//            "id"             => 250,
//            "caption"        => ' لیست فرم های ثبت شده ',
//            "route"          => "wh.entry_form_list",
//            "menu_type_id"   => 200,
//            "show_in_navbar" => 1
//        ],
        [
            "id"             => 255,
            "caption"        => '  افزودن کالاهای موجود (ویژه دوره پیاده سازی) ',
            "route"          => "import.add_existing_products.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 260,
            "caption"        => '  لیست انبار ها ',
            "route"          => "wh.warehouse.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 261,
            "caption"        => 'بسته بندی حمل و نقل (ویژه دوره پیاده سازی)',
            "route"          => "utility.transport.dashboard.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 265,
            "caption"        => '  داشبورد ورود به انبار ',
            "route"          => "wh.dashboard.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 270,
            "caption"        => '  داشبورد خروج <br/> به تفکیک درخواست ',
            "route"          => "wh.out.dashboard.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 271,
            "caption"        => '  داشبورد خروج <br/> به تفکیک مشتریان ',
            "route"          => "wh.out.customer.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 275,
            "caption"        => '  صفحه QR برگ خروج ',
            "route"          => "dashboard",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 280,
            "caption"        => 'داشبورد ارسال بار',
            "route"          => "utility.transport.loading.dashboard.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 285,
            "caption"        => 'داشبورد انبارک های تولید',
            "route"          => "wh.production_warehouse.dashboard.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 286,
            "caption"        => "انبار گردانی",
            "route"          => "wh.warehouse_handling.dashboard.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],


        [
            "id"             => 310,
            "caption"        => '  داشبورد  بازرگانی ',
            "route"          => "purchase.purchase.list",
            "menu_type_id"   => 300,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 315,
            "caption"        => '  داشبورد  نت ',
            "route"          => "line_product_station.maintenance.dashboard.index",
            "menu_type_id"   => 210,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 320,
            "caption"        => '  برگ سفارشات خرید ',
            "route"          => "dashboard",
            "menu_type_id"   => 300,
            "show_in_navbar" => 1
        ],


        [
            "id"             => 410,
            "caption"        => ' داشبورد برنامه ریزی تولید  ',
            "route"          => "dashboard",
            "menu_type_id"   => 400,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 415,
            "caption"        => ' داشبورد برنامه ریزی کالا ',
            "route"          => "utility.planing.product.dashboard.index",
            "menu_type_id"   => 400,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 416,
            "caption"        => ' داشبورد برنامه ریزی کانال تولید ',
            "route"          => "utility.planing.product.production_channel_type.index",
            "menu_type_id"   => 400,
            "show_in_navbar" => 1
        ],
//        [
//            "id"             => 420,
//            "caption"        => ' فراخوانی  ',
//            "route"          => "call.index",
//            "menu_type_id"   => 400,
//            "show_in_navbar" => 1
//        ],
        [
            "id"             => 425,
            "caption"        => ' برگ برنامه ریزی  ',
            "route"          => "utility.planing.index",
            "menu_type_id"   => 400,
            "show_in_navbar" => 1
        ],
//        [
//            "id"             => 426,
//            "caption"        => ' برگ دستور تولید  ',
//            "route"          => "utility.planing.production_order",
//            "menu_type_id"   => 400,
//            "show_in_navbar" => 1
//        ],
        [
            "id"             => 427,
            "caption"        => ' برگ دستور تولید ( ویژه دوره پیاده سازی)  ',
            "route"          => "utility.planing.production_order_demo",
            "menu_type_id"   => 400,
            "show_in_navbar" => 1
        ],


        [
            "id"             => 510,
            "caption"        => ' شاغلین',
            "route"          => "hr.worker.index",
            "menu_type_id"   => 320,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 515,
            "caption"        => 'پرسنلی',
            "route"          => "hr.personal.current_user",
            "menu_type_id"   => 320,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 516,
            "caption"        => 'چارت سازمانی',
            "route"          => "hr.personal.chart.show_chart",
            "menu_type_id"   => 320,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 520,
            "caption"        => '   پست های سازمان',
            "route"          => "hr.post.index",
            "menu_type_id"   => 320,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 530,
            "caption"        => '  شیفت های سازمان',
            "route"          => "hr.shift.index",
            "menu_type_id"   => 320,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 545,
            "caption"        => 'گزارش فعالیت کاربران',
            "route"          => "admin.user-activity",
            "menu_type_id"   => 320,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 550,
            "caption"        => 'کمیته ها',
            "route"          => "hr.definition.committee.index",
            "menu_type_id"   => 320,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 555,
            "caption"        => 'لیست آموزش ها',
            "route"          => "hr.definition.education.education.index",
            "menu_type_id"   => 320,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 560,
            "caption"        => 'لیست گزینش ها',
            "route"          => "hr.definition.selection.selection.index",
            "menu_type_id"   => 320,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 570,
            "caption"        => 'درخواست های همکاری',
            "route"          => "hr.employment.admin.dashboard.index",
            "menu_type_id"   => 2100,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 575,
            "caption"        => 'شاخص های ارزیابی',
            "route"          => "hr.definition.evaluation.evaluation.index",
            "menu_type_id"   => 320,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 576,
            "caption"        => 'فرم های ارزیابی',
            "route"          => "hr.evaluation_form.dashboard.index",
            "menu_type_id"   => 320,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 577,
            "caption"        => 'ثبت درخواست اضافه کاری تجمیعی',
            "route"          => "hr.worker.overtime_together.index",
            "menu_type_id"   => 320,
            "show_in_navbar" => 1
        ],

        [
            
            "id"             => 614,
            "caption"        => 'ثبت ارسال بار(ویژه دوره پیاده سازی) ',
            "route"          => "sales.loading_implementation.index",
            "menu_type_id"   => 310,
            "show_in_navbar" => 1

        ],
        
        [
            "id"             => 615,
            "caption"        => '  داشبورد  فروش  ',
            "route"          => "sales.dashboard.index",
            "menu_type_id"   => 310,
            "show_in_navbar" => 1
        ],
//        [
//            "id"             => 611,
//            "caption"        => '  داشبورد وصول مطالبات ',
//            "route"          => "sales.receipt_of_receivables.list",
//            "menu_type_id"   => 310,
//            "show_in_navbar" => 1
//        ],

        [
            "id"             => 1025,
            "caption"        => 'ثبت سفارش برای مشتری ',
            "route"          => "sales.customer.index",
            "menu_type_id"   => 310,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 710,
            "caption"        => 'داشبورد کنترل کیفیت',
            "route"          => "quality_control.dashboard.index",
            "menu_type_id"   => 410,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 810,
            "caption"        => 'داشبورد  پایانه بار',
            "route"          => "dashboard",
            "menu_type_id"   => 420,
            "show_in_navbar" => 1
        ],


        [
            "id"             => 910,
            "caption"        => ' آپلود اطلاعات پایه و انبارش کالا ',
            "route"          => "import.product.base_and_storage.index",
            "menu_type_id"   => 910,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 913,
            "caption"        => ' آپلود اطلاعات تولید کالا ',
            "route"          => "import.product.production.index",
            "menu_type_id"   => 910,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 915,
            "caption"        => ' آپلود اطلاعات خرید کالا ',
            "route"          => "import.product.purchase.index",
            "menu_type_id"   => 910,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 917,
            "caption"        => ' آپلود اطلاعات مشخصات کالا ',
            "route"          => "import.product.property.index",
            "menu_type_id"   => 910,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 919,
            "caption"        => ' آپلود قیمت بروز مواد اولیه ',
            "route"          => "import.product.pricing.index",
            "menu_type_id"   => 910,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 921,
            "caption"        => ' آپلود فایل پایه سنوات تجمیعی ',
            "route"          => "dashboard",
            "menu_type_id"   => 910,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 922,
            "caption"        => ' آپلود فایل مانده مرخصی ',
            "route"          => "import.leave_reminder.index",
            "menu_type_id"   => 910,
            "show_in_navbar" => 1
        ],
//        ["id" => 920, "caption" => 'آپلود لیست خط های تولید', "route" => "import.line.index", "menu_type_id" => 900, "show_in_navbar" => 1],
        [
            "id"             => 930,
            "caption"        => 'آپلود لیست خط - محصول - ایستگاه',
            "route"          => "import.line_product.index",
            "menu_type_id"   => 910,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 940,
            "caption"        => 'آپلود BOM',
            "route"          => "import.product.bom.index",
            "menu_type_id"   => 910,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 935,
            "caption"        => 'آپلود همبافت',
            "route"          => "import.product.lot_number.index",
            "menu_type_id"   => 910,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 932,
            "caption"        => ' لیست کالا ها ',
            "route"          => "line_product_station.product.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 934,
            "caption"        => ' رسته های کالا',
            "route"          => "line_product_station.goods_kind.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 960,
            "caption"        => 'آپلود لیست کارکنان',
            "route"          => "import.worker.index",
            "menu_type_id"   => 910,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 970,
            "caption"        => 'لیست تعرفه ها',
            "route"          => "accounting.tariff.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 975,
            "caption"        => ' تخفیف های درصدی',
            "route"          => "accounting.offer.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 976,
            "caption"        => 'مانده حساب های مشتری',
            "route"          => "import.account_balance.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
//        ["id" => 977, "caption" => 'ایستگاه های کاری', "route" => "line_product_station.station.index", "menu_type_id" => 900, "show_in_navbar" => 1],
        [
            "id"             => 978,
            "caption"        => 'خط های تولید',
            "route"          => "line_product_station.line.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],[
            "id"             => 979,
            "caption"        => 'کانال های تولید',
            "route"          => "line_product_station.production_channel_type.definition.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 982,
            "caption"        => ' حامل ها ',
            "route"          => "line_product_station.carrier.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 983,
            "caption"        => ' انواع حامل ها ',
            "route"          => "line_product_station.carrier.carrier_type.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 984,
            "caption"        => ' انواع خودرو ها ',
            "route"          => "utility.car.car_type.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 987,
            "caption"        => ' مخزن های کالا',
            "route"          => "line_product_station.reservoir.definition.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 990,
            "caption"        => 'پیمانکاران',
            "route"          => "contractor.definition.dashboard.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 992,
            "caption"        => 'تامین کنندگان',
            "route"          => "supplier.definition.dashboard.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 995,
            "caption"        => 'انواع بسته بندی',
            "route"          => "line_product_station.packing.packing_type.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 1040,
            "caption"        => 'لیست مشتریان ',
            "route"          => "customer_group.definition.admin.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 1050,
            "caption"        => 'پرینتر ها',
            "route"          => "utility.printer.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 1060,
            "caption"        => 'درخت حساب ها',
            "route"          => "accounting.definition.account.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 1070,
            "caption"        => ' نرم افزارهای مالی ',
            "route"          => "utility.financial_software.definition.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 1080,
            "caption"        => 'انواع مجوزها',
            "route"          => "utility.special_license.definition.dashboard.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 1095,
            "caption"        => 'لیست شرکت ها',
            "route"          => "dashboard",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 1090,
            "caption"        => 'عضویت بارکدی',
            "route"          => "utility.other.barcode_link.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 980,
            "caption"        => 'مراکز هزینه',
            "route"          => "accounting.definition.cost_center.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 90001,
            "caption"        => 'الگوهای عملیات مالی',
            "route"          => "accounting.definition.financial_operation_pattern.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 985,
            "caption"        => ' نقص های کالا',
            "route"          => "line_product_station.product.fault.product_fault.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 986,
            "caption"        => ' نقص های ماشین',
            "route"          => "line_product_station.machine.fault.machine_fault.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 988,
            "caption"        => 'لیست قرادادها',
            "route"          => "accounting.contract.contract.index",
            "menu_type_id"   => 900,
            "show_in_navbar" => 1
        ],



        [ "id" => 1010, "caption" => 'ثبت تامین (ویژه دوره پیاده سازی) ', "route" => "supplier.admin.supplier_register.index", "menu_type_id" => 430, "show_in_navbar" => 1 ],
        [ "id" => 1015, "caption" => 'مدیریت تامین کنندگان ', "route" => "supplier.admin.dashboard.index", "menu_type_id" => 430, "show_in_navbar" => 1 ],
        [ "id" => 1016, "caption" => 'تعریف الگوریتم بارکد تامین کنندگان', "route" => "page23", "menu_type_id" => 430, "show_in_navbar" => 1 ],
        [ "id" => 1017, "caption" => 'مدیریت کارت های تامین ( کالای امانی)', "route" => "supplier.trust_product.dashboard.index", "menu_type_id" => 430, "show_in_navbar" => 1 ],


        [
            "id"             => 1020,
            "caption"        => 'ثبت سفارش جدید ',
            "route"          => "customer_group.buy.new_order",
            "menu_type_id"   => 1100,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 1030,
            "caption"        => 'داشبورد مشتریان ',
            "route"          => "customer_group.order.index",
            "menu_type_id"   => 1100,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 1045,
            "caption"        => 'درخواست طراحی کالا  ',
            "route"          => "customer_group.tmp.product_creation.index",
            "menu_type_id"   => 1100,
            "show_in_navbar" => 1
        ],


        [
            "id"             => 1200,
            "caption"        => 'تنظیمات اولیه',
            "route"          => "utility.setting.index",
            "menu_type_id"   => 550,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 1205,
            "caption"        => 'تنظیمات فروش',
            "route"          => "sales.setting.index",
            "menu_type_id"   => 550,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 1206,
            "caption"        => '  تنظیمات قفل نرم افزار',
            "route"          => "utility.setting.software_lock",
            "menu_type_id"   => 550,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 1209,
            "caption"        => 'تنظیمات منابع انسانی',
            "route"          => "hr.setting.dashboard.index",
            "menu_type_id"   => 550,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 1199,
            "caption"        => 'تنظیمات داشبورد لحظه ای',
            "route"          => "report.real_time.setting.index",
            "menu_type_id"   => 550,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 1210,
            "caption"        => 'راهنمای وب سرویس ها',
            "route"          => "utility.help.api.index",
            "menu_type_id"   => 550,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 1211,
            "caption"        => 'راهنمای وب سرویس ها',
            "route"          => "dashboard",
            "menu_type_id"   => 550,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 2001,
            "caption"        => '1001- در انتظار تولید   ',
            "route"          => "report.1001.index",
            "menu_type_id"   => 1000,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 2002,
            "caption"        => '1002- کارت های ثبت شده',
            "route"          => "report.1002.index",
            "menu_type_id"   => 1000,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 2003,
            "caption"        => 'گزارش 1003-  گردش کالا',
            "route"          => "report.1003.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 2004,
            "caption"        => 'گزارش 1004- كاردكس کالا ',
            "route"          => "report.1004.index",
            "menu_type_id"   => 200,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 2005,
            "caption"        => 'گزارش 1012 - وضعیت های مهم بسته بندی',
            "route"          => "report.1012.index",
            "menu_type_id"   => 1000,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 2006,
            "caption"        => 'گزارش 1014 - گزارش حضور و غیاب پرسنل',
            "route"          => "report.1014.index",
            "menu_type_id"   => 1000,
            "show_in_navbar" => 1
        ],


        [
            "id"             => 2200,
            "caption"        => 'درخواست تیکت جدید',
            "route"          => "utility.ticket.create",
            "menu_type_id"   => 1300,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 2300,
            "caption"        => 'اطلاع رسانی',
            "route"          => "utility.notification.dashboard.index",
            "menu_type_id"   => 330,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 2310,
            "caption"        => 'اطلاع رسانی (Pup up)',
            "route"          => "utility.pup_up.admin.index",
            "menu_type_id"   => 330,
            "show_in_navbar" => 1
        ],


        [
            "id"             => 2400,
            "caption"        => 'داشبورد پیمانکاران',
            "route"          => "contractor.panel.dashboard.index",
            "menu_type_id"   => 1400,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 2405,
            "caption"        => 'مدیریت پیمانکاران',
            "route"          => "contractor.admin.dashboard.index",
            "menu_type_id"   => 1400,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 2410,
            "caption"        => 'گزارش پیمانکاران ',
            "route"          => "contractor.report.report_1.index",
            "menu_type_id"   => 1400,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 2501,
            "caption"        => 'ورود / خروج کالا ',
            "route"          => "guarding.dashboard.index",
            "menu_type_id"   => 1500,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 2502,
            "caption"        => 'درخواست های مرجوعی',
            "route"          => "guarding.reject_product.index",
            "menu_type_id"   => 1500,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 2601,
            "caption"        => 'میز کار من',
            "route"          => "utility.office_automation.dashboard.index",
            "menu_type_id"   => 1600,
            "show_in_navbar" => 1
        ],


        [
            "id"             => 2701,
            "caption"        => '  اشیاء هوشمند',
            "route"          => "utility.smart_object.index",
            "menu_type_id"   => 1700,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 2710,
            "caption"        => 'دستیارهای هوشمند',
            "route"          => "utility.script.index",
            "menu_type_id"   => 1700,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 2810,
            "caption"        => 'داشبورد لحظه ای',
            "route"          => "report.real_time.dashboard.index",
            "menu_type_id"   => 1800,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 2910,
            "caption"        => 'در انتظار تایید',
            "route"          => "utility.special_license.panel.dashboard.index",
            "menu_type_id"   => 1900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 2915,
            "caption"        => 'مجوزهای من',
            "route"          => "utility.special_license.panel.dashboard.my_license",
            "menu_type_id"   => 1900,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 2916,
            "caption"        => 'لیست همه مجوزها',
            "route"          => "utility.special_license.admin.dashboard.index",
            "menu_type_id"   => 1900,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 2101,
            "caption"        => ' لیست درخواست ها ',
            "route"          => "line_product_station.product.product_creation.dashboard.index",
            "menu_type_id"   => 2000,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 3101,
            "caption"        => 'چت سازمانی',
            "route"          => "hr.chat.index",
            "menu_type_id"   => 3100,
            "show_in_navbar" => 1
        ],

        [
            "id"             => 3202,
            "caption"        => ' تراکنش های فاکتور نشده',
            "route"          => "accounting.client.transaction.index",
            "menu_type_id"   => 3200,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 3203,
            "caption"        => 'صورت حساب',
            "route"          => "accounting.client.factor.index",
            "menu_type_id"   => 3200,
            "show_in_navbar" => 1
        ],
        [
            "id"             => 3301,
            "caption"        => 'لیست کالای های فروشگاه',
            "route"          => "accounting.store.dashboard.index",
            "menu_type_id"   => 3300,
            "show_in_navbar" => 1
        ],


        [
            "id"             => 3401,
            "caption"        => 'درخواست بروزرسانی',
            "route"          => "utility.update.dashboard.index",
            "menu_type_id"   => 3400,
            "show_in_navbar" => 1
        ],


        [
            "id"             => 3501,
            "caption"        => 'کیف پول تارا',
            "route"          => "accounting.welfare_service.tara.client.dashboard.index",
            "menu_type_id"   => 3500,
            "show_in_navbar" => 1
        ],


        [
            "id"             => 3502,
            "caption"        => 'ثبت نام پرسنل',
            "route"          => "accounting.welfare_service.tara.admin.dashboard.index",
            "menu_type_id"   => 3500,
            "show_in_navbar" => 1
        ],


    ];
    private $table = 'menus';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
                DB::table( $this->table )->insert( $item );
        }
    }
}
