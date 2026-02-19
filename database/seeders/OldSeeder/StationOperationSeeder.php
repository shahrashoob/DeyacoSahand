<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StationOperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        [ "id" => 1, "caption" => ' پیش فرض ', "station_id" => -1 ],

    ];
    private $table = 'station_operations';

    public function run() {

        foreach ( $this->data as $item ) {
            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {

                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )->where("id", $item["id"] )->update($item);
            }
        }

        DB::table( $this->table )->where("id", 1 )->delete();
    }
}
