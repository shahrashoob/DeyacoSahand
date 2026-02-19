<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarrierGroupSeeder extends Seeder
{
    private $data = [
        ["id" => 1, "caption" => "غلطک چله"],
        ["id" => 2, "caption" => "پالت "],
        ["id" => 3, "caption" => "بوبین"],
        ["id" => 4, "caption" => "غلطک پارچه خام"],
        ["id" => 5, "caption" => "گونی"],
        ["id" => 6, "caption" => "کارتن"],
        ["id" => 7, "caption" => "لوله مقوایی"],
        ["id" => 8, "caption" => "لاکاری"],
        ["id" => 9, "caption" => "بشکه"],
        ["id" => 10, "caption" => "کیسه"],
        ["id" => 11, "caption" => "وان / خرک"],
        ["id" => 12, "caption" => "'گاری'"],
        ["id" => 13, "caption" => "'قیف'"],
    ];
    private $table = 'carrier_groups';

    public function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {
            DB::table($this->table)->insert($item);
        }
    }
}
