<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservoirTypeSeeder extends Seeder
{
    private $data = [
        ["id" => 1, "caption" => "مخزن"],
        ["id" => 2, "caption" => "مخزن روباز "],
    ];
    private $table = 'reservoir_types';

    public function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {
            DB::table($this->table)->insert($item);
        }
    }
}
