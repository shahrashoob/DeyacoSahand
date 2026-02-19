<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellingTypeSeeder extends Seeder {
    private $data = [
        //
        [ "id" => 1, "caption" => "رسمی" ],
        [ "id" => 2, "caption" => "غیر رسمی" ],

    ];
    private $table = 'selling_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
