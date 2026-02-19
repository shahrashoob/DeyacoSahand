<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentReceiveStepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        ['id' => 1, 'caption' => 'اطلاعات فردی'],
        ['id' => 2, 'caption' => 'اطلاعات آدرس'],
        ['id' => 3, 'caption' => 'اطلاعات تحصیلی _ سیکل و ابتدایی'],
        ['id' => 4, 'caption' => 'اطلاعات تحصیلی _ دیپلم'],
        ['id' => 5, 'caption' => 'اطلاعات تحصیلی_کارشناسی'],
        ['id' => 6, 'caption' => 'اطلاعات تحصیلی _ کارشناسی ارشد'],
        ['id' => 7, 'caption' => 'اطلاعات تحصیلی _ دکتری'],
        ['id' => 8, 'caption' => 'اطلاعات شغلی'],
        ['id' => 9, 'caption' => 'اطلاعات دوره های آموزشی'],
        ['id' => 10, 'caption' => 'طب کار'],
        ['id' => 11, 'caption' => 'اطلاعات سربازی'],
    ];
    private $table = 'document_receive_steps';

    public function run()
    {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }else{
                DB::table( $this->table ) ->where( "id", $item["id"] )->update( $item );
            }

        }

    }
}
