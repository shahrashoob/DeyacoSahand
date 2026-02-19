<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LineGroupSeeder extends Seeder
{
    private $data = [
        //
        [ "id" => 99999 , "caption" => 'بدون گروه کالایی' ],
//        [ "id" => 1, "caption" => 'خط بسته بندی شیره' ],
//        [ "id" => 2, "caption" => 'خط کرم' ],
//        [ "id" => 3, "caption" => 'خط روغن' ],
//        [ "id" => 6, "caption" => 'خط حلوا ارده' ],
//        [ "id" => 4, "caption" => 'خط قاووت' ],
//        [ "id" => 5, "caption" => 'خط بسته بندی ارده' ],
////        [ "id" => 7, "caption" => '' ],
//        [ "id" => 8, "caption" => 'خط حلوا شکری' ],
////        [ "id" => 9, "caption" => '' ],
//        [ "id" => 10, "caption" => 'خط کنجد' ],
//        [ "id" => 11, "caption" => 'خط پودر کیک' ],
//        [ "id" => 12, "caption" => 'معجون' ],
//        [ "id" => 13, "caption" => 'خط آجیل و خشکبار' ],
//        [ "id" => 14, "caption" => 'مهربانو' ],
//        [ "id" => 15, "caption" => 'پکیج' ],
//        [ "id" => 16, "caption" => 'نوشیدنی و عرقیجات' ],
//        [ "id" => 17, "caption" => 'شکر' ],
//        [ "id" => 18, "caption" => 'تولید شیره' ],
//        [ "id" => 19, "caption" => 'تولید ارده' ],

    ];
    private $table = 'line_groups';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }

    }
}
