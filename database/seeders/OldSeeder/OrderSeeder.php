<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class   OrderSeeder extends Seeder
{
    private $data = [
        //
        [ "code" => 1, "series" => 1 , "customer_id"=>0 ], // سفارش هایی که از قبل وارد شدند
        [ "code" => 2, "series" => 10 , "customer_id"=>0 ], // سفارش های دستی
        [ "code" => 3, "series" => 20 , "customer_id"=>0 ], // ویژه دوره پیاده سازی، کد سفارش از کاربر دریافت شده است.

    ];
    private $table = 'orders';

    public function run() {

        foreach ( $this->data as $item ) {

            if(!DB::table( $this->table )->
                    where( "code","like",$item["code"] )->
                    where("series",$item["series"])->first()){
                DB::table( $this->table )->insert( $item );
            }

        }
    }
}
