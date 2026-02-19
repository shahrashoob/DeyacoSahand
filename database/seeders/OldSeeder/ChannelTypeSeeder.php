<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChannelTypeSeeder extends Seeder
{
    private $data = [
        //
//        [ "id" => 1, "caption" => ' شرکت ایساتیس تجارت ' ],
        [ "id" => 2, "caption" => ' کارکنان ' ],
        [ "id" => 3, "caption" => ' مغازه ' ],
//        [ "id" => 4, "caption" => ' فروشگاههای زنجیره ای ' ],
        [ "id" => 5, "caption" => ' نمایندگان ' ],
//        [ "id" => 6, "caption" => ' پارانو ' ],
        [ "id" => 7, "caption" => ' صادرات   ' ],
        [ "id" => 8, "caption" => 'کاتینگ' ],
        [ "id" => 9, "caption" => ' سایر ' ],
        [ "id" => 100, "caption" => ' فاقد اطلاعات کانال توزیع   ' ],

    ];
    private $table = 'channel_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
