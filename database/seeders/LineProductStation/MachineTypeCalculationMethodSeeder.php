<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineTypeCalculationMethodSeeder extends Seeder
{

    private $data = [

        [
            "id"              => 1,
            "caption"         => 'ورود توسط اپراتور ',
        ],
        [ "id"              => 2,
          "caption"         => "خوانش کنتور اشیاء",
        ],
        [ "id"              => 3,
          "caption"         => "خوانش از مقدار سامانه",
        ]

    ];
    private $table = 'machine_type_calculation_methods';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )->where( "id", $item["id"] )->update($item);
            }

        }
    }
}
