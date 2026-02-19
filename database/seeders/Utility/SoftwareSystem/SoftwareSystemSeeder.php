<?php

namespace Database\Seeders\Utility\SoftwareSystem;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoftwareSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        ["id" => 1, "caption" => "سازمان دیجیتال دیاکو", "directory" => "Deyaco","route"=>"deyaco"],

    ];
    private $table = 'software_systems';

    public function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {

            if (!DB::table($this->table)->
            where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            } else {
                DB::table($this->table)->where("id", $item["id"])->update($item);
            }

        }

    }
}
