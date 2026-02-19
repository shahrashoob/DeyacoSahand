<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "تامین کننده داخل کشور","trans_kind_id"=>100 ],
        [ "id" => 2, "caption" => "تامین کننده خارج از کشور","trans_kind_id"=>1 ],

    ];
    private $table = 'supplier_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
