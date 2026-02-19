<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachinePropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $data = [
        // بافندگی
        [ "id" => 1, "caption" => "عرض ماشین (cm)", "station_id"=>16,"min_value"=>0,"max_value"=>5000,"field_type_id"=>1,"priority_number"=>1 ],
        [ "id" => 2, "caption" => " فاصله شانه تا محل استخراج پارچه(cm)", "station_id"=>16 ,"min_value"=>0,"max_value"=>5000,"field_type_id"=>1,"priority_number"=>1],
        [ "id" => 3, "caption" => " (حذف به مشخصات گروه ماشین اضافه شد.) کارکرد کنتور اصلی در دقیقه", "station_id"=>16,"min_value"=>0,"max_value"=>50000,"field_type_id"=>1 ,"priority_number"=>6],
        [ "id" => 4, "caption" => "حداقل مقدار جهت داف (کیلوگرم)", "station_id"=>16,"min_value"=>0,"max_value"=>200,"field_type_id"=>1 ,"priority_number"=>1],
        [ "id" => 5, "caption" => "حداکثر مقدار جهت داف (کیلوگرم)", "station_id"=>16 ,"min_value"=>0,"max_value"=>1000,"field_type_id"=>1,"priority_number"=>1],
        [ "id" => 6, "caption" => "تعداد کنتور", "station_id"=>16,"min_value"=>0,"max_value"=>5,"field_type_id"=>1 ,"priority_number"=>2],
        [ "id" => 7, "caption" => "واحد اصلی کنتور", "station_id"=>16,"min_value"=>0,"max_value"=>5000,"field_type_id"=>3,"priority_number"=>3],
        [ "id" => 8, "caption" => "واحد نمایشی کنتور", "station_id"=>16,"min_value"=>0,"max_value"=>5000,"field_type_id"=>3,"priority_number"=>4],
        [ "id" => 9, "caption" => "ضریب تبدیل یا n (هر واحد اصلی n واحد فرعی می باشد) ", "station_id"=>16,"min_value"=>0,"max_value"=>1000,"field_type_id"=>1,"priority_number"=>5],


//        [ "id" => 101, "caption" => "حداکثر مقدار جهت داف (متر)", "station_id"=>21 ,"min_value"=>1,"max_value"=>90000,"field_type_id"=>1,"priority_number"=>1],


    ];
    private $table = 'machine_properties';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            } else {
                DB::table( $this->table )->
                where( "id", $item["id"] )->update( $item );
            }

        }
    }
}
