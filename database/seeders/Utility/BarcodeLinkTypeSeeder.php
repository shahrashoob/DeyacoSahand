<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarcodeLinkTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $data = [
        //
        [ "id" => 1, "caption" => 'عضویت مشتری سطح 1' ],
        [ "id" => 2, "caption" => 'عضویت مشتری سطح 2' ],

    ];
    private $table = 'barcode_link_types';

    public function run() {

        foreach ( $this->data as $item ) {
            if(! DB::table($this->table)->where("id", $item["id"])->first()) {
                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )->where("id", $item["id"])->update($item);
            }
        }

    }
}
