<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicantTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 10, "caption" => 'درخواست چله از طرف ماشین',"reference_caption"=>"شماره تخصیص" ],
        [ "id" => 20, "caption" => 'درخواست کالا از طرف پیمانکار' ,"reference_caption"=>"شماره دستور پیمان"],
        [ "id" => 30, "caption" => 'درخواست کالا از طرف مشتری',"reference_caption"=>"شماره سفارش" ],
        [ "id" => 40, "caption" => 'درخواست کالا از طرف انبارک های تولید',"reference_caption"=>"شماره درخواست دستیار دیجیتال" ],
        [ "id" => 50, "caption" => 'تامین کننده',"reference_caption"=>"تامین کننده" ],
        [ "id" => 60, "caption" => ' درخواست کالا از طرف تامین کننده (تحویل امانی - قرض)',"reference_caption"=>"شماره تخصیص تامین کننده" ],
        [ "id" => 70, "caption" => ' درخواست کالا از طرف تامین کننده (برگشت از خرید)',"reference_caption"=>"کد مجوز" ],
        [ "id" => 80, "caption" => 'درخواست کالا (ویژه دوره پیاده سازی)',"reference_caption"=>"کاربر" ],

    ];
    private $table = 'applicant_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
