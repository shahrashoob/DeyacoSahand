<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineProductPropertySeeder extends Seeder {
    private $data = [

        [
            "id"              => 1,
            "station_id"      => 16,
            "caption"         => 'عرض شانه',
            "min_value"       => 0,
            "max_value"       => 1000,
            "field_type_id"   => 1,
            "special_unit_id" => 998
        ],

        // ماشین جت
        [ "id"              => 2501,
          "caption"         => " سرعت وینچ",
          "station_id"      => 25,
          "min_value"       => 0,
          "max_value"       => 50000,
          "field_type_id"   => 1,
          "special_unit_id" => 0
        ],
        [ "id"              => 2503,
          "caption"         => " گراف",
          "station_id"      => 25,
          "min_value"       => 0,
          "max_value"       => 50000,
          "field_type_id"   => 3,
          "special_unit_id" => 0
        ],


        // ماشین استنتر
        [ "id"              => 2601,
          "caption"         => " عرض",
          "station_id"      => 26,
          "min_value"       => 0,
          "max_value"       => 50000,
          "field_type_id"   => 1,
          "special_unit_id" => 6
        ],
        [ "id"              => 2602,
          "caption"         => " اورفیت",
          "station_id"      => 26,
          "min_value"       => 0,
          "max_value"       => 50000,
          "field_type_id"   => 1,
          "special_unit_id" => 0
        ],
        [ "id"              => 2603,
          "caption"         => " دما",
          "station_id"      => 26,
          "min_value"       => 0,
          "max_value"       => 50000,
          "field_type_id"   => 1,
          "special_unit_id" => 15
        ],
        [ "id"              => 2604,
          "caption"         => " سرعت",
          "station_id"      => 26,
          "min_value"       => 0,
          "max_value"       => 50000,
          "field_type_id"   => 1,
          "special_unit_id" => 17
        ],


        // چله کشی
//        [ "id" => 2100, "caption" => "عرض چله - Beam Width", "station_id"=>21,"min_value"=>0,"max_value"=>50000,"field_type_id"=>1 ,"special_unit_id"=>4],
        [ "id" => 2101, "caption" => "تعداد سرنخ - Number Of Ends", "station_id"=>21,"min_value"=>0,"max_value"=>30000,"field_type_id"=>1 ,"special_unit_id"=>5],
        [ "id" => 2102, "caption" => "تعداد سر نخ هر بند - Ends Per Sections", "station_id"=>21,"min_value"=>0,"max_value"=>1000,"field_type_id"=>1 ,"special_unit_id"=>5],
        [ "id" => 2103, "caption" => "تعداد  بند - Sections", "station_id"=>21,"min_value"=>0,"max_value"=>100,"field_type_id"=>1 ,"special_unit_id"=>5],
        [ "id" => 2104, "caption" => "تعداد  سر نخ بند آخر - Ends Per Last Section", "station_id"=>21,"min_value"=>0,"max_value"=>1000,"field_type_id"=>1 ,"special_unit_id"=>5],
//        [ "id" => 2105, "caption" => "عرض بند - Section Width", "station_id"=>21,"min_value"=>0,"max_value"=>1000,"field_type_id"=>1 ,"special_unit_id"=>20],
        [ "id" => 2106, "caption" => "سرعت چله کشی  - Warping Speed", "station_id"=>21,"min_value"=>0,"max_value"=>1000,"field_type_id"=>1 ,"special_unit_id"=>18],
        [ "id" => 2107, "caption" => "سرعت برگردان - Beaming Speed", "station_id"=>21,"min_value"=>0,"max_value"=>1000,"field_type_id"=>1 ,"special_unit_id"=>18],
//        [ "id" => 2108, "caption" => "نیروی کشش - Pull Force", "station_id"=>21,"min_value"=>0,"max_value"=>50000,"field_type_id"=>1 ,"special_unit_id"=>19],
//        [ "id" => 2109, "caption" => "جابحایی  -Zig-Zag", "station_id"=>21,"min_value"=>0,"max_value"=>50000,"field_type_id"=>1 ,"special_unit_id"=>4],
//        [ "id" => 2110, "caption" => "شیب چله () - Feed(0.0001mm/omw)", "station_id"=>21,"min_value"=>0,"max_value"=>50000,"field_type_id"=>1 ,"special_unit_id"=>5],


    ];
    private $table = 'machine_product_properties';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }
            else{
               // DB::table( $this->table )->where( "id", $item["id"] )->update($item);
            }

        }
    }
}
