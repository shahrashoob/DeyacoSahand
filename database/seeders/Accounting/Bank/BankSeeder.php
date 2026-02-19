<?php

namespace Database\Seeders\Accounting\Bank;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        ["id" => 1, "caption" => " بانک ملّی ایران"],
        ["id" => 2, "caption" => "بانک سپه"],
        ["id" => 3, "caption" => "بانک کشاورزی"],
        ["id" => 4, "caption" => "بانک مسکن"],
        ["id" => 5, "caption" => "بانک توسعه صادرات ایران"],
        ["id" =>6, "caption" => "بانک توسعه تعاون"],
        ["id" => 7, "caption" => "بانک اقتصاد نوین"],
        ["id" => 8, "caption" => "بانک پارسیان"],
        ["id" => 9, "caption" => "بانک پارسیان"],
        ["id" => 10, "caption" => "بانک کارآفرین"],
        ["id" => 11, "caption" => "بانک سامان"],
        ["id" => 12, "caption" => "بانک سینا"],
        ["id" => 13, "caption" => "بانک خاورمیانه"],
        ["id" => 14, "caption" => "بانک شهر"],
        ["id" => 15, "caption" => "بانک دی"],
        ["id" => 16, "caption" => "بانک صادرات"],
        ["id" => 17, "caption" => "بانک ملت"],
        ["id" => 18, "caption" => "بانک تجارت"],
        ["id" => 19, "caption" => "بانک رفاه"],
        ["id" => 20, "caption" => "بانک آینده"],
        ["id" => 21, "caption" => "بانک گردشگری"],
        ["id" => 22, "caption" => "بانک ایران زمین"],
        ["id" => 23, "caption" => "بانک سرمایه"],
        ["id" => 24, "caption" => "بانک پاسارگاد"],
        ["id" => 25, "caption" => "بانک قرض‌الحسنه رسالت"],
        ["id" => 26, "caption" => "بانک قرض‌الحسنه مهر ایران"],
        ["id" => 27, "caption" => "موسسه اعتباری نور"],
        ["id" => 28, "caption" => "موسسه اعتباری ملل"],
        ["id" => 29, "caption" => "موسسه اعتباری نور"],

    ];
    private $table = 'banks';

    public function run()
    {

        foreach ($this->data as $item) {

            if (!DB::table($this->table)->
            where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            }

        }

    }
}
