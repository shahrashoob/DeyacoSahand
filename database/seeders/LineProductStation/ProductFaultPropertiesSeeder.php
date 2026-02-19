<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductFaultPropertiesSeeder extends Seeder
{

    private $data = [
        //

        [ "id" => 1,"caption"=>"موقعیت عرضی کیفی","field_type_id"=>3,"required"=>1],
        [ "id" => 2,"caption"=>"خط برش","field_type_id"=>3,"required"=>1],
        [ "id" => 3,"caption"=>"تکرار","field_type_id"=>1,"required"=>1],
        [ "id" => 4,"caption"=>"شدت","field_type_id"=>3,"required"=>1],
        [ "id" => 5,"caption"=>"موقعیت عرضی","field_type_id"=>4,"required"=>1],
        [ "id" => 6,"caption"=>"توضیحات","field_type_id"=>2,"required"=>0],
        [ "id" => 7,"caption"=>"طول","field_type_id"=>1,"required"=>1],
        [ "id" => 8,"caption"=>"مشهود","field_type_id"=>3,"required"=>1],


    ];
    private $table = 'product_fault_properties';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
