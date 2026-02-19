<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfferTypeSeeder extends Seeder
{
    private $data = [
        //
        ["id" => 100, "caption" => 'بدون تخفیف'],
        ["id" => 200, "caption" => ' تخفیف نقدی '],
        ["id" => 300, "caption" => ' تخفیف حجمی '],
        ["id" => 500, "caption" => ' تخفیف درصدی کانال '],
        ["id" => 510, "caption" => ' تخفیف درصدی ویژه مشتریان'],
        ["id" => 600, "caption" => ' تخفیف خاص - مدیر مالی '],

    ];
    private $table = 'order_types';

    public function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {
            DB::table($this->table)->insert($item);
        }
    }
}
