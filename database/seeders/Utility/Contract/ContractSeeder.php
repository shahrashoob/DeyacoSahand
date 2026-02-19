<?php

namespace Database\Seeders\Utility\Contract;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        ["id" => 1, "caption" => "قراداد کار"],

    ];
    private $table = 'contracts';

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
