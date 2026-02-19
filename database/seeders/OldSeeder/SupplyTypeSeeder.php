<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplyTypeSeeder extends Seeder
{
    private $data = [
        //
        [ "id" => 1, "caption" => 'تولید داخلی ' ],
        [ "id" => 2, "caption" => ' سفارش خرید' ],
        [ "id" => 3, "caption" => 'تولید توسط پیمانکار' ],
        [ "id" => 4, "caption" => 'دریافت امانی' ],

    ];
    private $table = 'supply_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
