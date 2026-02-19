<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductRequestFormTypeSeeder extends Seeder
{
    private $data = [

        [ "id" => 1, "caption" => 'اتومات' ],
        [ "id" => 2, "caption" => 'دستور واحد فروش' ],

    ];
    private $table = 'product_request_form_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }
    }
}

