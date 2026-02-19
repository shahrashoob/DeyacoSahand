<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeOfSaleOfProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "فروش عادی"],
        [ "id" => 2, "caption" => "فروش کارمزدی"],


    ];
    private $table = 'type_of_sale_of_products';

    public function run() {

        foreach ( $this->data as $item ) {

            DB::table( $this->table )->delete();
            foreach ( $this->data as $item ) {
                DB::table( $this->table )->insert( $item );
            }

        }
    }
}
