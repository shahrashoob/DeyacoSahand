<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionTypeSeeder extends Seeder
{
    private $data = [

        [ "id" => 1, "caption" => 'تولیدی' ],
        [ "id" => 2, "caption" => 'نمونه گیری' ],

    ];
    private $table = 'production_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }
    }
}

