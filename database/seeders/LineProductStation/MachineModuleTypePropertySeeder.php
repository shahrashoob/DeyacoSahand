<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineModuleTypePropertySeeder extends Seeder
{
    private $data = [

        [
            "id"              => 70030021401,
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "button_id"=>700300214, // پایان راه اندازی شیفت
            "caption"         => 'مقداری از پارچه که باید در راه اندازی شیفت باید بافته شود',
            "min_value"       => 0,
            "max_value"       => 1000,
            "special_unit_id"   =>6,// سانتی متر,
            "priority_number" => 1,
            "field_type_id"=>1,
        ],
        [
            "id"              => 70030021402,
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "button_id"=>700300214, // پایان راه اندازی شیفت
            "caption"         => 'مقدار خطای کنتور ماشین در راه اندازی شیفت',
            "min_value"       => 0,
            "max_value"       => 1000,
            "special_unit_id"   => 997, // پیک
            "priority_number" => 1,
            "field_type_id"=>1,
        ],
        [
            "id"              => 70030021403,
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "button_id"=>700300214, // پایان راه اندازی شیفت
            "caption"         => 'پست های ارسال پیامک عدم راه اندازی شیفت و عدم تغییر کالیته<br/>'."(لیست پست ها را با <b>-</b> ازهم جداکنید.) ",
            "min_value"       => 0,
            "max_value"       => 1000,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],
        [
            "id"              => 70030021404,
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "button_id"=>700300214, // پایان راه اندازی شیفت
            "caption"         => "اگر واحد فرعی 2 کالا قاب است، حداقل تعداد قاب که باید بافته شود چقدر است",
            "min_value"       => 1,
            "max_value"       => 10,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],
        [
            "id"              => 70030021405,
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "button_id"=>700300214, // پایان راه اندازی شیفت
            "caption"         => "در صورتی که واحد فرعی 2، یک کالا قاب است، <br/>حداقل مقدار راه اندازی ( 1- تعداد قاب 2- مقدار راه اندازی) باشد.",
            "min_value"       => 1,
            "max_value"       => 2,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],

        // چله کشی matthys
        [
            "id"              => 72030011201,
            "machine_module_type_id"      => 3, // ماژول های ماشین چله کشی matthys
            "button_id"=>720300112, // عدم قفسه گذاری
            "caption"         => 'پست های ارسال پیامک عدم قفسه گذاری<br/>'."(لیست پست ها را با <b>-</b> ازهم جداکنید.) ",
            "min_value"       => 0,
            "max_value"       => 1000,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],

        // چله کشی karl_mayer
        [
            "id"              => 72030011202,
            "machine_module_type_id"      => 6, // ماژول های ماشین چله کشی matthys
            "button_id"=>720300112, // عدم قفسه گذاری
            "caption"         => 'پست های ارسال پیامک عدم قفسه گذاری<br/>'."(لیست پست ها را با <b>-</b> ازهم جداکنید.) ",
            "min_value"       => 0,
            "max_value"       => 1000,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],

        // مازول عمومی تکمیل
        [
            "id"              => 73030011201,
            "machine_module_type_id"      => 4,
            "button_id"=>730300201, // شروع ستاپ
            "caption"         => "آیا مقدار فرم تولید با تزریق مواد اولیه تکمیل می شود؟ (0: خیر، 1: بله)",
            "min_value"       => 0,
            "max_value"       => 1,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],
        [
            "id"              => 73030011202,
            "machine_module_type_id"      => 4,
            "button_id"=>730300201, // شروع ستاپ
            "caption"         => "آیا در این گروه ماشین،  ماژول ثبت تولید وجود دارد؟ (0: خیر، 1: بله)",
            "min_value"       => 0,
            "max_value"       => 1,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],
        [
            "id"              => 73030011203,
            "machine_module_type_id"      => 4,
            "button_id"=>730300201, // شروع ستاپ
            "caption"         => "آیا ماهیت کالا در ماشین تغییر می کند؟ (0: خیر، 1: بله)",
            "min_value"       => 0,
            "max_value"       => 1,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],

        [
            "id"              => 73030011204,
            "machine_module_type_id"      => 4,
            "button_id"=>730300201, // شروع ستاپ
            "caption"         => "آیا تزریق مواد اولیه به صورت پیوست با ماشین قبل می باشد؟ (0: خیر، 1: بله)",
            "min_value"       => 0,
            "max_value"       => 1,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],

        [
            "id"              => 73030011205,
            "machine_module_type_id"      => 4,
            "button_id"=>730300201, // شروع ستاپ
            "caption"         => "آیا آیتم های مواد اولیه دقیقا در فرم تولید منعکس می گردد؟<br/> (0: خیر (مقدار سرجمع کل فرم تولید/برگ خروج به آیتم فرم تولید اضافه می گردد.)، 1: بله)",
            "min_value"       => 0,
            "max_value"       => 1,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],

        [
            "id"              => 73030011206,
            "machine_module_type_id"      => 4,
            "button_id"=>730300201, // شروع ستاپ
            "caption"         => "آیا بتواند آیتم های تخصیص که در یک فرم تولید وجود دارند را به صورت مجزا تخصیص دهد؟ (0: خیر، 1: بله)",
            "min_value"       => 0,
            "max_value"       => 1,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],

        [
            "id"              => 73030011207,
            "machine_module_type_id"      => 4,
            "button_id"=>730300201, // شروع ستاپ
            "caption"         => " الگوریتم تشخیص کارت هایی که با هم می توانند تخصیص داده شوند:<br/> (1: بر اساس تشابه مسیر محصول، 2: بر اساس تشابه کالا, 3:  بر اساس تشابه کلا و بسته بندی مجاز)",
            "min_value"       => 1,
            "max_value"       => 3,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],

        [
            "id"              => 73030011208,
            "machine_module_type_id"      => 4,
            "button_id"=>730300201, // شروع ستاپ
            "caption"         => " آیا نیاز است تا مقدار نهایی فرم تولید در پایان عملیات بروز رسانی شود؟ (0: خیر، 1: بله) ",
            "min_value"       => 1,
            "max_value"       => 3,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],

        [
            "id"              => 73030011209,
            "machine_module_type_id"      => 4,
            "button_id"=>730300201, // شروع ستاپ
            "caption"         => "  محاسبه مقدار مواد اولیه براساس مقدار تخصیص (1) است <br/> مقدار فرم تولید استخراج شده از ماشین قبل (2) <br/> بر اساس مقدار مواد اولیه تحویل داده شده به ماشین (3) ",
            "min_value"       => 1,
            "max_value"       => 3,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],

        [
            "id"              => 73030011210,
            "machine_module_type_id"      => 4,
            "button_id"=>730300201, // شروع ستاپ
            "caption"         => "آیا مقدار بسته بندی های خارج شده از ماشین نهایی هستند (1)<br/> یا باید مقدار آنها با توجه به مقدار تخصیص نرمالایز شود (2) <br/>به این صورت که جمع کل مقدار نهایی برابر شود با مقدار تخصیص",
            "min_value"       => 1,
            "max_value"       => 3,
            "special_unit_id"   => 1001, // فاقد واحد
            "priority_number" => 1,
            "field_type_id"=>2,
        ],



    ];
    private $table = 'machine_module_type_properties';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )->where( "id", $item["id"] )->update($item);
            }

        }
    }
}
