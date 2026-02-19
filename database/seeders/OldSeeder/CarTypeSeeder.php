<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        [ "id" => 100, "caption" => 'خاور مسقف',          "min_weight"=>2,   "max_weight"=>3,   "min_volume"=>0, "max_volume"=>22 ],
        [ "id" => 200, "caption" => 'کامیونت ایسوزو',     "min_weight"=>3,   "max_weight"=>6,   "min_volume"=>0, "max_volume"=>28 ],
        [ "id" => 300, "caption" => 'کامیونت بنز خاور',   "min_weight"=>2.5, "max_weight"=>4,   "min_volume"=>0, "max_volume"=>22 ],
        [ "id" => 400, "caption" => 'کامیونت هیوندا',     "min_weight"=>3.5, "max_weight"=>7,   "min_volume"=>0, "max_volume"=>30 ],
        [ "id" => 500, "caption" => 'وانت نیسان',         "min_weight"=>2.5, "max_weight"=>3,   "min_volume"=>0, "max_volume"=>5 ],
        [ "id" => 600, "caption" => 'وانت مزدا',          "min_weight"=>1,   "max_weight"=>1.2, "min_volume"=>0, "max_volume"=>3 ],
        [ "id" => 700, "caption" => 'وانت پیکان',         "min_weight"=>0.6, "max_weight"=>0.7, "min_volume"=>0, "max_volume"=>2 ],
        [ "id" => 800, "caption" => 'گاری دستی',          "min_weight"=>0.2, "max_weight"=>0.3, "min_volume"=>0, "max_volume"=>1.5 ],
        [ "id" => 900, "caption" => 'سواری شخصی',         "min_weight"=>0.05,"max_weight"=>0.15,"min_volume"=>0, "max_volume"=>0.6 ],
        [ "id" => 1000,"caption" => 'تریلی 18 چرخ',       "min_weight"=>22,  "max_weight"=>25,  "min_volume"=>0, "max_volume"=>90 ],
        [ "id" => 1100,"caption" => 'لیفتراک',            "min_weight"=>1,   "max_weight"=>5,   "min_volume"=>0, "max_volume"=>0 ],
        [ "id" => 1200,"caption" => 'بنز تک',             "min_weight"=>8,   "max_weight"=>10,  "min_volume"=>0, "max_volume"=>35 ],
        [ "id" => 1300,"caption" => 'بنز جفت (10 چرخ)',   "min_weight"=>15,  "max_weight"=>20,  "min_volume"=>0, "max_volume"=>45 ],
        [ "id" => 1400,"caption" => 'کامیونت بنز 911',    "min_weight"=>4,   "max_weight"=>5,   "min_volume"=>0, "max_volume"=>22 ],
        [ "id" => 1500,"caption" => 'اویکو تک',    "min_weight"=>4,   "max_weight"=>5,   "min_volume"=>0, "max_volume"=>22 ],
        [ "id" => 1600,"caption" => 'کامیونت بهمن',    "min_weight"=>4,   "max_weight"=>5,   "min_volume"=>0, "max_volume"=>22 ],
        [ "id" => 1700,"caption" => 'کامیونت فوتون',    "min_weight"=>4,   "max_weight"=>5,   "min_volume"=>0, "max_volume"=>22 ],
        [ "id" => 1800,"caption" => 'کامیونت جک',    "min_weight"=>4,   "max_weight"=>5,   "min_volume"=>0, "max_volume"=>22 ],
    ];

    private $table = 'car_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
