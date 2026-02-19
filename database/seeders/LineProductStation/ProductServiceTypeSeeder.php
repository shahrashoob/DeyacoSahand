<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "کالا"],
        [ "id" => 2, "caption" => "خدمت"],


    ];
    private $table = 'product_service_types';

    public function run() {

            DB::table( $this->table )->delete();
            foreach ( $this->data as $item ) {
                DB::table( $this->table )->insert( $item );
            }


    }
}
