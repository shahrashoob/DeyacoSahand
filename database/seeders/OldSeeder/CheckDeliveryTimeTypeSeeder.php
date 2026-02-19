<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CheckDeliveryTimeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        ["id" => 1, "caption" => 'قبل از ارسال بار'],
        ["id" => 2, "caption" => 'بعد از ارسال بار'],

    ];
    private $table = 'check_delivery_time_types';

    public function run()
    {
        foreach ($this->data as $item) {
            if (!DB::table($this->table)->where("id", $item["id"])->exists())
                DB::table($this->table)->insert($item);
        }
    }
}
