<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    private $data = [
        //
//        [ "id" => 1, "code" => '1',"caption" => 'انبار 1',"factory_id"=>1 ],
//        [ "id" => 2, "code" => '2',"caption" => 'انبار 2',"factory_id"=>1 ],
//        [ "id" => 3, "code" => '3',"caption" => ' انبار رنگ و مواد شیمیایی',"factory_id"=>1 ],
//        [ "id" => 4, "code" => '4',"caption" => 'انبار 4',"factory_id"=>1 ],
//        [ "id" => 5, "code" => '5',"caption" => 'انبار 5',"factory_id"=>1 ],
//        [ "id" => 6, "code" => '6',"caption" => 'انبار 6',"factory_id"=>1 ],
//        [ "id" => 7, "code" => '7',"caption" => 'انبار 7',"factory_id"=>1 ],
//        [ "id" => 8, "code" => '8',"caption" => 'انبار 8',"factory_id"=>1 ],
//        [ "id" => 9, "code" => '9',"caption" => 'انبار 9',"factory_id"=>1 ],
//        [ "id" => 10, "code" => '100',"caption" => 'انبار 100',"factory_id"=>1 ],
//        [ "id" => 65, "code" => '65',"caption" => 'انبار واسط',"factory_id"=>1 ],

    ];
    private $table = 'warehouses';

    public function run() {

        foreach ( $this->data as $item ) {
           if(!DB::table( $this->table )->where("id",$item["id"])->exists())
                DB::table( $this->table )->insert( $item );
        }
    }
}
