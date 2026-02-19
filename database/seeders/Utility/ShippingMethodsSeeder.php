<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $data = [
        [ "id" => 1, "caption" => "ارسال مستقیم با ماشین" ],
        [ "id" => 2, "caption" => "باربری (حمل توسط شرکت باربری)" ],
        [ "id" => 3, "caption" => "حمل توسط ماشین مشتری" ],
        [ "id" => 4, "caption" => "حمل قراردادی / پیمانی" ],
        [ "id" => 5, "caption" => "حمل ترکیبی (زمینی/ریلی/دریایی)" ],
        [ "id" => 6, "caption" => "پست / تیپاکس (بسته‌های کوچک)" ],
        [ "id" => 7, "caption" => "ناوگان داخلی کارخانه" ],
    ];

    private $table = 'shipping_methods';

    public function run()
    {
        foreach ($this->data as $item) {
            if (!DB::table($this->table)->where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            } else {
                DB::table($this->table)->where("id", $item["id"])->update($item);
            }
        }
    }
}
