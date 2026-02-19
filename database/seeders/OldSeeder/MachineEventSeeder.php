<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
 // عمومی
        [ "id" => 20,"machine_module_type_id"=>2, "caption" => "ثبت درخواست کالا از انبارک " ],
        [ "id" => 21,"machine_module_type_id"=>2, "caption" => "ثبت مجوز " ],
        [ "id" => 22,"machine_module_type_id"=>2, "caption" => "رد مجوز " ],
        // پارچه خام
        [ "id" => 90,"machine_module_type_id"=>2, "caption" => " وضعیت قبل از تخصیص " ],
        [ "id" => 92,"machine_module_type_id"=>2, "caption" => " تخصیص جدید " ],
        [ "id" => 95,"machine_module_type_id"=>2, "caption" => "  کنسل کردن تخصیص  " ],
        [ "id" => 100,"machine_module_type_id"=>2, "caption" => " شروع مقدمات تغییر کالیته " ],
        [ "id" => 110,"machine_module_type_id"=>2, "caption" => " پایان مقدمات تغییر کالیته " ],
        [ "id" => 120,"machine_module_type_id"=>2, "caption" => " پایان مرحله دوم تغییر کالیته " ],
        [ "id" => 130,"machine_module_type_id"=>2, "caption" => " شروع چله گذاری " ],
        [ "id" => 140,"machine_module_type_id"=>2, "caption" => " پایان چله گذاری " ],
        [ "id" => 150,"machine_module_type_id"=>2, "caption" => " پایان چله گذاری " ],
        [ "id" => 160,"machine_module_type_id"=>2, "caption" => " شروع گره زنی " ],
        [ "id" => 170,"machine_module_type_id"=>2, "caption" => " پایان گره زنی " ],
        [ "id" => 175,"machine_module_type_id"=>2, "caption" => " آماده سازی جهت لامل ریزی " ],
        [ "id" => 180,"machine_module_type_id"=>2, "caption" => " شروع لامل ریزی " ],
        [ "id" => 190,"machine_module_type_id"=>2, "caption" => " پایان لامل ریزی " ],
        [ "id" => 200,"machine_module_type_id"=>2, "caption" => " راه اندازی تغییر کالیته " ],
        [ "id" => 210,"machine_module_type_id"=>2, "caption" => " راه اندازی تغییر کالیته " ],
        [ "id" => 215,"machine_module_type_id"=>2, "caption" => " تایید کنترل کیفیت " ],
        [ "id" => 220,"machine_module_type_id"=>2, "caption" => " عدم تایید کنترل کیفیت " ],
        [ "id" => 230,"machine_module_type_id"=>2, "caption" => "  پایان راه اندازی شیفت " ],
        [ "id" => 235,"machine_module_type_id"=>2, "caption" => "  عدم راه اندازی شیفت " ],
        [ "id" => 238,"machine_module_type_id"=>2, "caption" => "راه اندازی مجدد شیفت " ],
        [ "id" => 240,"machine_module_type_id"=>2, "caption" => " طراحی پایان یافته شد " ],
        [ "id" => 250,"machine_module_type_id"=>2, "caption" => " چله موجود شد " ],
        [ "id" => 255,"machine_module_type_id"=>2, "caption" => " چله تحویل بافندگی شد " ],
        [ "id" => 260,"machine_module_type_id"=>2, "caption" => " دریافت لات نخ پود " ],
        [ "id" => 270,"machine_module_type_id"=>2, "caption" => " درخواست تعویض چله " ],
        [ "id" => 280,"machine_module_type_id"=>2, "caption" => " شروع تعویض چله " ],
        [ "id" => 290,"machine_module_type_id"=>2, "caption" => " پایان تعویض چله " ],
        [ "id" => 300,"machine_module_type_id"=>2, "caption" => " شروع گره زنی تعویض چله " ],
        [ "id" => 310,"machine_module_type_id"=>2, "caption" => " پایان گره زنی تعویض چله " ],
        [ "id" => 320,"machine_module_type_id"=>2, "caption" => " اجرا شدن ماژول تغییر لات پارچه خام" ],
        [ "id" => 330,"machine_module_type_id"=>2, "caption" => "عدم تایید تحویل چله از انبار" ],
        [ "id" => 340,"machine_module_type_id"=>2, "caption" => "اعلام پایان چله" ],
        [ "id" => 350,"machine_module_type_id"=>2, "caption" => "تغییر نخ پود" ],
        [ "id" => 360,"machine_module_type_id"=>2, "caption" => " شروع تعویض چله (جهت تغییر کالیته) " ],
        [ "id" => 370,"machine_module_type_id"=>2, "caption" => " شروع گره زنی (جهت تغییر کالیته) " ],
        [ "id" => 380,"machine_module_type_id"=>2, "caption" => " پایان تعویض چله (جهت تغییر کالیته) " ],
        [ "id" => 390,"machine_module_type_id"=>2, "caption" => " پایان گره زنی (جهت تغییر کالیته) " ],
        [ "id" => 400,"machine_module_type_id"=>2, "caption" => " راه اندازی شیفت (جهت تغییر کالیته) " ],
        [ "id" => 410,"machine_module_type_id"=>2, "caption" => " صدور دستور توقف کارت تولید جاری " ],
        [ "id" => 420,"machine_module_type_id"=>2, "caption" => " شروع تغییر نخ پود (دستور توقف) " ],
        [ "id" => 430,"machine_module_type_id"=>2, "caption" => " پایان تغییر نخ پود (دستور توقف) " ],
        [ "id" => 440,"machine_module_type_id"=>2, "caption" => " استخراج پارچه پایانی (دستور توقف) " ],
        [ "id" => 450,"machine_module_type_id"=>2, "caption" => " استخراج پارچه " ],
        [ "id" => 460,"machine_module_type_id"=>2, "caption" => " استخراج پارچه پایانی" ],
        [ "id" => 470,"machine_module_type_id"=>2, "caption" => "وضعیت نت عادی شد" ],
        [ "id" => 480,"machine_module_type_id"=>2, "caption" => "تایید تحویل چله" ],
        [ "id" => 490,"machine_module_type_id"=>2, "caption" => "شروع استخراج چله (دستور توقف)" ],
        [ "id" => 495,"machine_module_type_id"=>2, "caption" => "پایان استخراج چله (دستور توقف)" ],
        [ "id" => 500,"machine_module_type_id"=>2, "caption" => "تحویل چله به انبار (دستور توقف)" ],
        [ "id" => 510,"machine_module_type_id"=>2, "caption" => "کنسل شدن درخواست تعویض چله" ],
        [ "id" => 520,"machine_module_type_id"=>2, "caption" => "پایان بافت (کارت تولید جاری)" ],
        [ "id" => 530,"machine_module_type_id"=>2, "caption" => "شروع استخراج چله و چله گذاری (جهت تغییر کالیته)" ],
        [ "id" => 540,"machine_module_type_id"=>2, "caption" => "پایان استخراج چله و چله گذاری (جهت تغییر کالیته)" ],
        [ "id" => 550,"machine_module_type_id"=>2, "caption" => "شروع تغییر کالیته" ],
        [ "id" => 551,"machine_module_type_id"=>2, "caption" => "عدم تغییر کالیته" ],
        [ "id" => 560,"machine_module_type_id"=>2, "caption" => "شروع راه اندازی شیفت " ],
        [ "id" => 570,"machine_module_type_id"=>2, "caption" => "پایان تغییر کالیته" ],
        [ "id" => 580,"machine_module_type_id"=>2, "caption" => "ثبت درخواست چله جدید توسط سیستم" ],
        [ "id" => 590,"machine_module_type_id"=>2, "caption" => "تغییر متراژ تخصیص" ],
        [ "id" => 600,"machine_module_type_id"=>2, "caption" => "شروع شانه کشی" ],
        [ "id" => 610,"machine_module_type_id"=>2, "caption" => "پایان شانه کشی" ],
        [ "id" => 620,"machine_module_type_id"=>2, "caption" => "شروع تغییر عرض ماشین" ],
        [ "id" => 630,"machine_module_type_id"=>2, "caption" => "پایان تغییر عرض ماشین" ],
        [ "id" => 640,"machine_module_type_id"=>2, "caption" => "ثبت اپراتور مسئول ماشین" ],
        [ "id" => 641,"machine_module_type_id"=>2, "caption" => "ثبت لوگو" ],
        [ "id" => 650,"machine_module_type_id"=>2, "caption" => "ثبت تحویل شیفت" ],
        [ "id" => 660,"machine_module_type_id"=>2, "caption" => "تایید تحویل شیفت" ],
        [ "id" => 670,"machine_module_type_id"=>2, "caption" => "عدم تایید تحویل شیفت" ],
        [ "id" => 680,"machine_module_type_id"=>2, "caption" => "شروع نمونه گیری" ],
        [ "id" => 690,"machine_module_type_id"=>2, "caption" => "پایان نمونه گیری" ],


        // General module
        [ "id" => 691,"machine_module_type_id"=>2, "caption" => "تزریق مواد اولیه" ],
        [ "id" => 700,"machine_module_type_id"=>2, "caption" => "تایید تحویل مواد اولیه" ],
        [ "id" => 705,"machine_module_type_id"=>2, "caption" => "عدم تایید تحویل مواد اولیه" ],
        [ "id" => 706,"machine_module_type_id"=>2, "caption" => "عدم تایید تحویل مواد اولیه" ],
        [ "id" => 707,"machine_module_type_id"=>2, "caption" => "ثبت درخواست برگشت مواد اولیه" ],
        [ "id" => 708,"machine_module_type_id"=>2, "caption" => "اعلام نقص در ماشین/کالا" ],

        [ "id" => 5310901, "caption" => "شروع ستاپ (setup)", "machine_module_type_id"=>4,  ],
        [ "id" => 5310902, "caption" => "شروع عملیات", "machine_module_type_id"=>4,  ],
        [ "id" => 5310903, "caption" => "پایان عملیات", "machine_module_type_id"=>4,  ],
        [ "id" => 5310904, "caption" => "انجام تنظیمات نهایی", "machine_module_type_id"=>4,  ],
        [ "id" => 5310905, "caption" => "تایید کنترل کیفیت", "machine_module_type_id"=>4,  ],
        [ "id" => 5310906, "caption" => "شروع تست", "machine_module_type_id"=>4,  ],
        [ "id" => 5310907, "caption" => "پایان تست", "machine_module_type_id"=>4,  ],
        [ "id" => 5310908, "caption" => "تخصیص ماژول StartToStart", "machine_module_type_id"=>4,  ],

        [ "id" => 5310920, "caption" => "تزریق مواد اولیه", "machine_module_type_id"=>4,  ],

        //چله کشی matthys

        [ "id" => 1000,"machine_module_type_id"=>3, "caption" => "پایان برگردان" ],
        [ "id" => 1010,"machine_module_type_id"=>3, "caption" => "پایان قفسه گذاری" ],
        [ "id" => 1020,"machine_module_type_id"=>3, "caption" => "شروع چله کشی" ],
        [ "id" => 1030,"machine_module_type_id"=>3, "caption" => "پایان چله کشی" ],
        [ "id" => 1040,"machine_module_type_id"=>3, "caption" => "شروع برگردان" ],
        [ "id" => 1050,"machine_module_type_id"=>2, "caption" => " اجرا شدن ماژول تغییر لات چله" ],
        [ "id" => 1060,"machine_module_type_id"=>3, "caption" => "عدم قفسه گذاری" ],


    ];
    private $table = 'machine_event_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->exists() ) {
                DB::table( $this->table )->insert( $item );
            }
        }
    }
}
