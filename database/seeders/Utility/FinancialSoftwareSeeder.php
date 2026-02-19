<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialSoftwareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "نرم افزار مالی نوسا" ],

    ];
    private $table = 'financial_softwares';

    public function run() {
        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }
    }
}
