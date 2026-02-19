<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder {
    private $data = [
        //
        [ "id"             => 100,
          "caption"        => ' گالن ',
          "search_caption" => " گالن ",
          "bach_caption"   => "گالن",
          "measurement"    => "تعداد"
        ],
        [ "id"             => 200,
          "caption"        => 'عدد',
          "bach_caption"   => "تعداد",
          "search_caption" => ' عدد ',
          "measurement"    => "تعداد "
        ],
        [ "id"             => 300,
          "caption"        => "كيلوگرم",
          "search_caption" => " کیلوگرم ",
          "bach_caption"   => "کیلوگرم",
          "measurement"    => "وزن",
          "weight_conversion_rate"=>1
        ],
        [ "id"             => 400,
          "caption"        => "حلب",
          "bach_caption"   => "حلب",
          "search_caption" => " حلب ",
          "measurement"    => "وزن"
        ],
        [ "id"             => 500,
          "caption"        => "كارتن",
          "bach_caption"   => "کارتن",
          "search_caption" => " کارتن ",
          "measurement"    => "تعداد"
        ],
        [ "id"             => 600,
          "caption"        => "ليتر",
          "bach_caption"   => "لیتر",
          "search_caption" => " ليتر ",
          "measurement"    => "حجم"
        ],
        [ "id"             => 700,
          "caption"        => "گرم",
          "bach_caption"   => "گرم",
          "search_caption" => " گرم ",
          "measurement"    => "گزماژ",
          "weight_conversion_rate"=>0.001
        ],
        [ "id"             => 900,
          "caption"        => " بسته ",
          "bach_caption"   => "کارتن",
          "search_caption" => " بسته ",
          "measurement"    => "تعداد"
        ],
        [ "id"             => 1000,
          "caption"        => "رول",
          "bach_caption"   => "کارتن",
          "search_caption" => " رول ",
          "measurement"    => "تعداد"
        ],
        [ "id"             => 1100,
          "caption"        => "متر",
          "bach_caption"   => "متر",
          "search_caption" => " متر ",
          "measurement"    => "متراژ"
        ],
        [ "id"             => 1200,
          "caption"        => "عدل",
          "bach_caption"   => "عدل",
          "search_caption" => " عدل ",
          "measurement"    => "تعداد"
        ],
        [ "id"             => 1300,
          "caption"        => "بوبین",
          "bach_caption"   => "بوبین",
          "search_caption" => " بوبین ",
          "measurement"    => "تعداد"
        ],
        [ "id"             => 1400,
          "caption"        => "قاب",
          "bach_caption"   => "قاب",
          "search_caption" => " قاب ",
          "measurement"    => "قاب"
        ],
    ];
    private $table = 'units';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
