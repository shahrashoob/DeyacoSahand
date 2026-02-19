<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftWorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [

        [ "id" => 1, "caption" => "گروه شیفت 1" ],
        [ "id" => 2, "caption" => "گروه شیفت 2" ],
        [ "id" => 3, "caption" => "گروه شیفت 3" ],
        [ "id" => 4, "caption" => "گروه شیفت 4" ],
        [ "id" => 5, "caption" => "گروه شیفت 5" ],
        [ "id" => 6, "caption" => "گروه شیفت 6" ],
    ];
    private $table = 'shift_works';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->exists() ) {
                DB::table( $this->table )->insert( $item );
            }else{
                DB::table( $this->table )->where("id",$item["id"])->update($item);
            }
        }
    }
}
