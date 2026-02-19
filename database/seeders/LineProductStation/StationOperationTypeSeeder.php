<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StationOperationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "تولید دسته ای (Batch production)"],
        [ "id" => 2, "caption" => "تولید پیوسته (Continuous production)"],


    ];
    private $table = 'station_operation_types';

    public function run() {

        foreach ( $this->data as $item ) {

            DB::table( $this->table )->delete();
            foreach ( $this->data as $item ) {
                DB::table( $this->table )->insert( $item );
            }

        }
    }
}
