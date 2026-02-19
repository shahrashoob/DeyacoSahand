<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $data = [
        //
        [ "id" => 9,"code"=>1009, "caption" => "میزان کالای استخراج شده","show_in_real_time_dashboard"=>1,"show_in_cross_sectional_dashboard"=>0,"directory_namespace"=>"" ],
        [ "id" => 10,"code"=>1010, "caption" =>"شاخص های بهره وری","show_in_real_time_dashboard"=>1,"show_in_cross_sectional_dashboard"=>0,"directory_namespace"=>"" ],
        [ "id" => 11,"code"=>1010, "caption" =>"میزان کالای تولید شده","show_in_real_time_dashboard"=>1,"show_in_cross_sectional_dashboard"=>0,"directory_namespace"=>"" ],
    ];
    private $table = 'reports';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )-> where( "id",  $item["id"] )->update( $item );
            }

        }
    }
}
