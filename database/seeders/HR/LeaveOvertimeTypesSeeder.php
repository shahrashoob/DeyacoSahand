<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveOvertimeTypesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        [ "id" => 1, "caption" => "مرخصی استحقاقی" ,"leave_overtime_group_id"=>1],
        [ "id" => 2, "caption" => " مرخصی اضطراری","leave_overtime_group_id"=>1 ],
        [ "id" => 3, "caption" => "مرخصی استعلاجی","leave_overtime_group_id"=>1 ],
        [ "id" => 4, "caption" => "مرخصی ازدواج","leave_overtime_group_id"=>1 ],
        [ "id" => 5, "caption" => "مرخصی فوت اقوام درجه یک","leave_overtime_group_id"=>1 ],
        [ "id" => 6, "caption" => "مرخصی تشویقی","leave_overtime_group_id"=>1 ],
        [ "id" => 7, "caption" => "مرخصی بدون حقوق","leave_overtime_group_id"=>1 ],

        [ "id" => 200, "caption" => "اضافه کاری","leave_overtime_group_id"=>2 ],
        [ "id" => 300, "caption" => "ماموریت","leave_overtime_group_id"=>3 ],

        [ "id" => 400, "caption" => " جابجایی شیفت","leave_overtime_group_id"=>4 ],
        [ "id" => 401, "caption" => " برگشت جابجایی شیفت","leave_overtime_group_id"=>4 ],


        [ "id" => 501, "caption" => " جانشینی غیبت","leave_overtime_group_id"=>5 ],
    ];
    private $table = 'leave_overtime_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }else{
                DB::table( $this->table ) ->where( "id", $item["id"] )->update( $item );
            }

        }

    }
}
