<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FactoryEnteryTeypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "ثبت پیمانکاری" ],

    ];
    private $table = 'factory_entry_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
