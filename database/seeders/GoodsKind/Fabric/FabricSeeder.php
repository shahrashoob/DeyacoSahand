<?php

namespace Database\Seeders\GoodsKind\Fabric;

use App\Models\LineProduct\GoodsKind;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FabricSeeder extends Seeder {

    public function getProductionWaitingStatus() {
        return [
            "goods_kind_code" => "05",
            "station_code"    => "",
            "module_code"     => "7301",
            "code"            => [
                [ "id" => "001", "caption" => "تخصیص ماشین", "status_type_id" => "7301", "main_status_id" => "500" ],
                [
                    "id"             => "002",
                    "caption"        => "نصب و راه اندازی",
                    "status_type_id" => "7301",
                    "main_status_id" => "500"
                ],
                [ "id" => "003", "caption" => "در حال تولید", "status_type_id" => "7301", "main_status_id" => "500" ],
                [ "id" => "004", "caption" => "خاتمه یافته", "status_type_id" => "7301", "main_status_id" => "500" ],
                [ "id" => "005", "caption" => "تخصیص مجدد", "status_type_id" => "7301", "main_status_id" => "500" ],
            ],
            "controller_info" => \App\Http\Controllers\GoodsKindProcess\Fabric\ProductionCardController::get_controller_info_for_permission()
        ];
    }

    public function getProductionFormStatus() {
        return [
            "goods_kind_code" => "05",
            "station_code"    => "",
            "module_code"     => "7302",
            "code"            => [
                [ "id"             => "001",
                  "caption"        => " در حال تکمیل",
                  "status_type_id" => "7302",
                  "main_status_id" => ""
                ],
                [ "id"             => "002",
                  "caption"        => " خاتمه یافته",
                  "status_type_id" => "7302",
                  "main_status_id" => ""
                ],
                [ "id"             => "003",
                  "caption"        => "استخراج شده در انتظار تزریق به ماشین",
                  "status_type_id" => "7302",
                  "main_status_id" => ""
                ],
                [ "id"             => "004",
                  "caption"        => "تزریق شده به ماشین ",
                  "status_type_id" => "7302",
                  "main_status_id" => ""
                ],
            ],
            "controller_info" => []
        ];
    }

    private $machine_status = [
        "0301" => [
            "goods_kind_code" => "05",
            "module_code"     => "7303",
            "code"            => [
                [ "id" => "001", "caption" => "نداشتن سفارش", "status_type_id" => "7303", ],
//                [ "id" => "002", "caption" => "در حال راه اندازی", "status_type_id" => "7303", ],

                ["id" => "901", "caption" => "در انتظار شروع ستاب", "status_type_id" => "7303"],
                ["id" => "902", "caption" => "در انتظار شروع عملیات", "status_type_id" => "7303"],
                ["id" => "903", "caption" => "در انتظار پایان عملیات", "status_type_id" => "7303"],
                ["id" => "904", "caption" => "در انتظار انجام تنظیمات نهایی", "status_type_id" => "7303"],
                ["id" => "905", "caption" => "در انتظار تایید کنترل کیفیت", "status_type_id" => "7303"],
                ["id" => "906", "caption" => "در انتظار شروع تست", "status_type_id" => "7303"],
                ["id" => "907", "caption" => "در حال انجام تست", "status_type_id" => "7303"],
                ["id" => "908", "caption" => "در انتظار تایید تست", "status_type_id" => "7303"],
            ]
        ]
    ];


    public function run() {

        // Production Waiting Status
        $goods_kind_id = $this->getProductionWaitingStatus()["goods_kind_code"] + 0;
        DB::table( "production_waiting_status" )->where( [
            "goods_kind_id" => $goods_kind_id
        ] )->delete();
        foreach ( $this->getProductionWaitingStatus()["code"] as $item ) {

            $status_id = $this->getProductionWaitingStatus()["module_code"] .
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


            DB::table( "production_waiting_status" )->insert(
                [
                    "status_id"     => $status_id,
                    "goods_kind_id" => $goods_kind_id
                ]
            );

        }

        // Production Button
        DB::table( "buttons" )->where( [ "status_type_id" => $this->getProductionWaitingStatus()["module_code"] ] )->delete();

        foreach ( $this->getProductionWaitingStatus()["controller_info"] as $key => $item ) {
            $id      = $this->getProductionWaitingStatus()["module_code"] . $key;
            $name    = $item["route"] . "index";
            $caption = $item["button"]["caption"];

            DB::table( "buttons" )->insert( [
                "id"             => $id,
                "name"           => $name,
                "caption"        => $caption,
                "status_type_id" => $this->getProductionWaitingStatus()["module_code"]
            ] );

        }

        // Production From Status
        $goods_kind_id = $this->getProductionFormStatus()["goods_kind_code"] + 0;
        DB::table( "production_form_status" )->where( [
            "goods_kind_id" => $goods_kind_id
        ] )->delete();
        foreach ( $this->getProductionFormStatus()["code"] as $item ) {

            $status_id = $this->getProductionFormStatus()["module_code"] .
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


            DB::table( "production_form_status" )->insert(
                [
                    "status_id"     => $status_id,
                    "goods_kind_id" => $goods_kind_id
                ]
            );

        }

        // Production Form Button
        DB::table( "buttons" )->where( [ "status_type_id" => $this->getProductionFormStatus()["module_code"] ] )->delete();

        foreach ( $this->getProductionFormStatus()["controller_info"] as $key => $item ) {
            $id      = $this->getProductionFormStatus()["module_code"] . $key;
            $name    = $item["route"] . "index";
            $caption = $item["button"]["caption"];

            DB::table( "buttons" )->insert( [
                "id"             => $id,
                "name"           => $name,
                "caption"        => $caption,
                "status_type_id" => $this->getProductionFormStatus()["module_code"]
            ] );

        }
        // Machine Status
        foreach ( $this->machine_status as $station_code => $section ) {


            foreach ( $section["code"] as $item ) {

                $status_id = $section["module_code"] .
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


// Seed  برای قبل از راه اندازی چله کشی
//    public function run() {
//
//        $goods_kind = GoodsKind::where( "caption_en", "Warps" )->first();
//        if ( ! $goods_kind ) {
//            return;
//        }
//        $module = $goods_kind->getModuleList();
//        if ( ! $module ) {
//            return;
//        }
//        foreach ( $module as $module_id => $module_item ) {
//
//            foreach ( $module_item["controller_info"] as $key => $item ) {
//                $id      = $module_id . $key;
//                $name    = $item["route"] . "index";
//                $caption = $item["button"]["caption"];
//
//                if ( ! DB::table( "buttons" )->where( [ "id" => $id ] )->exists() ) {
//
//                    DB::table( "buttons" )->insert( [
//                        "id"             => $id,
//                        "name"           => $name,
//                        "caption"        => $caption,
//                        "status_type_id" => $module_id
//                    ] );
//                } else {
//
//                    DB::table( "buttons" )->where( [ "id" => $id ] )->update( [
//                        "name"           => $name,
//                        "caption"        => $caption,
//                        "status_type_id" => $module_id
//                    ] );
//                }
//            }
//        }
//
//
//    }
}
