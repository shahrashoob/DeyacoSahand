<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    private $data = [
        ["id" => 1, "caption" => "ریال", "free_price" => 1],
        ["id" => 2, "caption" => "دلار", "free_price" => 0.01],
    ];
    private $table = 'currencies';

    public function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {
            DB::table($this->table)->insert($item);
        }
    }
}
