<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BOMItemEnteringTypeSeeder extends Seeder
{

    private $data = [

        [
            "id"              => 1,
            "caption"=>"ناپیوسته (با بسته بندی)",
        ],
        [
            "id"              => 2,
            "caption"=>"پیوسته ( از مخزن یا منابع فاقد انبارش)",
        ],
    ];
    private $table = 'bill_of_material_entering_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )->where( "id", $item["id"] )->update($item);
            }

        }
    }
}
