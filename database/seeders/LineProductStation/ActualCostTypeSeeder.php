<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActualCostTypeSeeder extends Seeder
{

    private $data = [

        [
            "id"              => 1,
            "caption"=>"قیمت خرید",
        ],
        [
            "id"              => 2,
            "caption"=>"هزینه مواد اولیه مستقیم",
        ],
        [
            "id"              => 3,
            "caption"=>"هزینه نیروی انسانی مستقیم",
        ],
        [
            "id"              => 4,
            "caption"=>"هزینه انرژی",
        ],
        [
            "id"              => 5,
            "caption"=>"هزینه سربار",
        ],
        [
            "id"              => 6,
            "caption"=>"هزینه ترخیص (گمرکی)",
        ],
        [
            "id"              => 7,
            "caption"=>"هزینه پیمانکاری",
        ],
        [
            "id"              => 8,
            "caption"=>"هزینه حمل و نقل",
        ],
        [
            "id"              => 9,
            "caption"=>"هزینه بسته بندی",
        ],
        [
            "id"              => 1000,
            "caption"=>"بهای تمام شده",
        ],
    ];
    private $table = 'actual_cost_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )->where( "id", $item["id"] )->update($item);
            }

        }
    }
}
