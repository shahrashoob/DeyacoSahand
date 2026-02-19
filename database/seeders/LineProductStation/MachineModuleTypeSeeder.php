<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineModuleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
        //machine_off_reason
    // این مشخصه به اشتباه id ندارد
    private $data = [
        //
        [
            "id" => 1,
            "code" => "dobby",
            "caption" => 'ماژول های ماشین دابی و بادامکی (کارخانجات بزرگ و دستگاه های قدیمی)',
            "seeder_namespace" => "\Database\Seeders\GoodsKind\FabricRaw\Dobby\DobbySeeder",
            "directory_namespace" => "\App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby",

            "machine_production_status_id" => -1,
            "machine_off_reason" => -1,

            "machine_status_type_id" => -1,
            "production_status_type_id" => -1,
            "production_form_status_type_id" => -1,
            "production_form_terminated" => -1,// وضعیت خاتمه یافته فرم تولید ماژول های ماشین
            "production_card_terminated" => -1, // وضعیت خاتمه یافته شدن کارت تولید
            "include_questions_about_start_and_end_of_operation"=>0 // شامل سوالات شروع عملیات و پایان عملیات در تعریف مسیر محصول کالا است؟
        ],
        [
            "id" => 2,
            "code" => "jacquard",
            "caption" => 'ماژول های ماشین ژاکارد',
            "seeder_namespace" => "\Database\Seeders\GoodsKind\FabricRaw\Jacquard\JacquardSeeder",
            "directory_namespace" => "\App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard",

            "machine_production_status_id" => 7003017, // نداشتن سفارش
            "machine_off_reason" => 1615, // نداشتن سفارش

            "machine_status_type_id" => 7003,
            "production_status_type_id" => 7001,
            "production_form_status_type_id" => 7002,
            "production_form_in_production" => 7002001,// وضعیت در حال تولید فرم تولید ماژول های ماشین
            "production_form_terminated" => 7002005,// وضعیت خاتمه یافته فرم تولید ماژول های ماشین

            "production_card_terminated" => 7001004, // وضعیت خاتمه یافته شدن کارت تولید
            "include_questions_about_start_and_end_of_operation"=>0 // شامل سوالات شروع عملیات و پایان عملیات در تعریف مسیر محصول کالا است؟

        ],
        [
            "id" => 3,
            "code" => "matthys",
            "caption" => 'ماژول های ماشین چله کشی matthys',
            "seeder_namespace" => "\Database\Seeders\GoodsKind\Warps\Matthys\MatthysSeeder",
            "directory_namespace" => "\App\Http\Controllers\GoodsKindProcess\Warps\Matthys",

            "machine_production_status_id" => 7203001, // نداشتن سفارش
            "machine_off_reason" => 4000, // نداشتن سفارش

            "machine_status_type_id" => 7203,
            "production_status_type_id" => 7201,
            "production_form_status_type_id" => 7202,
            "production_form_in_production" => 7202002,// وضعیت در حال تولید فرم تولید ماژول های ماشین
            "production_form_terminated" => 7202004,// وضعیت خاتمه یافته فرم تولید ماژول های ماشین
            "production_card_terminated" => 7201004, // وضعیت خاتمه یافته شدن کارت تولید
            "include_questions_about_start_and_end_of_operation"=>0 // شامل سوالات شروع عملیات و پایان عملیات در تعریف مسیر محصول کالا است؟

        ],
        [
            "id" => 4,
            "code" => "finishing_machine",
            "caption" => 'ماشین آلات عمومی تکمیل صنعت نساجی',
            "seeder_namespace" => "\Database\Seeders\GoodsKind\Fabric\FinishingMachine\FinishingMachineSeeder",//
            "directory_namespace" => "\App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine", //
            "machine_production_status_id" => 7303001, // نداشتن سفارش
            "machine_off_reason" => 8000, // نداشتن سفارش

            "machine_status_type_id" => 7303,
            "production_status_type_id" => 7301,
            "production_form_status_type_id" => 7302,
            "production_form_in_production" => 7302001,// وضعیت در حال تولید فرم تولید ماژول های ماشین
            "production_form_terminated" => 7302002,// وضعیت خاتمه یافته فرم تولید ماژول های ماشین
            "production_card_terminated" => 7301004, // وضعیت خاتمه یافته شدن کارت تولید
            "include_questions_about_start_and_end_of_operation"=>1 // شامل سوالات شروع عملیات و پایان عملیات در تعریف مسیر محصول کالا است؟

        ],
        [
            "id" => 5,
            "code" => "special_production_fabric",
            "caption" => 'SpecialProductionFabric - پارجه تکمیل ویژه دوره پیاده سازی',
            "seeder_namespace" => "\Database\Seeders\GoodsKind\Fabric\SpecialProduction\SpecialProductionSeeder",
            "directory_namespace" => "\App\Http\Controllers\GoodsKindProcess\Fabric\SpecialProduction",

            "machine_production_status_id" => 7303001, // نداشتن سفارش
            "machine_off_reason" => 8000, // نداشتن سفارش

            "machine_status_type_id" => 7303,
            "production_status_type_id" => 7301,
            "production_form_status_type_id" => 7302,
            "production_form_in_production" => 7302001,// وضعیت در حال تولید فرم تولید ماژول های ماشین
            "production_form_terminated" => 7302002,// وضعیت خاتمه یافته فرم تولید ماژول های ماشین
            "production_card_terminated" => 7301004, // وضعیت خاتمه یافته شدن کارت تولید
            "include_questions_about_start_and_end_of_operation"=>0 // شامل سوالات شروع عملیات و پایان عملیات در تعریف مسیر محصول کالا است؟

        ],

        [
            "id" => 6,
            "code" => "karl_mayer",
            "caption" => 'ماژول های ماشین چله کشی karl mayer',
            "seeder_namespace" => "\Database\Seeders\GoodsKind\Warps\KarlMayer\KarlMayerSeeder",
            "directory_namespace" => "\App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer",

            "machine_production_status_id" => 7203001, // نداشتن سفارش
            "machine_off_reason" => 4000, // نداشتن سفارش

            "machine_status_type_id" => 7203,
            "production_status_type_id" => 7201,
            "production_form_status_type_id" => 7202,
            "production_form_in_production" => 7202002,// وضعیت در حال تولید فرم تولید ماژول های ماشین
            "production_form_terminated" => 7202004,// وضعیت خاتمه یافته فرم تولید ماژول های ماشین
            "production_card_terminated" => 7201004, // وضعیت خاتمه یافته شدن کارت تولید
            "include_questions_about_start_and_end_of_operation"=>0 // شامل سوالات شروع عملیات و پایان عملیات در تعریف مسیر محصول کالا است؟

        ],
    ];

    public static $checklist = [
        /**
         * ماشین های دابی بادامکی قدیمی
         */
        1 => [
            "design_change" => [
                220262, // تعداد سرنخ چله
                220330, //  تعداد سرنخ زمینه
                220306, // تعداد ورد زمینه
                220307, // تعداد ورد کناره
                220325, // نوع نخ کشی
                220308, // نخ کشی از ورد زمینه
                220309, // نخ کشی از ورد کناره
                220228, // عرض شانه
                220271, // نمره شانه
                220311, // تعداد نخ در دندانه شانه کناره
                220310,  // تعداد نخ در دندانه شانه زمینه
                220298, // عرض چله
                220376, // تراکم نهایی تار
            ],
            "density_change" => [
                220380, // تراکم پایه
                220224,  // تراکم پود 1
                220225, // تراکم پود 2
                220377, // تراکم پود 3
                220384, // تراکم پود 4
                220385, // تراکم پود 5
                220386, // تراکم پود 6
                220387, // تراکم پود 7
                220388, // تراکم پود 8
            ],
            "article_change" => [0],
            "current_production_form_status" => [
                7002001, // در حال تکمیل
                7002007, // در انتظار استخراج پارچه پایانی
                7002008, // در حال بافت پارچه پایانی
                7002009  // در حال تغییر نخ پود (دستور توقف)
            ],
            "current_production_form_status_with_reserve" => [
                7002001, // در حال تکمیل
                7002008, // در حال بافت پارچه پایانی
                7002011, // در حال بارگذاری
            ],
            // وضعیت بعداز تحویل چله اگر ماشین در وضعیت 019 باشد
            "warps_delivery_7003019" => 7003006,
            "warps_delivery_7003021" => 7003022,
            "warps_delivery_7003029" => 7003028

        ],
        /**
         * ماشین های ژاکارد
         */
        2 => [
            "design_change" => [
//                220262, // تعداد سرنخ چله
//                220263, // عرض پارچه خام
                220376, // تراکم نهایی تار
            ],
            "density_change" => [
                220380, // تراکم پایه
                220224,  // تراکم پود 1
                220225, // تراکم پود 2
                220377, // تراکم پود 3
                220384, // تراکم پود 4
                220385, // تراکم پود 5
                220386, // تراکم پود 6
                220387, // تراکم پود 7
                220388, // تراکم پود 8
            ],
            "article_change" => [
                220219, // کالیته
            ],
            "current_production_form_status" => [
                7002001, // در حال تکمیل
                7002008, // در حال بافت پارچه پایانی
            ],
            "current_production_form_status_with_reserve" => [
                7002001, // در حال تکمیل
                7002008, // در حال بافت پارچه پایانی
                7002011, // در حال بارگذاری
            ],
            "reserve_production_form_status" => [
                7002011, // در حال بارگذاری
            ],
            // مشخصه شییر
            "shear" => [
                220410 // شییر
            ],
            // مشخصه تکمیل
            "completing" => [
                220409 // نوع پروسه تکمیل
            ],
            // مقدار فیلد نوع پروسه تکمیل، از نوع تکمیلی است
            "is_takmili_item_id" => 493,

            // وضعیت بعداز تحویل چله اگر ماشین در وضعیت 019 باشد
            "warps_delivery_7003019" => 7003044,
            "warps_delivery_7003021" => 7003022,
            // رویدادی که در آن ماشین شروع به تولید کارت جاری می کند.
            "start_machine_event_for_production" => [
                570 // شروع تغییر کالیته
            ]
        ],

        /**
         * ماشین های چله کشی Matthys
         */
        3 => [
            "current_production_form_status" => [
                7202001, //  در انتظار شروع چله کشی
                7002002, //  در حال چله کشی
                7002003, //  در حال برگردان
            ],
            "current_production_form_status_with_reserve" => [
                7202001, //  در انتظار شروع چله کشی
                7002002, //  در حال چله کشی
                7002003, //  در حال برگردان
            ],
            // رویدادی که در آن ماشین شروع به تولید کارت جاری می کند.
            "start_machine_event_for_production" => [
                1010 // پایان قفسه گذاری
            ],
            "reserve_production_form_status" => [
                -1, // چله کشی فرم رزرو ندارد
            ],
        ],

        /**
         * jet - ماشین های جت
         */
        4 => [
            "current_production_form_status" => [
                7302001, // در حال تکمیل
            ],
            "start_machine_event_for_production" => [
                -1
            ],
            "reserve_production_form_status" => [
                -1,
            ],
            "current_production_form_status_with_reserve" => [
                -1
            ],
        ],
        /**
         * ماشین های تولید SpecialProduction Fabric
         */
        5 => [
            "current_production_form_status" => [
                -1
            ],
            "start_machine_event_for_production" => [
                -1
            ],
            "reserve_production_form_status" => [
                -1,
            ],
            "current_production_form_status_with_reserve" => [
                -1
            ],
        ],


        /**
         * ماشین های چله کشی KarlMayer
         */
        6 => [
            "current_production_form_status" => [
                7202001, //  در انتظار شروع چله کشی
                7002002, //  در حال چله کشی
                7002003, //  در حال برگردان
            ],
            "current_production_form_status_with_reserve" => [
                7202001, //  در انتظار شروع چله کشی
                7002002, //  در حال چله کشی
                7002003, //  در حال برگردان
            ],
            // رویدادی که در آن ماشین شروع به تولید کارت جاری می کند.
            "start_machine_event_for_production" => [
                1010 // پایان قفسه گذاری
            ],
            "reserve_production_form_status" => [
                -1, // چله کشی فرم رزرو ندارد
            ],
        ],
    ];
    private $table = 'machine_module_types';

    public function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {
            DB::table($this->table)->insert($item);
        }

    }
}
