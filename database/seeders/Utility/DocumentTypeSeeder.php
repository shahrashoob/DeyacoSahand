<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        ['id' => 1, 'caption' => 'تصویر صفحه اول شناسنامه','nationality_id'=>1],
        ['id' => 2, 'caption' => 'تصویر صفحه دوم شناسنامه','nationality_id'=>1],
        ['id' => 3, 'caption' => 'تصویر صفحه سوم شناسنامه','nationality_id'=>1],
        ['id' => 4, 'caption' => 'تصویر روی کارت ملی','nationality_id'=>1],
        ['id' => 5, 'caption' => 'تصویر پشت کارت ملی','nationality_id'=>1],
//        ['id' => 6, 'caption' => 'عکس پرسنلی'],
        ['id' => 7, 'caption' => 'تصویر مدرک تحصیلی'],
        ['id' => 8, 'caption' => 'تصویر طب کار'],
        ['id' => 9, 'caption' => ' تصویر سابقه شغلی'],
//        ['id' => 10, 'caption' => 'تصویر کارت پایان خدمت'],
        ['id' => 11, 'caption' => 'تصویر تاییدیه کد پستی'],
        ['id' => 12, 'caption' => ' تصویر گواهینامه دوره ی آموزشی'],
        ['id' => 13, 'caption' => 'تصویرگذرنامه / کارت اقامت','nationality_id'=>2],
        ['id' => 14, 'caption' => 'تصویر مدرک سربازی'],
        ['id' => 15, 'caption' => 'تصویر صفحه مشخصات همسر','nationality_id'=>1],
    ];
    private $table = 'document_types';

    public function run()
    {
        DB::table( $this->table )->delete();
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
