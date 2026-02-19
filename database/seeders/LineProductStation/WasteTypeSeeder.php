<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WasteTypeSeeder extends Seeder
{
    private $data = [

        [ "id" => 1, "caption" => 'ضایعات حین مصرف' ],
        [ "id" => 2, "caption" => 'ضایعات همراه تولید' ],

    ];
    private $table = 'product_waste_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }
    }
}
