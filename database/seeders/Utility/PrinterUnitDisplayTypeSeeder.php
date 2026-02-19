<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrinterUnitDisplayTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "واحد اصلی" ],
        [ "id" => 2, "caption" =>"واحد فرعی" ],
    ];
    private $table = 'printer_unit_display_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )-> where( "id",  $item["id"] )->update( $item );
            }

        }
    }
}
