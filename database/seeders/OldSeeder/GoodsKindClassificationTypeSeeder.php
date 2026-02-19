<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoodsKindClassificationTypeSeeder extends Seeder
{
    private $data = [
        //
        ["id" => 1, "caption" => 'اصلی'],
        ["id" => 2, "caption" => 'فرعی'],

    ];
    private $table = 'goods_kind_classification_types';

    public function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {
            DB::table($this->table)->insert($item);
        }
    }
}

