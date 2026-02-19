<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineProductPropertyOptionSeeder extends Seeder
{
    private $data = [

        // گراف
        ["id" => 1, "caption" => "گراف 1", "machine_product_property_id" => 2503],
        ["id" => 2, "caption" => "گراف 2", "machine_product_property_id" => 2503],
        ["id" => 3, "caption" => "گراف 3", "machine_product_property_id" => 2503],
        ["id" => 4, "caption" => "گراف 4", "machine_product_property_id" => 2503],
        ["id" => 5, "caption" => "گراف 5", "machine_product_property_id" => 2503],
        ["id" => 6, "caption" => "گراف 6", "machine_product_property_id" => 2503],
        ["id" => 7, "caption" => "گراف 7", "machine_product_property_id" => 2503],
        ["id" => 8, "caption" => "گراف 8", "machine_product_property_id" => 2503],
        ["id" => 9, "caption" => "گراف 9", "machine_product_property_id" => 2503],
        ["id" => 10, "caption" => "گراف 10", "machine_product_property_id" => 2503],
        ["id" => 11, "caption" => "گراف 11", "machine_product_property_id" => 2503],
        ["id" => 12, "caption" => "گراف 12", "machine_product_property_id" => 2503],
        ["id" => 13, "caption" => "گراف 13", "machine_product_property_id" => 2503],
        ["id" => 14, "caption" => "گراف 14", "machine_product_property_id" => 2503],
        ["id" => 15, "caption" => "گراف 15", "machine_product_property_id" => 2503],
        ["id" => 16, "caption" => "گراف 16", "machine_product_property_id" => 2503],
        ["id" => 17, "caption" => "گراف 17", "machine_product_property_id" => 2503],
        ["id" => 18, "caption" => "گراف 18", "machine_product_property_id" => 2503],
        ["id" => 19, "caption" => "گراف 19", "machine_product_property_id" => 2503],
        ["id" => 20, "caption" => "گراف 20", "machine_product_property_id" => 2503],
        ["id" => 21, "caption" => "گراف 21", "machine_product_property_id" => 2503],
        ["id" => 22, "caption" => "گراف 22", "machine_product_property_id" => 2503],
        ["id" => 23, "caption" => "گراف 23", "machine_product_property_id" => 2503],
        ["id" => 24, "caption" => "گراف 24", "machine_product_property_id" => 2503],
        ["id" => 25, "caption" => "گراف 25", "machine_product_property_id" => 2503],
        ["id" => 26, "caption" => "گراف 26", "machine_product_property_id" => 2503],
        ["id" => 27, "caption" => "گراف 27", "machine_product_property_id" => 2503],
        ["id" => 28, "caption" => "گراف 28", "machine_product_property_id" => 2503],
        ["id" => 29, "caption" => "گراف 29", "machine_product_property_id" => 2503],
        ["id" => 30, "caption" => "گراف 30", "machine_product_property_id" => 2503],
        ["id" => 31, "caption" => "گراف 31", "machine_product_property_id" => 2503],
        ["id" => 32, "caption" => "گراف 32", "machine_product_property_id" => 2503],
        ["id" => 33, "caption" => "گراف 33", "machine_product_property_id" => 2503],
        ["id" => 34, "caption" => "گراف 34", "machine_product_property_id" => 2503],
        ["id" => 35, "caption" => "گراف 35", "machine_product_property_id" => 2503],
        ["id" => 36, "caption" => "گراف 36", "machine_product_property_id" => 2503],
        ["id" => 37, "caption" => "گراف 37", "machine_product_property_id" => 2503],
        ["id" => 38, "caption" => "گراف 38", "machine_product_property_id" => 2503],
        ["id" => 39, "caption" => "گراف 39", "machine_product_property_id" => 2503],
        ["id" => 40, "caption" => "گراف 40", "machine_product_property_id" => 2503],
        ["id" => 41, "caption" => "گراف 41", "machine_product_property_id" => 2503],
        ["id" => 42, "caption" => "گراف 42", "machine_product_property_id" => 2503],
        ["id" => 43, "caption" => "گراف 43", "machine_product_property_id" => 2503],
        ["id" => 44, "caption" => "گراف 44", "machine_product_property_id" => 2503],
        ["id" => 45, "caption" => "گراف 45", "machine_product_property_id" => 2503],
        ["id" => 46, "caption" => "گراف 46", "machine_product_property_id" => 2503],
        ["id" => 47, "caption" => "گراف 47", "machine_product_property_id" => 2503],
        ["id" => 48, "caption" => "گراف 48", "machine_product_property_id" => 2503],
        ["id" => 49, "caption" => "گراف 49", "machine_product_property_id" => 2503],
        ["id" => 50, "caption" => "گراف 50", "machine_product_property_id" => 2503],

    ];
    private $table = 'machine_product_property_options';

    public function run()
    {

        foreach ($this->data as $item) {

            if (!DB::table($this->table)->where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            }

        }
    }
}
