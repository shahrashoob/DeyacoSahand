<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkDayTypeSeeder extends Seeder
{
    var $data = [
        [ "id" => 1, "caption" => "روز کاری"],
        [ "id" => 2, "caption" => "تعطیلی رسمی"],
        [ "id" => 3, "caption" => "تعطیلی غیررسمی"],
        [ "id" => 4, "caption" => "جمعه"],
    ];
    private $table = 'work_day_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }

    }
}
