<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CooperationTypeSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "کارمند تمام وقت","caption2"=>"همکار"  , "priority_number"=>1],
        [ "id" => 11, "caption" => "کارمند پاره وقت","caption2"=>"همکار" , "priority_number"=>2 ],


        [ "id" => 2, "caption" => " پیمانکار","caption2"=>"پیمانکار" , "priority_number"=>3 ],


        [ "id" => 3, "caption" => "مشتری","caption2"=>"مشتری" , "priority_number"=> 5],

        [ "id" => 6, "caption" => "تامین کننده","caption2"=>"تامین کننده" , "priority_number"=> 7],

        [ "id" => 4, "caption" => "نماینده دیاکو","caption2"=>"نماینده" , "priority_number"=> 9],

        [ "id" => 5, "caption" => "اشیاء هوشمند","caption2"=>"اشیاء هوشمند", "priority_number"=> 10 ],

    ];
    private $table = 'cooperation_types';

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
