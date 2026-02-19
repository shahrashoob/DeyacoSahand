<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QueueOfLargeOperationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => 'تخصیص اتوماتیک' ],
        [ "id" => 201, "caption" => 'بررسی اولیه انبارگردانی' ],
        [ "id" => 202, "caption" => 'بررسی نهایی انبارگردانی و ثبت فرم های انبار' ],
        [ "id" => 300, "caption" => 'پرینت فرم های بسته بندی' ],
        [ "id" => 400, "caption" => 'گزارش 1003: گزارش گردش کالا' ],
        [ "id" => 500, "caption" => 'گزارش 1003: گزارش گردش کالا' ],
        [ "id" => 600, "caption"=>   "تخصیص مجدد تخصیص های خاتمه یافته، در صورت نیاز"],
        [ "id" => 700, "caption"=>   "کپی بسته بندی در ماژول تولید ویژه دوره پیاده سازی"],
        [ "id" => 800, "caption"=>   "خروجی اکسل داشبورد تولید با جزئیات"],

    ];
    private $table = 'queue_of_large_operation_types';

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
