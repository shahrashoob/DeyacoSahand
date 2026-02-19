<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserDeviceTypeSeeder extends Seeder
{/**
 * Run the database seeds.
 *
 * @return void
 */
    var $data = [
    ["id" => 1, "caption" => "تلفن همراه"],
    ["id" => 2, "caption" => "تبلت"],
    ["id" => 3, "caption" => "دسکتاب"],

];
    private $table = 'user_device_types';

    public function run() {

    foreach ( $this->data as $item ) {

        if ( ! DB::table( $this->table )->
        where( "id", $item["id"] )->first() ) {
            DB::table( $this->table )->insert( $item );
        }

    }

}
}
