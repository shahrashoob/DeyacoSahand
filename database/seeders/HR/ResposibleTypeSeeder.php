<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResposibleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        ["id" => 1, "caption" => "مسئول آموزش"],
        ["id" => 2, "caption" => "مسئول بروزرسانی"],
        ["id" => 3, "caption" => "حضوری"],

    ];
    private $table = 'responsible_types';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }

    }
}
