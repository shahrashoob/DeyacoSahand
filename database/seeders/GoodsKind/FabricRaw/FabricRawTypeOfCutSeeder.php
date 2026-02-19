<?php

namespace Database\Seeders\GoodsKind\FabricRaw;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FabricRawTypeOfCutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $data = [

        [ "id" => 1, "caption" => 'برش از شانه' ],
        [ "id" => 2, "caption" => 'برش از چروک گیر' ],

    ];
    private $table = 'fabric_raw_type_of_cuts';

    public function run() {

        foreach ( $this->data as $item ) {
            if ( ! DB::table( $this->table)->where( [ "id" => $item["id"] ] )->exists() ) {
                DB::table( $this->table )->insert( $item );
            } else {
                DB::table( $this->table )->where( [ "id" => $item["id"] ] )->update( $item );
            }
        }
    }
}
