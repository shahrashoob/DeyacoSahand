<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoodsTypeSeeder extends Seeder
{
    private $data = [
        //
        [ "id" => 1, "caption" => 'محصول  ' ],
        [ "id" => 2, "caption" => ' ماده اولیه ' ],

    ];
    private $table = 'goods_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
