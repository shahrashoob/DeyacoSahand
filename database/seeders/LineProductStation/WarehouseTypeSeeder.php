<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "انبار کالا"],
        [ "id" => 2, "caption" => "انبارک ماشین"],
        [ "id" => 3, "caption" => "انبارک گروه ماشین"],
        [ "id" => 4, "caption" => "انبارک ایستگاه کاری"],
        [ "id" => 5, "caption" => "انبارک خط تولید"],
        [ "id" => 100, "caption" => "فاقد انبارش"], // کالاهایی که انبارش ندارند در بخش bom این مقدار برای نوع انبار آنها ست می شود.
    ];
    private $table = 'warehouse_types';

    public function run() {

        foreach ( $this->data as $item ) {

            DB::table( $this->table )->delete();
            foreach ( $this->data as $item ) {
                DB::table( $this->table )->insert( $item );
            }

        }
    }
}
