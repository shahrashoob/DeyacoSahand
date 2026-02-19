<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionAlgorithmTypeSeeder extends Seeder {
    private $data = [
        //
        [ "id" => 1, "caption" => ' بر اساس سفارش ' ],
        [ "id" => 2, "caption" => ' بر اساس مصرف ' ],
        [ "id" => 3, "caption" => ' تولید غیر ارادی ' ],

    ];
    private $table = 'production_algorithm_types';

    public function run() {

        foreach ( $this->data as $item ) {
            if ( ! DB::table( $this->table )->where( [ "id" => $item["id"] ] )->exists() ) {
                DB::table( $this->table )->insert( $item );
            }
        }
    }
}
