<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineContourUnitSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => " قطب " ],
    ];
    private $table = 'machine_contour_units';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->exists() ) {
                DB::table( $this->table )->insert( $item );
            }
        }
    }
}
