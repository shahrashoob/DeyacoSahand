<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileTypeSeeder extends Seeder
{
    private $data = [
        //
        ["id" => 10, "caption" => 'فایلهای مربوط به رویداد'],
        ["id" => 20, "caption" => 'تصویر شخصی'],
        ["id" => 30, "caption" => 'لوگو شرکت'],
        ["id" => 40, "caption" => 'تصویر در مشخصه های کالا'],
        ["id" => 41, "caption" => 'تصویر  کالا'],
        ["id" => 42, "caption" => 'تصویر نمونه کالا'],
        ["id" => 50, "caption" => 'فایل های مربوط به میز کار'],
        ["id" => 60, "caption" => "فایل متنی آموزش ها"],
        ["id" => 70, "caption" => "فایل فیلم آموزش ها"],
        ["id" => 80, "caption" => "فایل مدرک تحصیلی"],
        ["id" => 90, "caption" => "مدارک استخدامی"],
        ["id" => 100, "caption" => 'موارد دیگر'],
        ["id" => 110, "caption" => 'تصویر تاییدیه مشتری'],

    ];
    private $table = 'file_types';

    public function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {
            DB::table($this->table)->insert($item);
        }
    }
}
