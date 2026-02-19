<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarrierTypeSeeder_remove extends Seeder {
    /***
     * @var array ا
     * بعد از بالا آمدن ic نیاز به این سیید نداریم.
     */
    private $data = [
        // 100
        [ "id"                  => 1,
          "carrier_group_id"    => 1,
          "caption"             => "غلطک چله دابی/بادامکی",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        // چله آهار شده
        [ "id"                  => 101,
          "carrier_group_id"    => 1,
          "caption"             => "غلطک چله ایتما ",
          "placed_in_warehouse" => 0,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 102,
          "carrier_group_id"    => 1,
          "caption"             => "غلطک چله طول 220cm ",
          "placed_in_warehouse" => 0,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 104,
          "carrier_group_id"    => 1,
          "caption"             => "غلطک چله طول 320cm ",
          "placed_in_warehouse" => 0,
          "min_band_number"     => 2,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],

        // 200 پالت
        [ "id"                  => 2,
          "carrier_group_id"    => 2,
          "caption"             => "پالت چوبی 2.1 متر",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 201,
          "carrier_group_id"    => 2,
          "caption"             => "پالت چوبی 1.8 متر",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 202,
          "carrier_group_id"    => 2,
          "caption"             => "پالت چوبی 1.4 متر",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],

        // 300 دوک و بوبین
        [ "id"                  => 3,
          "carrier_group_id"    => 3,
          "caption"             => "بوبین اپن اند",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],
        [ "id"                  => 301,
          "carrier_group_id"    => 3,
          "caption"             => "بوبین رینگ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],
        [ "id"                  => 302,
          "carrier_group_id"    => 3,
          "caption"             => "بوبین مخروطی مقوایی 185mm  (قطر 68mm)",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],
        [ "id"                  => 307,
          "carrier_group_id"    => 3,
          "caption"             => "بوبین مخروطی مقوایی 171mm  (قطر 70mm)",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],
        [ "id"                  => 308,
          "carrier_group_id"    => 3,
          "caption"             => "بوبین مخروطی مقوایی 160mm  (قطر 65mm)",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],
        [ "id"                  => 309,
          "carrier_group_id"    => 3,
          "caption"             => "بوبین مخروطی مقوایی 115mm  (قطر 117mm)",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],

        [ "id"                  => 303,
          "carrier_group_id"    => 3,
          "caption"             => "بوبین استوانه ای پلاستیکی 290mm  (قطر 75mm)",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],
        [ "id"                  => 304,
          "carrier_group_id"    => 3,
          "caption"             => "بوبین استوانه ای پلاستیکی 200mm  (قطر 73mm)",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],
        [ "id"                  => 305,
          "carrier_group_id"    => 3,
          "caption"             => "بوبین استوانه ای پلاستیکی 240mm  (قطر 60mm)",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],
        [ "id"                  => 306,
          "carrier_group_id"    => 3,
          "caption"             => "بوبین استوانه ای پلاستیکی 175mm  (قطر 72mm)",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],
        [ "id"                  => 310,
          "carrier_group_id"    => 3,
          "caption"             => "بوبین مخروطی پلاستیکی 230mm  (قطر 75mm)",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],


        [ "id"                  => 321,
          "carrier_group_id"    => 3,
          "caption"             => "بوبین استوانه ای مقوایی 115mm  (قطر 123mm)",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],

        [ "id"                  => 4,
          "carrier_group_id"    => 4,
          "caption"             => " غلطک پارچه خام 237cm",
          "placed_in_warehouse" => 0,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 401,
          "carrier_group_id"    => 4,
          "caption"             => " غلطک پارچه خام 358cm",
          "placed_in_warehouse" => 0,
          "min_band_number"     => 2,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],


        [ "id"                  => 500,
          "carrier_group_id"    => 5,
          "caption"             => "گونی",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],
        [ "id"                  => 501,
          "carrier_group_id"    => 5,
          "caption"             => "گونی 75*cm75 ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],
        [ "id"                  => 502,
          "carrier_group_id"    => 5,
          "caption"             => "گونی تست ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],

        //کارتن
        [ "id"                  => 600,
          "carrier_group_id"    => 6,
          "caption"             => "کارتن 30*47*cm48 ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 601,
          "carrier_group_id"    => 6,
          "caption"             => "کارتن 39*40*cm58 ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 602,
          "carrier_group_id"    => 6,
          "caption"             => "کارتن 42*44*cm53 ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 603,
          "carrier_group_id"    => 6,
          "caption"             => "کارتن 30*49*cm49 ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 604,
          "carrier_group_id"    => 6,
          "caption"             => "کارتن 24*43*cm57 ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 605,
          "carrier_group_id"    => 6,
          "caption"             => "کارتن 36*45.5*cm66.5 ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 606,
          "carrier_group_id"    => 6,
          "caption"             => "کارتن 25.5*39.5*cm76.5 ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 607,
          "carrier_group_id"    => 6,
          "caption"             => "کارتن 35*70*cm48 ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 608,
          "carrier_group_id"    => 6,
          "caption"             => "کارتن 33.5*64*cm53 ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],
        [ "id"                  => 609,
          "carrier_group_id"    => 6,
          "caption"             => "کارتن 15*74*cm38.5 ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 1
        ],

        [ "id"                  => 700,
          "carrier_group_id"    => 7,
          "caption"             => "لوله مقوایی طول 150cm قطر 4.5cm ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],
        [ "id"                  => 701,
          "carrier_group_id"    => 7,
          "caption"             => "لوله مقوایی طول 140cm قطر 4.5cm ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],
        [ "id"                  => 801,
          "carrier_group_id"    => 7,
          "caption"             => "لاکاری مقوایی طول 75cm*17cm ",
          "placed_in_warehouse" => 1,
          "min_band_number"     => 1,
          "max_band_number"     => 1,
          "min_band_capacity"   => 1,
          "max_band_capacity"   => 1,
          "has_number_ability"  => 0
        ],

        [ "id" => 9900, "carrier_group_id" => 99, "caption" => "فاقد حامل", "placed_in_warehouse" => 1 ],


    ];
    private $table = 'carrier_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }

        DB::table( $this->table )->
        where( "id", 9900 )->delete();
    }
}
