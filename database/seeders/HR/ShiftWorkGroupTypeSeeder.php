<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftWorkGroupTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        ["id" => 1, "caption" => "دسته A",],
        ["id" => 2, "caption" => "دسته B"],
        ["id" => 3, "caption" => "دسته C"],
        ["id" => 4, "caption" => "دسته D"],
        ["id" => 5, "caption" => "دسته E"],
        ["id" => 6, "caption" => "دسته F"],
    ];
    private $table = 'shift_work_group_types';

    public function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {

            if (!DB::table($this->table)->
            where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            }

        }

    }
}
