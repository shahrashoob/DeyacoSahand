<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialUniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //

        [ "id" => 1, "caption" => 'گرم/لیتر',"special_unit_type_id"=>1 ],
        [ "id" => 2, "caption" => 'دسی تکس',"special_unit_type_id"=>1 ],
        [ "id" => 3, "caption" => 'دنیر',"special_unit_type_id"=>1 ],
        [ "id" => 4, "caption" => 'میلی متر',"special_unit_type_id"=>1 ],
        [ "id" => 5, "caption" => 'عدد',"special_unit_type_id"=>1 ],
        [ "id" => 6, "caption" => 'سانتی متر',"special_unit_type_id"=>1 ],
        [ "id" => 7, "caption" => 'تعداد سرنخ در یک سانتی متر',"special_unit_type_id"=>1 ],
        [ "id" => 8, "caption" => 'درصد',"special_unit_type_id"=>1 ],
        [ "id" => 10, "caption" => 'گرم',"special_unit_type_id"=>1 ],
        [ "id" => 11, "caption" => 'تعداد دنده شانه در یک سانتی متر',"special_unit_type_id"=>1 ],
        [ "id" => 12, "caption" => 'گرم (دنیر)',"special_unit_type_id"=>1 ],
        [ "id" => 13, "caption" => 'واحد نمره نخ',"special_unit_type_id"=>1 ],
        [ "id" => 14, "caption" => 'کیلوگرم بر متر طولی',"special_unit_type_id"=>1 ],

        [ "id" => 15, "caption" =>'سانتی گراد',"special_unit_type_id"=>1 ],
        [ "id" => 16, "caption" =>'دقیقه',"special_unit_type_id"=>1 ],
        [ "id" => 17, "caption" =>'متر بر ثانیه',"special_unit_type_id"=>1 ],
        [ "id" => 18, "caption" =>'متر بر دقیقه',"special_unit_type_id"=>1 ],
        [ "id" => 19, "caption" =>'نیوتن',"special_unit_type_id"=>1 ],
        [ "id" => 20, "caption" =>'یک دهم میلی میتر',"special_unit_type_id"=>1 ],
        [ "id" => 21, "caption" =>'قلاب در سانتی متر',"special_unit_type_id"=>1 ],
        [ "id" => 22, "caption" =>'قلاب در اینچ',"special_unit_type_id"=>1 ],
        [ "id" => 23, "caption" => 'Mandrel',"special_unit_type_id"=>1 ],
        [ "id" => 24, "caption" => 'ثانیه ',"special_unit_type_id"=>1 ],
        [ "id" => 25, "caption" => 'گرم برسانتی مترمکعب  ',"special_unit_type_id"=>1 ],
        [ "id" => 25, "caption" => 'گرم برسانتی مترمکعب  ',"special_unit_type_id"=>1 ],
        [ "id" => 26, "caption" => 'ساعت',"special_unit_type_id"=>1 ],

        [ "id" => 997, "caption" => 'کنتور نمایشی ماشین',"special_unit_type_id"=>2 ],
        [ "id" => 998, "caption" => 'سانتی متر-',"special_unit_type_id"=>2 ],
        [ "id" => 999, "caption" => 'قطب',"special_unit_type_id"=>2 ],
        [ "id" => 1000, "caption" => 'پیک',"special_unit_type_id"=>2 ],
        [ "id" => 1001, "caption" => 'فاقد واحد',"special_unit_type_id"=>2 ],

    ];
    private $table = 'special_units';

    public function run() {

        foreach ( $this->data as $item ) {
            if(! DB::table($this->table)->where("id", $item["id"])->first()) {
                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )->where("id", $item["id"])->update($item);
            }
        }

    }
}
