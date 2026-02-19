<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketTypeSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $data = [
        //
        [
            "id"          => 10,
            "caption"     => 'درخواست رفع عیب ماشین آلات',
            "code"        => "R",
            "description" => "در صورتی که ماشین آلات نیاز به تعمیر دارند و یا نیاز به تعویض قطعه دارند در این  پیام بگذارید.",
            "has_machine"=> 1,
            "has_ic"=> 1,
        ],

    ];
    private $table = 'ticket_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }

}
