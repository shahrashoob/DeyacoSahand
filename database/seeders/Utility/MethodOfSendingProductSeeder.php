<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MethodOfSendingProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "تحویل به صورت حضوری",  ],
        [ "id" => 2, "caption" =>"خدمات پستی",  ],

    ];
    private $table = 'method_of_sending_products';

    public function run() {

        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
