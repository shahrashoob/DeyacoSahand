<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContractorPropertiesSeeder extends Seeder {
    private $data = [
        //
        [
            "id"              => 1,
            "caption"         => "سابقه فعالیت (سال)",
            "min_value"       => 0,
            "max_value"       => 5000,
            "field_type_id"   => 1,
            "priority_number" => 1
        ],
    ];
    private $table = 'contractor_properties';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            } else {
                DB::table( $this->table )->
                where( "id", $item["id"] )->update( $item );
            }

        }
    }
}
