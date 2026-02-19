<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScriptSeeder extends Seeder {
    private $data = [
        //
        [
            "id"               => 1,
            "code"             => 1001,
            "caption_en"       => 1001,
            "caption"          => "بات هوشمند پیش بینی زمان های کارت تولید",
            "active_status_id" => 1200,
            "script_type_id"   => 1,
            "cron"=>"*/5 * * * *"
        ],
        [
            "id"               => 2,
            "code"             => 1002,
            "caption_en"       => 1002,
            "caption"          => "بات هوشمند گزارش پیامکی کالا براساس وضعیت های مختلف بسته بندی",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"30 07 * * *"
        ],
        [
            "id"               => 3,
            "code"             => 1003,
            "caption_en"       => 1003,
            "caption"          => "دستیار هوشمند محاسبه موجودی انبار",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"*/11 * * * *"
        ],
        [
            "id"               => 4,
            "code"             => 1004,
            "caption_en"       => 1004,
            "caption"          => "بات هوشمند گزارش میزان تولید استخراج شده (براساس طبقه بندی کالا)",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"30 6 23 * *"
        ],
        [
            "id"               => 5,
            "code"             => 1005,
            "caption_en"       => 1005,
            "caption"          => "بات محاسبه مقدار کالای تولید شده به ازای هر لاگ ماشین و تکمیل جدول گزارش 1010 ( میزان کالای تولید شده)",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"*/10 * * * *"
        ],
        [
            "id"               => 6,
            "code"             => 1006,
            "caption_en"       => 1006,
            "caption"          => "بات هوشمند محاسبه راندمان ایستگاه کاری",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"00 06 * * *"
        ],
        [
            "id"               => 7,
            "code"             => 1007,
            "caption_en"       => 1007,
            "caption"          => "دستیار هوشمند درخواست کالا از طرف انبارک ماشین به انبار",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"00 08 * * *"
        ],
        [
            "id"               => 8,
            "code"             => 1008,
            "caption_en"       => "GoodsReceiptBot",
            "caption"          => "بات هوشمند رسید و ثبت بسته بندی های  فرم ورود به انبار در انتظار پردازش ",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"* * * * *"
        ],
        [
            "id"               => 9,
            "code"             => 1009,
            "caption_en"       => "WhizBot",
            "caption"          => "بات هوشمند انبار  جهت ثبت تراکنش های مصرف",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"*/5 * * * *"
        ],
        [
            "id"               => 10,
            "code"             => 1010,
            "caption_en"       => "TransportBot",
            "caption"          => "بات هوشمند حمل و نقل",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"*/10 7-16 * * *"
        ],
        [
            "id"               => 11,
            "code"             => 1011,
            "caption_en"       => "ProductionBot",
            "caption"          => "بات هوشمند بروز رسانی مقدار تولید",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"26 7,19 * * *"
        ],
        [
            "id"               => 12,
            "code"             => 1012,
            "caption_en"       => "AssistantForReturnMaterial",
            "caption"          => "دستیار هوشمند انبار جهت برگشت مواد اولیه از انبارک ماشین",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"*/5 * * * *"
        ],
        [
            "id"               => 13,
            "code"             => 1013,
            "caption_en"       => "FinancialSoftware",
            "caption"          => "دستیار هوشمند ثبت تراکنش در نرم افزار مالی",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"* * * * *"
        ],
        [
            "id"               => 14,
            "code"             => 1014,
            "caption_en"       => "ShutdownMachine",
            "caption"          => "دستیار هوشمند بررسی خاموشی دستگاه",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"45 07 * * *"
        ],
        [
            "id"               => 15,
            "code"             => 1015,
            "caption_en"       => "1015",
            "caption"          => "دستیار هوشمند انبار جهت ثبت تراکنش های برگ خروج در سامانه",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"* * * * *"
        ],
        [
            "id"               => 16,
            "code"             => 1016,
            "caption_en"       => "1016",
            "caption"          => "دستیار هوشمند ناظر",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"*/40 * * * *"
        ],
        [
            "id"               => 17,
            "code"             => 1017,
            "caption_en"       => "1017",
            "caption"          => "دستیار هوشمند نظارت بر تحویل شیفت",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"30 * * * *"
        ],
        [
            "id"               => 18,
            "code"             => 1018,
            "caption_en"       => "1018",
            "caption"          => "بات ایجاد فایل اکسل گزارش 1003- گزارش گردش کالا",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"*/5 * * * *"
        ],
        [
            "id"               => 19,
            "code"             => 1019,
            "caption_en"       => "1019",
            "caption"          => "دستیار هوشمند انباردار، جهت   اطلاع رسانی برگشت مواد اولیه",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"00 08 * * *"
        ],
        [
            "id"               => 20,
            "code"             => 1020,
            "caption_en"       => "1020",
            "caption"          => "دستبار دیجیتال حسابداری: محاسبه مقدار ریالی هر فرم ورود/برگ خروج  انبار",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"*/15 * * * *"
        ],
        [
            "id"               => 21,
            "code"             => 1021,
            "caption_en"       => "1021",
            "caption"          => "دستبار دیجیتال دیاکو جهت انجام عملیات های خیلی بزرگ",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"* * * * *"
        ],
        [
            "id"               => 22,
            "code"             => 1022,
            "caption_en"       => "1022",
            "caption"          => "دستیار دیجیتال جهت محاسبه وضعیت های مهم بسته بندی ها",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"*/2 16-23 * * *",
            "priority"=>22
        ],
        [
            "id"               => 23,
            "code"             => 1023,
            "caption_en"       => "1023",
            "caption"          => "دستیار دیجیتال منابع انسانی جهت محاسبه کارکرد پرسنل",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"0 1 * * *",
            "priority"=>23
        ],
        [
            "id"               => 24,
            "code"             => 1024,
            "caption_en"       => "1024",
            "caption"          => "دستیار دیجیتال محاسبه مصرف واقعتی دستورهای پیمان",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"0 2 * * *",
            "priority"=>24
        ],
        [
            "id"               => 25,
            "code"             => 1025,
            "caption_en"       => "1025",
            "caption"          => "بروز رسانی وضعیت های پرسنل",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"*/15 * * * *",
            "priority"=>24
        ],
        [
            "id"               => 26,
            "code"             => 1026,
            "caption_en"       => "1026",
            "caption"          => "اجرای الگوریتم های تخصیص و صدور کارت تولید ",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"0 6 * * *",
            "priority"=>26
        ],
        [
            "id"               => 27,
            "code"             => 1027,
            "caption_en"       => "1027",
            "caption"          => "بات نظارت بر توقف ماشین ها در وضعیت های مختلف ",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"0 * * * *",
            "priority"=>27
        ],
        [
            "id"               => 28,
            "code"             => 1028,
            "caption_en"       => "1028",
            "caption"          => "محاسبه بهای تمام شده بسته بندی ها ",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"0 * * * *",
            "priority"=>28
        ],
        [
            "id"               => 29,
            "code"             => 1029,
            "caption_en"       => "1029",
            "caption"          => "محاسبه هزینه پیامک و صدور فاکتور ",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"5 0 * * *",
            "priority"=>29
        ],
        [
            "id"               => 30,
            "code"             => 1030,
            "caption_en"       => "1030",
            "caption"          => "کارشناس دیجیتال برنامه ریزی تامین کالای دیاکو",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"5 0 * * *",
            "priority"=>30
        ],
        [
            "id"               => 33,
            "code"             => 1033,
            "caption_en"       => "1033",
            "caption"          => "کارشناس دیجیتال برنامه ریزی دیاکو ",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"5 0 * * *",
            "priority"=>33
        ],
        [
            "id"               => 34,
            "code"             => 1034,
            "caption_en"       => "1034",
            "caption"          => "کارشناس دیجیتال محاسبه مقدار میانگین مصرف",
            "active_status_id" => 1210,
            "script_type_id"   => 1,
            "cron"=>"5 0 * * *",
            "priority"=>34
        ],


    ];
    private $table = 'scripts';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            } else {
                unset($item["active_status_id"]);
                unset($item["cron"]);
                DB::table( $this->table )->
                where( "id", $item["id"] )->update( $item );
            }

        }
    }
}
