<?php

namespace Database\Seeders\Utility\Contract;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContractTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        ["id" => 1, "caption" => "کارمند", ],
        ["id" => 2, "caption" => "مشتری", ],
        ["id" => 3, "caption" => "تامین کننده", ],
        ["id" => 4, "caption" => "پیمانکار", ],

    ];
    private $table = 'contract_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            } else {
                DB::table( $this->table )->where( "id", $item["id"] )->update( $item );
            }

        }
    }
}
