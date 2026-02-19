<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineTypeConsumptionTypeSeeder extends Seeder
{
    private $data = [

        [ "id" => 1, "caption" => 'ثبت مصرف با توجه به کنتور ماشین' ],
        [ "id" => 2, "caption" => 'ثبت مصرف با توجه به مقدار تولید شده' ],

    ];
    private $table = 'machine_type_consumption_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }
    }
}
