<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ButtonPostSeeder extends Seeder
{
    private $data = [
        //
        ["post_id" => 1, "button_name" => "production.cancel"],
//        ["post_id" => 1, "button_name" => "production.edit"],
//        ["post_id" => 1, "button_name" => "production.export.production500"],
//
//        ["post_id" => 1002, "button_name" => "production.cancel"],
//        ["post_id" => 1002, "button_name" => "production.edit"],
//        ["post_id" => 1002, "button_name" => "production.export.production500"],

    ];
    private $table = 'button_post';

    public function run()
    {
        if (!DB::table($this->table)->where(["button_id" =>120, "post_id" => 1001])->exists())
            DB::table($this->table)->insert(["button_id" => 120, "post_id" => 1001]);

        if (!DB::table($this->table)->where(["button_id" =>127, "post_id" => 1001])->exists())
            DB::table($this->table)->insert(["button_id" => 127, "post_id" => 1001]);


        if (!DB::table($this->table)->where(["button_id" =>120, "post_id" => 2000])->exists())
            DB::table($this->table)->insert(["button_id" => 120, "post_id" => 2000]);

        if (!DB::table($this->table)->where(["button_id" =>127, "post_id" => 2000])->exists())
            DB::table($this->table)->insert(["button_id" => 127, "post_id" => 2000]);
    }
}
