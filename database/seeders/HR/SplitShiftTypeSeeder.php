<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SplitShiftTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        [ "id" => 1, "caption" => "صبح کار","split_shift_type_group_id"=>1,"start_time"=>"07:00:00","end_time"=>"15:00:00" ],
        [ "id" => 2, "caption" => "بعدازظهر کار","split_shift_type_group_id"=>2,"start_time"=>"15:00:00","end_time"=>"22:00:00" ],
        [ "id" => 3, "caption" => "شب کار (بخش اول)","split_shift_type_group_id"=>3,"start_time"=>"22:00:00","end_time"=>"24:00:00" ],
        [ "id" => 4, "caption" => " شب کار (بخش دوم)","split_shift_type_group_id"=>3,"start_time"=>"00:00:00","end_time"=>"07:00:00" ],
    ];
    private $table = 'split_shift_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }

    }
}
