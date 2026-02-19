<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftDeliveryModuleSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [
            "id"                                                       => 1,
            "caption"                                                  => "تحویل شیفت بافندگی",
            "presence_in_the_organization_checked"                     => 1,
            "people_who_have_a_delivery_shift_have_permission_to_exit" => 1
        ],
        [
            "id"                                                       => 2,
            "caption"                                                  => "تحویل شیفت نگهبانی",
            "presence_in_the_organization_checked"                     => 0,
            "people_who_have_a_delivery_shift_have_permission_to_exit" => 1
        ],
        [
            "id"                                                       => 3,
            "caption"                                                  => "تحویل شیفت راه انداز",
            "presence_in_the_organization_checked"                     => 0,
            "people_who_have_a_delivery_shift_have_permission_to_exit" => 1
        ],

    ];
    private $table = 'shift_delivery_modules';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }
    }
}
