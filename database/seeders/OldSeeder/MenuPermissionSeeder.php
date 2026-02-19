<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuPermissionSeeder extends Seeder
{

    private $table = 'menu_post';

    public function run()
    {
        foreach (DB::table("menus")->get() as $item) {

            // مدیر سیستم
            if (!DB::table($this->table)->where(["menu_id" => $item->id, "post_id" => 2000])->exists())
                DB::table($this->table)->insert(["menu_id" =>  $item->id, "post_id" => 2000]);
        }
    }
}

