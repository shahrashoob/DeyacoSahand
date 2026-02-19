<?php

namespace Database\Seeders\Utility\Warehouse;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseShelvingTypeSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "سایت","sub_caption"=>"سالن"  , "priority_number"=>1],
        [ "id" => 2, "caption" => "سالن","sub_caption"=>"طبقه"  , "priority_number"=>2],
        [ "id" => 3, "caption" => "طبقه","sub_caption"=>"ردیف"  , "priority_number"=>3],
        [ "id" => 4, "caption" => "ردیف","sub_caption"=>"ستون"  , "priority_number"=>4],
        [ "id" => 5, "caption" => "ستون","sub_caption"=>"جایگاه"  , "priority_number"=>5],
        [ "id" => 6, "caption" => "جایگاه","sub_caption"=>""  , "priority_number"=>-1],

    ];
    private $table = 'warehouse_shelving_types';

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
