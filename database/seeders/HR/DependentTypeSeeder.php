<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DependentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        ["id" => 1, "caption" => "همسر"],
        ["id" => 2, "caption" => "فرند دختر"],
        ["id" => 3, "caption" => "فرزند پسر"],

    ];
    private $table = 'dependent_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }

    }
}
