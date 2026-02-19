<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;

class FormTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected  $data=[
//        ["id"=> 0 , "caption"=>"برگ خروج"],
        ["id"=>301,"caption"=>"فرم تولید چله کشی"." -حذف "],
        ["id"=>303,"caption"=>"فرم تحویل کالا از انبار"." -حذف "],
        ["id"=>304,"caption"=>"فرم تحویل کالا به انبار - فرم ورود پکینگ لیست"],
        ["id"=>306,"caption"=>"فرم ورود کالای مرجوعی به انبار"],
        ["id"=>401,"caption"=>"فرم تحویل کالا به انبار ( نقل و انتقال از انبارک به انبار و تراکنش اصلاحی)"]
        ];
    public function run()
    {
        //
    }
}
