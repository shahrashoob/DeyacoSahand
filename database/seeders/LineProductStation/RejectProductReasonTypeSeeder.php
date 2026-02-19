<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RejectProductReasonTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [

        ["id" => 1, "caption" => "عدم تطابق رنگ پارچه با رنگ سفارش"],
        ["id" => 2, "caption" => "عدم تطابق زیر دست پارچه با زیر دست سفارش"],
        ["id" => 3, "caption" => "خرابی پارچه به علت خرابی نخ"],
        ["id" => 4, "caption" => "لکه سیاهی و روغن در پارچه"],
        ["id" => 5, "caption" => "سایر"],
    ];
    private $table = 'reject_product_reason_types';

    public function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {
            DB::table($this->table)->insert($item);
        }
    }
}
