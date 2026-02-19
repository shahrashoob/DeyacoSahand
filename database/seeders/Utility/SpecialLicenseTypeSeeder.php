<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialLicenseTypeSeeder extends Seeder
{
    private $data = [
        //
        [
            "id" => 1,
            "code" => "1001",
            "caption" => "  خروج کالا از انبار بیش از حد مجاز",
            "description" => "توضیحات مربوط به درخواست مجوز برای خروج کالا از اانبار ...",
            "back_route" => ""
            /*
               param1: کد کالا
               param2: مقدار درخواست ثبت شده
               param3: مقدار درخواست جدید جهت خروج
             */
        ],
        [
            "id" => 2,
            "code" => "1002",
            "caption" => " پایان تولید، کمتر از مقدار کارت تولید"
            /*
             param1:
             param2:
             param3:
           */
        ],
        [
            "id" => 3, "code" => "1003", "caption" => " ویرایش تردد"
            /*
                param1:ساعت ورود صحیح
                param2:ساعت خروج صحیح
                param3: نوع ورود / خروج
                param4: تاریخ اشتباه قبلی
              */
        ],
        ["id" => 4, "code" => "1004", "caption" => " اضافه کردن بسته بندی به درخواست خروج از انبار"
            /*
                param1: شناسه درخواست خروج از انبار
                param2: شماره ردیف درخواست خروج از انبار
                param3: کد بسته بندی جدید
                param4: آیا بعد از تایید مجوز، بسته بندی به لیست بسته بندی های مجاز کالا اضافه گردد.
              */
        ],
        ["id" => 5, "code" => "1005", "caption" => " باز کردن بسته بندی حمل و نقل"
            /*
                param1: کد درخواست خروج از انبار
                param2:
                param3:
              */
        ],
        ["id" => 6, "code" => "1006", "caption" => " تایید وزن واقعی بسته بندی"
            /*
                param1: وزن سیستم
                param2: وزن واقعی
                param3: مقدار واقعی
                param4: وضعیت بعدی بسته بندی
                param5: وزن ناخالص
              */
        ],
        ["id" => 7, "code" => "1007", "caption" => " تحویل کالا کمتر از حد مجاز"
            /*
                param1: کد کالا
                param2: مقدار درخواست ثبت شده
                param3: مقدار درخواست جدید جهت خروج
              */
        ],
        ["id" => 8, "code" => "1008", "caption" => " تغییر در درخواست خروج از انبار "
            /*
                param1: شماره درخواست
                param2: شماره ردیف های درخواست
                param3: شماره بسته بندی جدید
                param4: کد انبار جدید
              */
        ],
        ["id" => 9, "code" => "1009", "caption" => "مجوز برگشت کالا به تامین کننده"
            /*
             * reference_id: شماره فرم انبار
                param1: تامین کننده
                param2:
                param3:
                param4:
              */
        ],
        ["id" => 10, "code" => "1010", "caption" => "مجوز درصد جمع شدگی"
            /*
             * reference_id: شماره بسته بندی
                param1: از فلان
                param2: به فلان
                param3:
                param4:
              */
        ],
        ["id" => 11, "code" => "1011", "caption" => "مجوز ایجاد کانال رزور"
            /*
             * reference_id: کد ماشین
                param1: کانال قبلی
                param2:
                param3:


                param4:
              */
        ],
        ["id" => 12, "code" => "1012", "caption" => " تعریف نوع حامل جدید"
            /*
             * reference_id: true
                param1: اطلاعات نوع حامل جدید شناسه json 1
                param2:  2
                param3:  3
                param4:  4
              */
        ],

        ["id" => 13, "code" => "1013", "caption" => " تعریف نوع بسته بندی جدید"
            /*
             * reference_id: true
                param1: اطلاعات نوع حامل جدید شناسه json 1
                param2:  2
                param3:  3
                param4:  4
              */
        ],

        ["id" => 14, "code" => "1014", "caption" => " مشاهده بارکد بسته بندی های خوانده نشده در بارگیری"
            /*
             * reference_id: true // شماره بار
                param1: کد کالا
                param2:  تعداد مجاز قابل مشاهده
                param3:  3
                param4:  4
              */
        ],

        ["id" => 15, "code" => "1015", "caption" => "برگشت کالا توسط مشتری خارج از مهلت تعیین شده"
            /*
             * reference_id: true // شماره سفارش
                param1: شماره برگ خروج
                param2:  حداکثر زمان برگشت
                param3:
                param4:
              */
        ],

        ["id" => 16, "code" => "1016", "caption" => "ثبت تردد"
            /*
               param1:ساعت ورود صحیح
               param2:ساعت خروج صحیح
               param3:
               param4:
             */
        ],

        ["id" => 17, "code" => "1017", "caption" => "جایگزین بسته بندی در تخصیص به پیمانکار"
            /*
               ref:    کد بسته بندی
               param1: کارت تولید سطح 1
               param2: کارت تولید جایگزین سطح 1
               param3: کانال تولید
                param4: پیمانکار
             */
        ],

    ];
    private $table = 'special_license_types';

    public function run()
    {

        foreach ($this->data as $item) {

            if (!DB::table($this->table)->
            where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            } else {
                DB::table($this->table)->where("id", $item["id"])->update($item);
            }

        }
    }
}
