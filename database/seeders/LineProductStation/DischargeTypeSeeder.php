<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DischargeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        ['id' => 1, 'caption' => 'دقیقا لایفو (LiFo)'],
        ['id' => 2, 'caption' => 'دقیقا فایفو (FiFo)'],
        ['id' => 3, 'caption' => 'تقریبا لایفو (LiFo)'],
        ['id' => 4, 'caption' => 'تقریبا فایفو (FiFo)'],
        ['id' => 5, 'caption' => 'درهم'],

    ];
    private $table = 'discharge_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            } else {
                DB::table( $this->table )->where( "id", $item["id"] )->update( $item );
            }

        }
    }
}
