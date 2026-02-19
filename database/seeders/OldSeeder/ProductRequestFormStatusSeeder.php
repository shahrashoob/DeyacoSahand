<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductRequestFormStatusSeeder extends Seeder
{
    private $request_form_status = [
        "goods_kind_code" => "03",
        "station_code"    => "",
        "module_code"     => "7005",
        "code"            => [
            [ "id" => "001", "caption" => "در انتظار تحویل (ارسال) درخواست", "status_type_id" => "7005", "main_status_id" => "" ],
            [ "id" => "002", "caption" => "تحویل (ارسال) شده", "status_type_id" => "7005", "main_status_id" => "" ],
            [ "id" => "003", "caption" => "در انتظار تکمیل موجودی", "status_type_id" => "7005", "main_status_id" => "" ],
            [ "id" => "004", "caption" => "در انتظار تایید برگ خروج", "status_type_id" => "7005", "main_status_id" => "" ],
            [ "id" => "005", "caption" => "در انتظار مغایرت گیری", "status_type_id" => "7005", "main_status_id" => "" ],
            [ "id" => "006", "caption" => "کنسل شده", "status_type_id" => "7005", "main_status_id" => "" ],
            [ "id" => "007", "caption" => "در انتظار هماهنگی جهت ارسال", "status_type_id" => "7005", "main_status_id" => "" ],
            [ "id" => "008", "caption" => "در انتظار تحویل (ارسال) باقی مانده درخواست", "status_type_id" => "7005", "main_status_id" => "" ],
            [ "id" => "009", "caption" => "خاتمه یافته", "status_type_id" => "7005", "main_status_id" => "" ],
            [ "id" => "101", "caption" => "درخواست های فعال", "status_type_id" => "7005", "main_status_id" => "" ],
            [ "id" => "102", "caption" => "درخواست های غیرفعال", "status_type_id" => "7005", "main_status_id" => "" ],
            [ "id" => "201", "caption" => "در حال پردازش برگ خروج (جهت کشیدن برگ خروج از درخواست)", "status_type_id" => "7005", "main_status_id" => "" ],
        ]

    ];

    public function run() {
        foreach ( $this->request_form_status["code"] as $item ) {

            $status_id = $this->request_form_status["module_code"] .
                         $item["id"];

            if ( ! DB::table( "status" )->where( [ "id" => $status_id ] )->exists() ) {
                $item["id"]   = $status_id;
                $item["code"] = $status_id;

                DB::table( "status" )->insert( $item );
            } else {
                unset( $item["id"] );
                $item["code"] = $status_id;
                DB::table( "status" )->where( [ "id" => $status_id ] )->update( $item );
            }

        }

    }
}
