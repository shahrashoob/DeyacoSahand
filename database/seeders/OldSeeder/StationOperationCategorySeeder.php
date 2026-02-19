<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StationOperationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $data = [

        // پارچه خام
        [
            "id"                      => 1001,
            "caption"                 => 'دسته عملیات بافت',
        ],
        // پارچه تکمیل
        [
            "id"           => 2001,
            "caption"      => 'دسته عملیات پیش فرض تکمیل',
        ],
        [
            "id"           => 2003,
            "caption"      => 'دسته عملیات استنتر',
        ],
        [
            "id"           => 2004,
            "caption"      => 'دسته عملیات خارزنی',
        ],
        [
            "id"           => 2005,
            "caption"      => 'دسته عملیات برش',
        ],
        [
            "id"           => 2006,
            "caption"      => 'دسته عملیات شییر',
        ],
        [
            "id"           => 2007,
            "caption"      => 'دسته عملیات رول برگردان',
        ],
        [
            "id"           => 2008,
            "caption"      => 'دسته عملیات بسته بندی',
        ],

        [
            "id"           => 2009,
            "caption"      => 'دسته عملیات عمومی  جت',
        ],
        [
            "id"           => 2010,
            "caption"      => 'دسته عملیات  پنبه ویکوز جت',
        ],
        [
            "id"           => 2011,
            "caption"      => 'دسته عملیات  پلی استر جت',
        ],

        [
            "id"           => 3001,
            "caption"      => 'دسته عملیات  پیش فرض چله کشی',
        ],

        [
            "id"           => 4001,
            "caption"      => 'دسته عملیات تکمیل رنگرزی نخ',
        ],

    ];
    private $table = 'station_operation_categories';

    public function run() {

        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            } else {
                DB::table( $this->table )->where( "id", $item["id"] )->update( $item );
            }

        }
    }
}
