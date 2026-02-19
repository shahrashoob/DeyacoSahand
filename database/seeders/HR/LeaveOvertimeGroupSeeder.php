<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveOvertimeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        [ "id" => 1, "caption" => "مرخصی" ],
        [ "id" => 2, "caption" => "اضافه کاری" ],
        [ "id" => 3, "caption" => "ماموریت" ],
        [ "id" => 4, "caption" => "جایگزینی" ],
        [ "id" => 5, "caption" => "غیبت" ],
    ];
    private $table = 'leave_overtime_groups';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }

    }
}
