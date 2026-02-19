<?php

namespace Database\Seeders\Utility\Algorithm;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlgorithmTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 100, "caption" => 'الگوریتم درخواست مواد اولیه' ],
        [ "id" => 200, "caption" => 'الگوریتم تخصیص کارت تولید' ],
        [ "id" => 300, "caption" => 'الگوریتم صدور کارت تولید' ],
        [ "id" => 400, "caption" => 'الگوریتم بارکد' ],
        [ "id" => 500, "caption" => 'الگوریتم روش برنامه ریزی' ],
        [ "id" => 700, "caption" => 'الگوریتم نقطه سفارش' ],
        [ "id" => 800, "caption" => 'الگوریتم LideTime' ],
        


 [ "id" => 600, "caption" => 'الگوریتم داف خروجی ماشین ها' ],
    ];
    private $table = 'algorithm_types';

    public function run() {

        foreach ( $this->data as $item ) {
            if(! DB::table($this->table)->where("id", $item["id"])->first()) {
                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )->where("id", $item["id"])->update($item);
            }
        }

    }
}
