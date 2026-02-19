<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [

        // فرم تولید بافندگی
        [ "id" => 7002001, "caption" => 'ایجاد فرم تولید', "status_type_id" => 7002 ],
//        [ "id" => 7002002, "caption" => 'ثبت درجه بندی', "status_type_id" => 7002 ],
//        [ "id" => 7002003, "caption" => 'پایان درجه بندی', "status_type_id" => 7002 ],
//        [ "id" => 7002004, "caption" => 'ثبت حامل بسته بندی', "status_type_id" => 7002 ],
        [ "id" => 7002005, "caption" => 'استخراج پارچه', "status_type_id" => 7002 ],
//        [ "id" => 7002006, "caption" => 'تغییر وضعیت', "status_type_id" => 7002 ],
//        [ "id" => 7002007, "caption" => 'پایان ثبت حامل', "status_type_id" => 7002 ],
//        [ "id" => 7002008, "caption" => 'برش از شانه (ایجاد فرم) ', "status_type_id" => 7002 ],
//        [ "id" => 7002009, "caption" => 'برش از شانه (استخراج فرم)', "status_type_id" => 7002 ],
//        [ "id" => 7002010, "caption" => 'بروزرسانی مقدار همبافت ها', "status_type_id" => 7002 ],
//        [ "id" => 7002011, "caption" => 'بروزرسانی کنتور(های) اولین همبافت', "status_type_id" => 7002 ],
//        [ "id" => 7002012, "caption" => 'بروزرسانی کنتور(های) آخرین همبافت', "status_type_id" => 7002 ],
//        [ "id" => 7002013, "caption" => 'حذف درجه بندی', "status_type_id" => 7002 ],
        [ "id" => 7002014, "caption" => 'خاتمه یافته شدن فرم توسط سیستم', "status_type_id" => 7002 ],
        [ "id" => 7002015, "caption" => 'پایان تغییر کالیته', "status_type_id" => 7002 ],
        [ "id" => 7002016, "caption" => 'پایان بافت کارت تولید', "status_type_id" => 7002 ],
        [ "id" => 7002017, "caption" => 'استخراج فرم تولید', "status_type_id" => 7002 ],
        [ "id" => 7002018, "caption" => 'استخراج بخشی از فرم تولید', "status_type_id" => 7002 ],
        [ "id" => 7002019, "caption" => 'عدم تغییر کالیته', "status_type_id" => 7002 ],
        [ "id" => 7002020, "caption" => 'بروز رسانی مقدار فرم تولید', "status_type_id" => 7002 ],
        [ "id" => 7002025, "caption" => 'جابجایی تخصیص', "status_type_id" => 7002 ],

        // فرم تولید چله کشی
        [ "id" => 7202001, "caption" => 'ایجاد فرم تولید', "status_type_id" => 7202 ],
        [ "id" => 7202002, "caption" => 'پایان برگردان', "status_type_id" => 7202 ],
        // فرم تولید عملیات عمومی
        [ "id" => 7302001, "caption" => 'تزریق مواد اولیه (ساختاری)', "status_type_id" => 7302 ],

        //
        [ "id" => 7301003, "caption" => 'شروع عملیات', "status_type_id" => 7301 ],
        [ "id" => 7301004, "caption" => 'پایان عملیات', "status_type_id" => 7301 ],

        // فرم بسته بندی
        [ "id" => 7007001, "caption" => "ایجاد  بسته بندی", "status_type_id" => 7007 ],
        [ "id" => 7007002, "caption" => "تغییر حامل بسته بندی", "status_type_id" => 7007 ],
        [ "id" => 7007003, "caption" => "حذف آیتم بسته بندی", "status_type_id" => 7007 ],
        [ "id" => 7007004, "caption" => "حذف فرم بسته بندی", "status_type_id" => 7007 ],
        [ "id" => 7007005, "caption" => "تکمیل و تحویل به انبار", "status_type_id" => 7007 ],
        [ "id" => 7007006, "caption" => "تایید ورود به انبار", "status_type_id" => 7007 ],
        [ "id" => 7007007, "caption" => "عدم تایید انبار", "status_type_id" => 7007 ],
        [ "id" => 7007008, "caption" => "تغییر بسته بندی", "status_type_id" => 7007 ],
        [ "id" => 7007009, "caption" => "ثبت نهایی تولید", "status_type_id" => 7007 ],
        [ "id" => 7007010, "caption" => "تایید تراکنش خروج از انبار", "status_type_id" => 7007 ],
        [ "id" => 7007011, "caption" => " تغییر بسته بندی (داخل انبار)", "status_type_id" => 7007 ],
        [ "id" => 7007012, "caption" => "دریافت محموله", "status_type_id" => 7007 ],
        [ "id" => 7007013, "caption" => "ثبت مرجوعی", "status_type_id" => 7007 ],
        [ "id" => 7007014, "caption" => "ورود محموله به کارخانه", "status_type_id" => 7007 ],
        [ "id" => 7007015, "caption" => "استخراج بسته بندی (های) فرعی", "status_type_id" => 7007 ],
        [ "id" => 7007016, "caption" => "تغییر بسته بندی اصلی", "status_type_id" => 7007 ],
        [ "id" => 7007017, "caption" => "ثبت مصرف برای ماشین", "status_type_id" => 7007 ],
        [ "id" => 7007018, "caption" => "ثبت مصرف برای ماشین (به اندازه موجودی)", "status_type_id" => 7007 ],
        [ "id" => 7007019, "caption" => "ثبت تراکنش اصلاحی (ورود)", "status_type_id" => 7007 ],
        [ "id" => 7007020, "caption" => "ثبت تراکنش اصلاحی (خروج)", "status_type_id" => 7007 ],
        [ "id" => 7007021, "caption" => "ثبت تراکنش تغییر درجه (ورود)", "status_type_id" => 7007 ],
        [ "id" => 7007022, "caption" => "ثبت تراکنش  تغییر درجه (خروج)", "status_type_id" => 7007 ],
        [ "id" => 7007023, "caption" => "حذف بسته بندی با ادغام", "status_type_id" => 7007 ],
        [ "id" => 7007024, "caption" => "اضافه شدن آیتم با ادغام بسته بندی", "status_type_id" => 7007 ],
        [ "id" => 7007025, "caption" => "ثبت تراکنش ضایعات مواد اولیه (ورود)", "status_type_id" => 7007 ],
        [ "id" => 7007026, "caption" => "ثبت تراکنش  ضایعات مواد اولیه (خروج)", "status_type_id" => 7007 ],
        [ "id" => 7007027, "caption" => "تکمیل اطلاعات بسته بندی", "status_type_id" => 7007 ],
//        [ "id" => 7007028, "caption" => "انتقال انبارک به انبارک", "status_type_id" => 7007 ],
        [ "id" => 7007029, "caption" => "کسری انبارگردانی", "status_type_id" => 7007 ],
        [ "id" => 7007030, "caption" => "مازاد انبارگردانی", "status_type_id" => 7007 ],
        [ "id" => 7007031, "caption" => "ثبت مجوز", "status_type_id" => 7007 ],
        [ "id" => 7007032, "caption" => "رد مجوز", "status_type_id" => 7007 ],
        [ "id" => 7007033, "caption" => "ثبت نهایی کالای امانی", "status_type_id" => 7007 ],
        [ "id" => 7007034, "caption" => "ثبت در سامانه مشتری/پیمانکار", "status_type_id" => 7007 ],
        [ "id" => 7007035, "caption" => "ثبت ناموفق در سامانه مشتری/پیمانکار", "status_type_id" => 7007 ],
        [ "id" => 7007036, "caption" => "تزریق به مخزن", "status_type_id" => 7007 ],
        [ "id" => 7007037, "caption" => "کنترل کیفیت", "status_type_id" => 7007 ],
        [ "id" => 7007038, "caption" => "تغییر نوع بسته بندی", "status_type_id" => 7007 ],
        [ "id" => 7007039, "caption" => "ثبت شماره درخواست کالا از انبار", "status_type_id" => 7007 ],
        [ "id" => 7007040, "caption" => "ثبت مجوز جایگزین بسته بندی در تخصیص سریع پیمانکار", "status_type_id" => 7007 ],

        //  فرم مرجوعی کالا
        [ "id" => 7009001, "caption" => "ثبت مرجوعی", "status_type_id" => 7009 ],
        [ "id" => 7009002, "caption" => "تایید کنترل کیفیت", "status_type_id" => 7009 ],
        [ "id" => 7009003, "caption" => "تایید نگهبانی", "status_type_id" => 7009 ],
        [ "id" => 7009004, "caption" => "ثبت ورود به انبار", "status_type_id" => 7009 ],
        [ "id" => 7009005, "caption" => "ارسال کالا (درخواست کننده)", "status_type_id" => 7009 ],
        [ "id" => 7009006, "caption" => "تایید کارشناس فروش", "status_type_id" => 7009 ],


        [ "id" => 1001001, "caption" => "ارسال پیامک", "status_type_id" => 7007 ],

        // تخصیص پیمانکار
        [ "id" => 5310103, "caption" => " هماهنگی جهت ارسال مواد اولیه", "status_type_id" => 5310 ],
        [ "id" => 5310107, "caption" => "تخصیص پیمانکار", "status_type_id" => 5310 ],
        [ "id" => 5310108, "caption" => "تحویل مواد اولیه", "status_type_id" => 5310 ],
        [ "id" => 5310104, "caption" => "تایید دریافت مواد اولیه", "status_type_id" => 5310 ],
        [ "id" => 5310105, "caption" => "تحویل کالا", "status_type_id" => 5310 ],
        [ "id" => 5310109, "caption" => "خاتمه یافته شدن", "status_type_id" => 5310 ],

        // تخصیص کارت تولید به ماشین
        [ "id" => 5310501, "caption" => "تخصیص جدید", "status_type_id" => 5310 ],
        [ "id" => 5310502, "caption" => "ایجاد تخصیص مجدد", "status_type_id" => 5310 ],
        [ "id" => 5310503, "caption" => "خاتمه یافته شدن", "status_type_id" => 5310 ],


        [ "id" => 5310901, "caption" => "شروع ستاپ (setup)", "status_type_id" => 5310 ],
        [ "id" => 5310902, "caption" => "شروع عملیات", "status_type_id" => 5310 ],
        [ "id" => 5310903, "caption" => "پایان عملیات", "status_type_id" => 5310 ],
        [ "id" => 5310904, "caption" => "انجام تنظیمات نهایی", "status_type_id" => 5310 ],
        [ "id" => 5310905, "caption" => "تایید کنترل کیفیت", "status_type_id" => 5310 ],
        [ "id" => 5310906, "caption" => "شروع تست کالا", "status_type_id" => 5310 ],
        [ "id" => 5310907, "caption" => "پایان تست کالا", "status_type_id" => 5310 ],
        [ "id" => 5310908, "caption" => "تایید تست کالا", "status_type_id" => 5310 ],
        [ "id" => 5310909, "caption" => "عدم تایید تست کالا (ویرایش BOM)", "status_type_id" => 5310 ],
        [ "id" => 5310910, "caption" => "پایان بسته بندی کارت جاری", "status_type_id" => 5310 ],
        [ "id" => 5310911, "caption" => "پایان عملیات و ادامه ثبت تولید", "status_type_id" => 5310 ],
        [ "id" => 5310912, "caption" => "جابجایی تخصیص", "status_type_id" => 5310 ],

        /// تخصص کارت تامین به مشتری
        [ "id" => 5312101, "caption" => "تخصیص به مشتری", "status_type_id" => 5312 ],
        [ "id" => 5312102, "caption" => "تایید فرم ورود به انبار", "status_type_id" => 5312 ],
        // رویداد های حامل
        [ "id" => 5320101, "caption" => "ایجاد حامل", "status_type_id" => 5320 ],
        [ "id" => 5320102, "caption" => "ارسال محصول به کارفرما", "status_type_id" => 5320 ],
        [ "id" => 5320103, "caption" => "چله گذاری", "status_type_id" => 5320 ],
        [ "id" => 5320104, "caption" => "پایان بافت کارت تولید", "status_type_id" => 5320 ],
        [ "id" => 5320105, "caption" => "استخراج کالا", "status_type_id" => 5320 ],
        [ "id" => 5320106, "caption" => "تغییر بسته بندی", "status_type_id" => 5320 ],
        [ "id" => 5320107, "caption" => "تحویل به انبار", "status_type_id" => 5320 ],
        [ "id" => 5320108, "caption" => "ورود به انبار", "status_type_id" => 5320 ],
        [ "id" => 5320109, "caption" => "خالی کردن حامل", "status_type_id" => 5320 ],
        [ "id" => 5320110, "caption" => "تغییر بسته بندی", "status_type_id" => 5320 ],
        [ "id" => 5320111, "caption" => "پر شدن حامل", "status_type_id" => 5320 ],
        [ "id" => 5320112, "caption" => "تغییر مدیر سیستم", "status_type_id" => 5320 ],
        [ "id" => 5320113, "caption" => "ثبت در فرم تولید", "status_type_id" => 5320 ],


        // رویدادهای درخواست کالا از انبار

        [ "id" => 7005001, "caption" => "ایجاد درخواست از انبار", "status_type_id" => 7005 ],
        [ "id" => 7005002, "caption" => "تایید دریافت کالا", "status_type_id" => 7005 ],
        [ "id" => 7005004, "caption" => "تحویل کالا", "status_type_id" => 7005 ],
        [ "id" => 7005007, "caption" => "تایید نهایی (واحد مالی)", "status_type_id" => 7005 ],
        [ "id" => 7005008, "caption" => "کنسل کردن درخواست", "status_type_id" => 7005 ],
        [ "id" => 7005009, "caption" => "تایید خروج (نگهبانی)", "status_type_id" => 7005 ],
        [ "id" => 7005010, "caption" => "تایید پیش نویس (واحد مالی)", "status_type_id" => 7005 ],
        [ "id" => 7005011, "caption" => "عدم تایید برگ خروج", "status_type_id" => 7005 ],
        [ "id" => 7005012, "caption" => "ثبت تراکنش انبار", "status_type_id" => 7005 ],
        [ "id" => 7005013, "caption" => "تایید برگ خروج", "status_type_id" => 7005 ],
        [ "id" => 7005014, "caption" => "ثبت ارسال (بارگیری)", "status_type_id" => 7005 ],
        [ "id" => 7005015, "caption" => "موجود شدن کالا", "status_type_id" => 7005 ],
        [ "id" => 7005016, "caption" => "خاتمه یافته کردن", "status_type_id" => 7005 ],
        [ "id" => 7005017, "caption" => "اصلاح برگ خروج", "status_type_id" => 7005 ],
        [ "id" => 7005018, "caption" => "اضافه شدن برگ خروج", "status_type_id" => 7005 ],
        [ "id" => 7005019, "caption" => "ثبت اطلاعات درخواست", "status_type_id" => 7005 ],
        [ "id" => 7005020, "caption" => "باز کردن بسته بندی حمل و نقل", "status_type_id" => 7005 ],
        [ "id" => 7005021, "caption" => "تغییر در درخواست", "status_type_id" => 7005 ],
        [ "id" => 7005022, "caption" => "ثبت مجوز", "status_type_id" => 7005 ],
        [ "id" => 7005023, "caption" => "رد مجوز", "status_type_id" => 7005 ],
        [ "id" => 7005024, "caption" => "تایید کنترل کیفیت", "status_type_id" => 7005 ],
        [ "id" => 7005025, "caption" => "تایید وصول مطالبات ", "status_type_id" => 7005 ],
        [ "id" => 7005026, "caption" => "تغییر مقدار درخواست ", "status_type_id" => 7005 ],
        [ "id" => 7005027, "caption" => "کنسل کردن درخواست (حداکثر زمان ارسال) ", "status_type_id" => 7005 ],

        // مرخصی
        [ "id" => 4630001, "caption" => "ثبت مرخصی", "status_type_id" => 4630 ],
        [ "id" => 4630002, "caption" => "تایید جانشین", "status_type_id" => 4630 ],
        [ "id" => 4630003, "caption" => "تایید مافوق", "status_type_id" => 4630 ],
        [ "id" => 4630004, "caption" => "عدم تایید مافوق", "status_type_id" => 4630 ],
        [ "id" => 4630005, "caption" => "عدم تایید جانشین", "status_type_id" => 4630 ],
        [ "id" => 4630009, "caption" => "در انتظار اخذ توضیح", "status_type_id" => 4630 ],
        [ "id" => 4630010, "caption" => "ثبت توضیحات", "status_type_id" => 4630 ],
        [ "id" => 4630011, "caption" => "انصراف", "status_type_id" => 4630 ],
        [ "id" => 4630012, "caption" => "ثبت جانشینی غیبت", "status_type_id" => 4630 ],
        [ "id" => 4630013, "caption" => "ثبت جانشینی غیبت", "status_type_id" => 4630 ],


        // سفارش
        [ "id" => 304010, "caption" => "تایید پیش نویس سفارش", "status_type_id" => 351 ],
        [ "id" => 304020, "caption" => "تایید کارشناس فروش", "status_type_id" => 351 ],
        [ "id" => 304030, "caption" => "تایید پیش فاکتور توسط مشتری", "status_type_id" => 351 ],
        [ "id" => 304040, "caption" => "تایید کارشناس وصول مطالبات", "status_type_id" => 351 ],
        [ "id" => 304050, "caption" => "تایید مدیر فروش", "status_type_id" => 351 ],
        [ "id" => 304060, "caption" => "تایید مدیر  مالی", "status_type_id" => 351 ],
        [ "id" => 304070, "caption" => "تایید مدیر عامل", "status_type_id" => 351 ],
        [ "id" => 304075, "caption" => "تایید هئیت مدیره", "status_type_id" => 351 ],
        [ "id" => 304080, "caption" => "تایید پردازش ", "status_type_id" => 351 ],
        [ "id" => 304990, "caption" => "ویرایش درخواست", "status_type_id" => 351 ],
        [ "id" => 304991, "caption" => "شروع ویرایش درخواست", "status_type_id" => 351 ],
        [ "id" => 304992, "caption" => "ثبت تخفیف خاص", "status_type_id" => 351 ],
        [ "id" => 304993, "caption" => "ارجاع به مرحله قبل", "status_type_id" => 351 ],
        [ "id" => 304994, "caption" => "ارجاع به کارتابل مشتری", "status_type_id" => 351 ],
        [ "id" => 304995, "caption" => " ارجاع به کارتابل کارشناس فروش", "status_type_id" => 351 ],
        [ "id" => 304996, "caption" => "اتصال به درگاه بانک", "status_type_id" => 351 ],


        [ "id" => 35010, "caption" => "خاتمه یافته قبل از شروع", "status_type_id" => 350 ],
        [ "id" => 35020, "caption" => "کنسل کردن درخواست", "status_type_id" => 350 ],
        [ "id" => 35060, "caption" => "خاتمه یافته", "status_type_id" => 350 ],
        [ "id" => 35065, "caption" => "کنسل کردن سفارش", "status_type_id" => 350 ],
        [ "id" => 35070, "caption" => " خاتمه یافته ( اپراتور) ", "status_type_id" => 350 ],
        [ "id" => 35080, "caption" => " ثبت مجوز بارگیری ", "status_type_id" => 350 ],
        [ "id" => 35090, "caption" => "  تایید برگ خروج ", "status_type_id" => 350 ],
        [ "id" => 35091, "caption" => "صدور برگ خروج ", "status_type_id" => 350 ],
        [ "id" => 35092, "caption" => "دریافت محصول", "status_type_id" => 350 ],
        [ "id" => 35095, "caption" => "  عدم تایید برگ خروج ", "status_type_id" => 350 ],
        [ "id" => 35096, "caption" => "ثبت تراکنش انبار", "status_type_id" => 350 ],
        [ "id" => 35097, "caption" => "ثبت سری سفارش", "status_type_id" => 350 ],
        [ "id" => 35098, "caption" => "ثبت مجوز مرجوعی", "status_type_id" => 350 ],
        [ "id" => 35099, "caption" => "دستور تولید مازاد", "status_type_id" => 350 ],
        [ "id" => 35100, "caption" => "در انتظار پردازش پس از آماده سازی", "status_type_id" => 350 ],
        [ "id" => 35101, "caption" => "پردازش پس از آماده سازی", "status_type_id" => 350 ],
        [ "id" => 35102, "caption" => "انجام پردازش", "status_type_id" => 350 ],
        [ "id" => 35103, "caption" => "عدم نیاز به پردازش توسط کارشناس دیجیتال", "status_type_id" => 350 ],
        [ "id" => 35104, "caption" => "خطا در پردازش سفارش", "status_type_id" => 350 ],
        [ "id" => 35105, "caption" => "تایید پردازش توسط کارشناس دیجیتال ", "status_type_id" => 350 ],

// درخواست تعریف کالا
        [ "id" => 5231001, "caption" => " ثبت درخواست طراحی کالا ", "status_type_id" => 5231 ],
        [ "id" => 5231002, "caption" => "***** ", "status_type_id" => 52310001 ], // حذف
        [ "id" => 5231003, "caption" => " تایید دریافت نمونه کالا ", "status_type_id" => 5231 ],
        [ "id" => 5231004, "caption" => " تعریف کالای جدید ", "status_type_id" => 5231 ],
        [ "id" => 5231005, "caption" => " ثبت اطلاعات پایه ", "status_type_id" => 5231 ],
        [ "id" => 5231006, "caption" => " ثبت کد رهگیری پست ", "status_type_id" => 5231 ],
        [ "id" => 5231007, "caption" => " ثبت اطلاعات کالای مصرفی", "status_type_id" => 5231 ],
        [ "id" => 5231008, "caption" => " ثبت اطلاعات فروش", "status_type_id" => 5231 ],
        [ "id" => 5231009, "caption" => " ثبت اطلاعات انبارش", "status_type_id" => 5231 ],
        [ "id" => 5231010, "caption" => " ثبت اطلاعات طبقه بندی", "status_type_id" => 5231 ],
        [ "id" => 5231011, "caption" => " ثبت مشخصات کالا", "status_type_id" => 5231 ],
        [ "id" => 5231012, "caption" => " ثبت اطلاعات مسیر محصول", "status_type_id" => 5231 ],
        [ "id" => 5231013, "caption" => " ثبت اطلاعات مشخصات مسیر محصول", "status_type_id" => 5231 ],
        [ "id" => 5231014, "caption" => " ثبت اطلاعات BOM", "status_type_id" => 5231 ],
        [ "id" => 5231015, "caption" => " ثبت اطلاعات کالای جایگزین تولید", "status_type_id" => 5231 ],
        [ "id" => 5231016, "caption" => " ثبت اطلاعات کالای جایگزین مصرف", "status_type_id" => 5231 ],
        [ "id" => 5231017, "caption" => " ثبت اطلاعات ضایعات", "status_type_id" => 5231 ],
        [ "id" => 5231018, "caption" => "طراحی جریان همبافتی (مواد)", "status_type_id" => 5231 ],
        [ "id" => 5231019, "caption" => "ثبت اطلاعات بسته بندی ها", "status_type_id" => 5231 ],
        [ "id" => 5231020, "caption" => "ثبت اطلاعات بسته طبقه بندی", "status_type_id" => 5231 ],
        [ "id" => 5231021, "caption" => "ثبت اطلاعات لات ", "status_type_id" => 5231 ],
        [ "id" => 5231022, "caption" => "ثبت اطلاعات شید ", "status_type_id" => 5231 ],
        [ "id" => 5231023, "caption" => "ثبت اطلاعات بهای تمام شده ", "status_type_id" => 5231 ],
        [ "id" => 5231024, "caption" => "صدور دستور تولید نمونه ", "status_type_id" => 5231 ],
        [ "id" => 5231025, "caption" => "پایان تولید نمونه ", "status_type_id" => 5231 ],
        [ "id" => 5231026, "caption" => "تایید اولیه نمونه ", "status_type_id" => 5231 ],
        [ "id" => 5231027, "caption" => "تایید نهایی نمونه ", "status_type_id" => 5231 ],
        [ "id" => 5231028, "caption" => "ارسال نمونه برای مشتری ", "status_type_id" => 5231 ],
        [ "id" => 5231029, "caption" => "تایید نمونه توسط مشتری ", "status_type_id" => 5231 ],
        [ "id" => 5231030, "caption" => "بارگذاری تصویر نهایی کالا ", "status_type_id" => 5231 ],
        [ "id" => 5231031, "caption" => "ثبت کالا در نرم افزار مالی ", "status_type_id" => 5231 ],
        [ "id" => 5231032, "caption" => "ثبت تعرفه گذاری", "status_type_id" => 5231 ],
        [ "id" => 5231033, "caption" => "ثبت قیمت گذاری", "status_type_id" => 5231 ],
        [ "id" => 5231034, "caption" => "تبت تنظیمات کنترل کیفیت", "status_type_id" => 5231 ],
        [ "id" => 5231034, "caption" => "تبت تنظیمات برنامه ریزی", "status_type_id" => 5231 ],

        [ "id" => 5231034, "caption" => "انجام گام 1 طراحی", "status_type_id" => 5231 ],
        [ "id" => 5231035, "caption" => "انجام گام 2 طراحی", "status_type_id" => 5231 ],
        [ "id" => 5231036, "caption" => "انجام گام 3 طراحی", "status_type_id" => 5231 ],
        [ "id" => 5231037, "caption" => "انجام گام 4 طراحی", "status_type_id" => 5231 ],
        [ "id" => 5231038, "caption" => "انجام گام 5 طراحی", "status_type_id" => 5231 ],

        [ "id" => 5231039, "caption" => "بازگشت به مراحل قبلی", "status_type_id" => 5231 ],

        [ "id" => 5231040, "caption" => "تکمیل اطلاعات خدمت", "status_type_id" => 5231 ],
        [ "id" => 5231041, "caption" => "ثبت خدمت در نرم افزار مالی", "status_type_id" => 5231 ],
        [ "id" => 5231042, "caption" => "تایید نهایی خدمت", "status_type_id" => 5231 ],

        [ "id" => 5231501, "caption" => " ثبت درخواست طراحی سریع کالا ", "status_type_id" => 5231 ],
        [ "id" => 5231502, "caption" => " صدور دستور نمونه گیری (طراحی سریع کالا) ", "status_type_id" => 5231 ],
        [ "id" => 5231503, "caption" => " تخصیص ناموفق", "status_type_id" => 5231 ],

        [ "id" => 5231601, "caption" => " ایجاد درخواست طراحی از کالاهای موجود ", "status_type_id" => 5231 ],


        [ "id" => 5231610, "caption" => " گام هایی نیاز به تکمیل ندارد", "status_type_id" => 5231 ],

        // اتوماسیون اداری
        [ "id" => 5250001, "caption" => "تعریف کار جدید", "status_type_id" => 5250 ],
        [ "id" => 5250002, "caption" => "ایجاد ارجاع جدید", "status_type_id" => 5250 ],
        [ "id" => 5250003, "caption" => "ثبت انجام کار", "status_type_id" => 5250 ],
        [ "id" => 5250004, "caption" => "تایید انجام کار", "status_type_id" => 5250 ],
        [ "id" => 5250005, "caption" => "ارجاع مجدد", "status_type_id" => 5250 ],
        [ "id" => 5250006, "caption" => "مشاهده کار", "status_type_id" => 5250 ],
        [ "id" => 5250007, "caption" => "اولین مشاهده کار", "status_type_id" => 5250 ],

        // رویداد های اجرای اسکریپت ها
        // وضعیت اجرای ماژول ها
        [ "id" => 41001, "caption" => "اجرای موفق مرحله n ام", "status_type_id" => 410 ],
        [ "id" => 41002, "caption" => "پایان اجرا", "status_type_id" => 410 ],
        [ "id" => 41003, "caption" => "اجرای نا موفق", "status_type_id" => 410 ],


        // رویدادهای حمل و نقل
        [ "id" => 6010101, "caption" => "ایجاد بار", "status_type_id" => 6010 ],
        [ "id" => 6010102, "caption" => "پایان بارگیری ", "status_type_id" => 6010 ],

        // رویدادهای نگهداری و تعمیرات
        [ "id" => 6003101, "caption" => "ایجاد درخواست", "status_type_id" => 6003 ],
        [ "id" => 6003102, "caption" => "شروع نت", "status_type_id" => 6003 ],
        [ "id" => 6003103, "caption" => "پایان نت", "status_type_id" => 6003 ],
        [ "id" => 6003104, "caption" => "تایید نت ", "status_type_id" => 6003 ],
        [ "id" => 6003105, "caption" => "تایید انجام نت ", "status_type_id" => 6003 ],
        [ "id" => 6003106, "caption" => "عدم تایید انجام نت", "status_type_id" => 6003 ],
        [ "id" => 6003107, "caption" => "عدم تایید نت", "status_type_id" => 6003 ],

        // رویدادهای مجوز ها
        [ "id" => 6040001, "caption" => "ایجاد درخواست", "status_type_id" => 6040 ],
        [ "id" => 6040002, "caption" => "تایید خبره", "status_type_id" => 6040 ],
        [ "id" => 6040003, "caption" => "عدم تایید خبره", "status_type_id" => 6040 ],
        [ "id" => 6040004, "caption" => "ارسال برای مافوق", "status_type_id" => 6040 ],
        [ "id" => 6040005, "caption" => "عدم تایید توسط سامانه", "status_type_id" => 6040 ],

        // انبار گردانی
        [ "id" => 524000301, "caption" => "ایجاد انبار گردانی", "status_type_id" => 52400 ],
        [ "id" => 524000302, "caption" => "پایان انبار گردانی", "status_type_id" => 52400 ],
        [ "id" => 524000303, "caption" => "پردازش اولیه", "status_type_id" => 52400 ],
        [ "id" => 524000304, "caption" => "تایید مرحله اول", "status_type_id" => 52400 ],
        [ "id" => 524000305, "caption" => "تایید مرحله دوم", "status_type_id" => 52400 ],
        [ "id" => 524000306, "caption" => "تایید مرحله سوم", "status_type_id" => 52400 ],
        [ "id" => 524000307, "caption" => "پردازش نهایی", "status_type_id" => 52400 ],
        [ "id" => 524000308, "caption" => "عدم تایید", "status_type_id" => 52400 ],
        [ "id" => 524000309, "caption" => "بررسی بسته بندی ها", "status_type_id" => 52400 ],
        [ "id" => 524000310, "caption" => "خوانش بسته بندی بعد از پردازش اولیه", "status_type_id" => 52400 ],

        // درخواست های همکاری
        ["id" =>4640001, "caption" => "ثبت درخواست همکاری", "status_type_id" => 4640],
        ["id" =>4640002, "caption" => "تایید اطلاعات درخواست", "status_type_id" => 4640],
        ["id" =>4640003, "caption" => "هماهنگی گزینش", "status_type_id" => 4640],
        ["id" =>4640004, "caption" => "ثبت نتیجه گزینش", "status_type_id" => 4640],
        ["id" =>4640005, "caption" => "تحویل مدارک", "status_type_id" => 4640],
        ["id" =>4640006, "caption" => "عدم تایید اطلاعات شخصی", "status_type_id" => 4640],
        ["id" =>4640007, "caption" => "عدم تایید اطلاعات آدرس", "status_type_id" => 4640],
        ["id" =>4640008, "caption" => "عدم تایید اطلاعات تحصیلی", "status_type_id" => 4640],
        ["id" =>4640009, "caption" => "عدم تایید اطلاعات شغلی", "status_type_id" => 4640],
        ["id" =>4640010, "caption" => "عدم تایید اطلاعات دوره های آموزشی", "status_type_id" => 4640],
        ["id" =>4640011, "caption" => "عدم تایید اطلاعات مدارک بارگذاری شده", "status_type_id" => 4640],
        ["id" =>4640012, "caption" => " تایید مدارک بارگذاری شده", "status_type_id" => 4640],
        ["id" =>4640013, "caption" => " تعیین پست سازمانی", "status_type_id" => 4640],
        ["id" =>4640014, "caption" => " ثبت پیش نویس قرارداد هوشمند (تامین کننده)", "status_type_id" => 4640],
        ["id" =>4640015, "caption" => " تایید اولیه پیش نویس قرارداد هوشمند (تامین کننده)", "status_type_id" => 4640],
        ["id" =>4640016, "caption" => "اصلاح  فرم درخواست", "status_type_id" => 4640],
        ["id" =>4640017, "caption" => "تایید قرارداد توسط (تامین کننده)", "status_type_id" => 4640],
        ["id" =>4640018, "caption" => " ثبت مرکزهزینه (تامین کننده)", "status_type_id" => 4640],
        ["id" =>4640019, "caption" => "بارگزاری مدارک مرحله دوم", "status_type_id" => 4640],
        ["id" =>4640020, "caption" => "تایید قرارداد توسط کارمند", "status_type_id" => 4640],
        ["id" =>4640021, "caption" => "ثبت طب کار توسط کارمند", "status_type_id" => 4640],
        ["id" =>4640022, "caption" => "اصلاح فرم درخواست", "status_type_id" => 4640],
        ["id" =>4640023, "caption" => "عدم تایید درخواست", "status_type_id" => 4640],
        ["id" =>4640024, "caption" => "عدم تایید اطلاعات افراد تحت تکفل", "status_type_id" => 4640],
        ["id" =>4640025, "caption" => "ثبت اطلاعات بانکی", "status_type_id" => 4640],
        ["id" =>4640026, "caption" => "تعریف اطلاعات مالی", "status_type_id" => 4640],
        ["id" =>4640027, "caption" => "تعریف حساب اینترنت", "status_type_id" => 4640],
        ["id" =>4640028, "caption" => "ثبت پیش نویس قرارداد هوشمند (مشتری)", "status_type_id" => 4640],
        ["id" =>4640029, "caption" => " تایید اولیه  پیش نویس قرارداد هوشمند (مشتری)", "status_type_id" => 4640],
        ["id" =>4640030, "caption" => "تایید نهایی  پیش نویس قرارداد هوشمند (مشتری)", "status_type_id" => 4640],
        ["id" =>4640031, "caption" => "   ثبت مرکز هزینه (مشتری)", "status_type_id" => 4640],
        ["id" =>4640032, "caption" => "تایید قرارداد توسط (مشتری)", "status_type_id" => 4640],
        ["id" =>4640033, "caption" => "تایید تحویل قراداد (مشتری)", "status_type_id" => 4640],
        ["id" =>4640034, "caption" => "تایید تحویل قراداد (تامین کننده)", "status_type_id" => 4640],
        ["id" =>4640035, "caption" => "ثبت پیش نویس قرارداد هوشمند (پیمانکار)", "status_type_id" => 4640],
        ["id" =>4640036, "caption" => " تایید اولیه  پیش نویس قرارداد هوشمند (پیمانکار)", "status_type_id" => 4640],
        ["id" =>4640037, "caption" => "تایید نهایی  پیش نویس قرارداد هوشمند (پیمانکار)", "status_type_id" => 4640],
        ["id" =>4640038, "caption" => "   ثبت مرکز هزینه (پیمانکار)", "status_type_id" => 4640],
        ["id" =>4640039, "caption" => "تایید قرارداد توسط (پیمانکار)", "status_type_id" => 4640],
        ["id" =>4640040, "caption" => "تایید تحویل قراداد (پیمانکار)", "status_type_id" => 4640],
        ["id" =>4640041, "caption" => "ثبت نمایندگی", "status_type_id" => 4640],
        ["id" =>4640042, "caption" => " تایید نهایی پیش نویس قرارداد هوشمند (تامین کننده)", "status_type_id" => 4640],
        ["id" => 4640043, "caption" => "تایید پیش نویس اطلاعات (مشتری)", "status_type_id" => 4640],
        ["id" => 4640044, "caption" => "تایید  پیش نویس اطلاعات (پیمانکار)", "status_type_id" => 4640],
        ["id" => 4640045, "caption" => "تایید  پیش نویس اطلاعات (تامین کننده)", "status_type_id" => 4640],
        // ارزیابی عملکرد
        ["id" =>4650001, "caption" => "ایجاد فرم ارزیابی", "status_type_id" => 4650],
        ["id" =>4650002, "caption" => "انجام ارزیابی", "status_type_id" => 4650],


        // کارت تولید،
        // دستور پیمان
        ["id" =>7008001, "caption" => "تخصیص موفق", "status_type_id" => 7008],
        ["id" =>7008002, "caption" => "تخصیص ناموفق", "status_type_id" => 7008],
        ["id" =>7008003, "caption" => "صدور دستور ابلاغ", "status_type_id" => 7008],
        ["id" =>7008004, "caption" => "کنسل کردن تخصیص", "status_type_id" => 7008],
        ["id" =>7008005, "caption" => "تغییر مقدار تخصیص", "status_type_id" => 7008],
        ["id" =>7008006, "caption" => "خاتمه یافته کردن کارت", "status_type_id" => 7008],
        ["id" =>7008007, "caption" => "صدور دستور ابلاغ (پیاده سازی)", "status_type_id" => 7008],
        ["id" =>7008008, "caption" => "صدور دستور ابلاغ (مازاد تولید)", "status_type_id" => 7008],
        ["id" =>7008009, "caption" => "تغییر مقدار تخصیص (در تخصیص مجدد)", "status_type_id" => 7008],
        ["id" =>7008010, "caption" => "صدور دستور ابلاغ (کارشناس دیجیتال)", "status_type_id" => 7008],
        ["id" =>7008011, "caption" => "نرمالایز کردن فرم تولید", "status_type_id" => 7008],
        //، کارت تامین

    ];
    private $table = 'events';

    public function run() {

        foreach ( $this->data as $item ) {
            if ( ! DB::table( $this->table )->where( [ "id" => $item["id"] ] )->exists() ) {
                DB::table( $this->table )->insert( $item );
            } else {
                DB::table( $this->table )->where( [ "id" => $item["id"] ] )->update( $item );
            }
        }
    }
}
