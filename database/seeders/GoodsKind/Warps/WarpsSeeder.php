<?php

namespace Database\Seeders\GoodsKind\Warps;

use App\Models\LineProduct\GoodsKind;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarpsSeeder extends Seeder {

    public function getProductionWaitingStatus() {
        return [
            "goods_kind_code" => "03",
            "station_code"    => "",
            "module_code"     => "7201",
            "code"            => [
                [ "id" => "001", "caption" => "تخصیص ماشین", "status_type_id" => "7201", "main_status_id" => "500" ],
                [
                    "id"             => "002",
                    "caption"        => "نصب و راه اندازی",
                    "status_type_id" => "7201",
                    "main_status_id" => "500"
                ],
                [ "id" => "003", "caption" => "در حال تولید", "status_type_id" => "7201", "main_status_id" => "500" ],
                [ "id" => "004", "caption" => "خاتمه یافته", "status_type_id" => "7201", "main_status_id" => "500" ],
            ],
            "controller_info" => \App\Http\Controllers\GoodsKindProcess\Warps\ProductionCardController::get_controller_info_for_permission()
        ];
    }

    public function getProductionFormStatus() {
        return [
            "goods_kind_code" => "03",
            "station_code"    => "",
            "module_code"     => "7202",
            "code"            => [
                [ "id"             => "001",
                  "caption"        => " در انتظار شروع چله کشی",
                  "status_type_id" => "7202",
                  "main_status_id" => ""
                ],
                [
                    "id"             => "002",
                    "caption"        => " در حال چله کشی",
                    "status_type_id" => "7202",
                    "main_status_id" => ""
                ],
                [
                    "id"             => "003",
                    "caption"        => "در حال برگردان",
                    "status_type_id" => "7202",
                    "main_status_id" => ""
                ],
                [ "id" => "004", "caption" => "بسته بندی شده", "status_type_id" => "7202", "main_status_id" => "" ],

            ],
            "controller_info" => \App\Http\Controllers\GoodsKindProcess\Warps\ProductionFormController::get_controller_info_all()
        ];
    }

    private $machine_status = [
        "0301" => [
            "goods_kind_code" => "03",
            "module_code"     => "7203",
            "code"            => [
                [ "id" => "001", "caption" => "نداشتن سفارش", "status_type_id" => "7203", ],
                [ "id" => "002", "caption" => "در حال برگردان", "status_type_id" => "7203", ],
                [ "id" => "003", "caption" => "در انتظار پایان قفسه گذاری", "status_type_id" => "7203", ],
                [ "id" => "004", "caption" => "در انتظار شروع چله کشی ", "status_type_id" => "7203", ],
                [ "id" => "005", "caption" => "در حال چله کشی ", "status_type_id" => "7203", ],
                [ "id" => "006", "caption" => "در انتظار شروع برگردان ", "status_type_id" => "7203", ],


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
