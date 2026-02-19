<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [

        [ "id" => 1, "caption" => "شیفت پیش فرض اداری 8 - 15:30" ,"active_status_id"=>1210,"number_of_shift_work"=>1],

    ];
    private $table = 'shifts';

    public function run() {

        foreach ( $this->data as $item ) {
            if ( ! DB::table( $this->table )->exists() ) {
                DB::table( $this->table )->insert( $item );
            }
        }
    }
}
