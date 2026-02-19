<?php

namespace Database\Seeders\Utility\Notification;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SMSTemplateGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $data = [
        ['id' => 1, 'caption' => 'مدیریت'],
        ['id' => 2, 'caption' => 'اسکریپت'],
        ['id' => 3, 'caption' => 'ورود'],
        ['id' => 4, 'caption' => 'همکاری  با ما'],
        ['id' => 5, 'caption' => 'طراحی کالا'],
        ['id' => 6, 'caption' => 'حضور غیاب'],
        ['id' => 7, 'caption' => 'تولید'],
        ['id' => 8, 'caption' => 'مشتری'],
        ['id' => 9, 'caption' => 'سفارش'],
        ['id' => 10, 'caption' => 'میز کار'],
        ['id' => 11, 'caption' => 'پیمانکار'],


    ];
    private $table = 'sms_template_groups';

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
