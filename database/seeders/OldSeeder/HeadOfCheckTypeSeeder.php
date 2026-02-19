<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HeadOfCheckTypeSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => 'یک ماه' ],
        [ "id" => 2, "caption" => 'دو ماه' ],
        [ "id" => 3, "caption" => 'سه ماه' ],

    ];
    private $table = 'head_of_check_types';

    public function run() {
        foreach ( $this->data as $item ) {
            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->exists() ) {
                DB::table( $this->table )->insert( $item );
            } else {
                DB::table( $this->table )->where("id", $item["id"] )->update($item);
            }
        }
    }
}
