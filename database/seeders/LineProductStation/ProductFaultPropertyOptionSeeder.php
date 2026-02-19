<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductFaultPropertyOptionSeeder extends Seeder
{

    private $data = [
        //

        [ "id" => 1,"product_fault_property_id"=>1,"caption"=>"حاشیه راست","value"=>1],
        [ "id" => 2,"product_fault_property_id"=>1,"caption"=>"حاشیه چپ","value"=>2],
        [ "id" => 3,"product_fault_property_id"=>1,"caption"=>"1/4 راست","value"=>3],
        [ "id" => 4,"product_fault_property_id"=>1,"caption"=>"1/4 چپ","value"=>4],
        [ "id" => 5,"product_fault_property_id"=>1,"caption"=>"1/2 راست","value"=>5],
        [ "id" => 6,"product_fault_property_id"=>1,"caption"=>"1/2 چپ","value"=>6],
        [ "id" => 7,"product_fault_property_id"=>1,"caption"=>"وسط","value"=>7],




        [ "id" => 4001,"product_fault_property_id"=>4,"caption"=>"خیلی زیاد","value"=>1],
        [ "id" => 4002,"product_fault_property_id"=>4,"caption"=>"زیاد","value"=>2],
        [ "id" => 4003,"product_fault_property_id"=>4,"caption"=>"متوسط","value"=>3],
        [ "id" => 4004,"product_fault_property_id"=>4,"caption"=>"کم","value"=>4],
        [ "id" => 4005,"product_fault_property_id"=>4,"caption"=>"خیلی کم","value"=>5],


        [ "id" => 2001,"product_fault_property_id"=>2,"caption"=>"دارد","value"=>1],
        [ "id" => 2002,"product_fault_property_id"=>2,"caption"=>"ندارد","value"=>2],

        [ "id" => 8001,"product_fault_property_id"=>2,"caption"=>"مشهود","value"=>1],
        [ "id" => 8002,"product_fault_property_id"=>2,"caption"=>"نا مشهود","value"=>2],

    ];
    private $table = 'product_fault_property_options';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
