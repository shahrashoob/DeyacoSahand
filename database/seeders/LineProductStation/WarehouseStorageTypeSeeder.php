<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseStorageTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "بدون انبارش"],
        [ "id" => 2, "caption" => "انبارش با بسته بندی"],
        [ "id" => 3, "caption" => "انبارش با مخزن"],
        [ "id" => 4, "caption" => "انبارش بدون مخزن و بسته بندی (دپو)"],


    ];
    private $table = 'warehouse_storage_types';

    public function run() {

        foreach ( $this->data as $item ) {

            DB::table( $this->table )->delete();
            foreach ( $this->data as $item ) {
                DB::table( $this->table )->insert( $item );
            }

        }
    }
}
