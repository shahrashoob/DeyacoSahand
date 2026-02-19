<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FloatingPostTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        ["id" => 1, "caption" => "پست (های) درخواست دهنده"],
        ["id" => 2, "caption" => "پست مافوق سطح 1"],
        ["id" => 3, "caption" => "پست مافوق سطح 2"],
        ["id" => 4, "caption" => "پست مافوق سطح 3"],
        ["id" => 5, "caption" => "پست مافوق سطح 4"],
        ["id" => 6, "caption" => "پست مافوق سطح 5"],
    ];
    private $table = 'floating_post_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }

    }
}
