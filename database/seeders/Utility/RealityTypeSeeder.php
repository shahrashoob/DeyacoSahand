<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RealityTypeSeeder extends Seeder
{
    private $data = [
        //
        [ "id" => 1, "caption" => "حقیقی" ],
        [ "id" => 2, "caption" => "مجازی" ],

    ];
    private $table = 'reality_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
