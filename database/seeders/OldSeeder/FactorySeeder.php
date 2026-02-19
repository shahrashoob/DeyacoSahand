<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FactorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        ["id" => 1, "company_id" => 1, "caption" => " کارخانه"],
        ["id" => 2, "company_id" => 1, "caption" => " کارخانه(1)"],
        ["id" => 3, "company_id" => 1, "caption" => " کارخانه(2)"],
        ["id" => 4, "company_id" => 1, "caption" => " کارخانه(3)"],
        ["id" => 5, "company_id" => 1, "caption" => " کارخانه(4)"],
        ["id" => 6, "company_id" => 1, "caption" => " کارخانه(5)"],
        ["id" => 7, "company_id" => 1, "caption" => " کارخانه(6)"],
        ["id" => 8, "company_id" => 1, "caption" => " کارخانه(7)"],
        ["id" => 9, "company_id" => 1, "caption" => " کارخانه(8)"],
        ["id" => 10, "company_id" => 1, "caption" => " کارخانه(9)"],
        ["id" => 11, "company_id" => 1, "caption" => " کارخانه(10)"],
        ["id" => 12, "company_id" => 1, "caption" => " کارخانه(11)"],
        ["id" => 20, "company_id" => 2, "caption" => 'کارخانه آریا'],

    ];
    private $table = 'factories';

    public function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {
            DB::table($this->table)->insert($item);
        }
    }
}
