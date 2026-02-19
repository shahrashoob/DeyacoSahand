<?php

namespace Database\Seeders\GoodsKind\FabricRaw;

use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Station;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FabricRawSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function getProductionWaitingStatus() {
        return [
            "goods_kind_code" => "04",
            "station_code"    => "",
            "module_code"     => "7001",
            "code"            => [
                [ "id" => "001", "caption" => "تخصیص ماشین", "status_type_id" => "7001", "main_status_id" => "500" ],
                [
                    "id"             => "002",
                    "caption"        => "نصب و راه اندازی",
                    "status_type_id" => "7001",
                    "main_status_id" => "500"
                ],
                [ "id" => "003", "caption" => "بافت *** ماشین", "status_type_id" => "7001", "main_status_id" => "500" ],
                [ "id" => "004", "caption" => "خاتمه یافته", "status_type_id" => "7001", "main_status_id" => "500" ],
                [ "id" => "005", "caption" => "تخصیص مجدد", "status_type_id" => "7001", "main_status_id" => "500" ],
            ],
            "controller_info" => \App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCardController::get_controller_info_for_permission()
        ];
    }

    public function getProductionFormStatus() {
        return [
            "goods_kind_code" => "04",
            "station_code"    => "",
            "module_code"     => "7002",
            "code"            => [
                [ "id"             => "001",
                  "caption"        => " در حال بافت پارچه",
                  "status_type_id" => "7002",
                  "main_status_id" => ""
                ],
                [
                    "id"             => "002",
                    "caption"        => "در انتظار استخراج پارچه",
                    "status_type_id" => "7002",
                    "main_status_id" => ""
                ],
                [ "id"             => "003",
                  "caption"        => "در انتظار  درجه بندی",
                  "status_type_id" => "7002",
                  "main_status_id" => ""
                ],
                [
                    "id"             => "004",
                    "caption"        => "در انتظار ثبت حامل بسته بندی",
                    "status_type_id" => "7002",
                    "main_status_id" => ""
                ],
                [ "id" => "005", "caption" => "خاتمه یافته", "status_type_id" => "7002", "main_status_id" => "" ],
                [
                    "id"             => "006",
                    "caption"        => "بسته بندی شده",
                    "status_type_id" => "7002",
                    "main_status_id" => ""
                ],
                [
                    "id"             => "007",
                    "caption"        => "در انتظار استخراج پارچه پایانی",
                    "status_type_id" => 7002,
                    "main_status_id" => ""
                ],
                [
                    "id"             => "008",
                    "caption"        => "در حال بافت بخش پایانی پارچه",
                    "status_type_id" => 7002,
                    "main_status_id" => ""
                ],
                [
                    "id"             => "009",
                    "caption"        => "در حال تغییر نخ پود (دستور توقف)",
                    "status_type_id" => 7002,
                    "main_status_id" => ""
                ],
                [
                    "id"             => "010",
                    "caption"        => "در انتظار استخراج پارچه پایانی (دستور توقف)",
                    "status_type_id" => 7002,
                    "main_status_id" => ""
                ],
                [
                    "id"             => "011",
                    "caption"        => "در انتظار بارگذاری",
                    "status_type_id" => 7002,
                    "main_status_id" => ""
                ],

            ],
            "controller_info" => \App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionFormController::get_controller_info_all()
        ];
    }

    private $machine_status = [
        "0301" => [
            "goods_kind_code" => "04",
            "module_code"     => "7003",
            "code"            => [
                [
                    "id"             => "001",
                    "caption"        => "در انتظار شروع مقدمات تغییر کالیته",
                    "status_type_id" => "7003",

                ],
//                002 => حذف
                [
                    "id"             => "003",
                    "caption"        => "در حال انجام مقدمات تغییر کالیته",
                    "status_type_id" => "7003",

                ],
                [
                    "id"             => "004",
                    "caption"        => "در حال انجام  مرحله دوم تغییر کالیته",
                    "status_type_id" => "7003",

                ],
                [
                    "id"             => "005",
                    "caption"        => "نداشتن طراحی",
                    "status_type_id" => "7003"
                ],
                [
                    "id"             => "006",
                    "caption"        => "در انتظار شروع بارگذاری چله",
                    "status_type_id" => "7003",

                ],
//                [
//                    "id"             => "007",
//                    "caption"        => "شروع لامل ریزی",
//                    "status_type_id" => "7003",
//
//                ],
//                [
//                    "id"             => "008",
//                    "caption"        => "نداشتن چله",
//                    "status_type_id" => "7003",
//                ],
                [
                    "id"             => "009",
                    "caption"        => "درحال چله گزاری",
                    "status_type_id" => "7003",

                ],
                [
                    "id"             => "010",
                    "caption"        => "در انتظار شروع گره زنی",
                    "status_type_id" => "7003"
                ],
                [
                    "id"             => "011",
                    "caption"        => "در حال گره زنی",
                    "status_type_id" => "7003",

                ],
                [
                    "id"             => "012",
                    "caption"        => "در انتظار راه اندازی تغییر کالیته",
                    "status_type_id" => "7003",

                ],
                [
                    "id"             => "013",
                    "caption"        => "در انتظار راه اندازی شیفت",
                    "status_type_id" => "7003",

                ],
                [ "id" => "014", "caption" => "در انتظار شروع لامل ریزی", "status_type_id" => "7003", ],
                [ "id" => "015", "caption" => "در انتظار کنترل", "status_type_id" => "7003", ],
                [ "id" => "016", "caption" => "در حال بافت", "status_type_id" => "7003", ],
                [ "id" => "017", "caption" => "نداشتن سفارش", "status_type_id" => "7003", ],
                [ "id" => "018", "caption" => "در حال لامل ریزی", "status_type_id" => "7003", ],
                [ "id" => "019", "caption" => "در انتظار آماده سازی چله", "status_type_id" => "7003", ],
                [ "id" => "020", "caption" => "در انتظار آماده سازی طراحی ", "status_type_id" => "7003", ],
                [ "id" => "021", "caption" => "در انتظار آماده سازی چله جهت تعویض چله ", "status_type_id" => "7003", ],
                [ "id" => "022", "caption" => "در انتظار تعویض چله ", "status_type_id" => "7003", ],
                [ "id" => "023", "caption" => "در حال تعویض چله ", "status_type_id" => "7003", ],
                [ "id" => "024", "caption" => "در انتظار شروع گره زنی (تعویض چله) ", "status_type_id" => "7003", ],
                [ "id" => "025", "caption" => "در حال گره زنی (تعویض چله) ", "status_type_id" => "7003", ],
                [ "id" => "025", "caption" => "در حال گره زنی (تعویض چله) ", "status_type_id" => "7003", ],
                [ "id" => "026", "caption" => "در انتظار پایان چله (جهت تغییر کالیته) ", "status_type_id" => "7003", ],
                [ "id" => "027", "caption" => "در انتظار تغییر نخ پود ", "status_type_id" => "7003", ],
                [
                    "id"             => "028",
                    "caption"        => "در انتظار شروع تعویض چله (جهت تغییر کالیته) ",
                    "status_type_id" => "7003",
                ],
                [
                    "id"             => "029",
                    "caption"        => "در انتظار اماده سازی چله (جهت تغییر کالیته) ",
                    "status_type_id" => "7003",
                ],
                [ "id" => "030", "caption" => "در حال تعویض چله (جهت تغییر کالیته) ", "status_type_id" => "7003", ],
                [
                    "id"             => "031",
                    "caption"        => "در انتظار شروع گره زنی (جهت تغییر کالیته) ",
                    "status_type_id" => "7003",
                ],
                [ "id" => "032", "caption" => "در حال گره زنی (جهت تغییر کالیته) ", "status_type_id" => "7003", ],
                [
                    "id"             => "033",
                    "caption"        => "در انتظار راه اندازی شیفت (جهت تغییر کالیته) ",
                    "status_type_id" => "7003",
                ],
                [ "id" => "034", "caption" => "در انتظار پایان عملیات نت (تغییر تراکم) ", "status_type_id" => "7003", ],
                [ "id" => "035", "caption" => "در حال بافت پارچه پایانی ", "status_type_id" => "7003", ],
                [ "id" => "036", "caption" => "در انتظار تغییر نخ پود (توقف کارت تولید)", "status_type_id" => "7003", ],
                [ "id" => "037", "caption" => "در حال تغییر نخ پود (توقف کارت تولید)", "status_type_id" => "7003", ],
                [
                    "id"             => "038",
                    "caption"        => "در انتظار استخراج پارچه پایانی (توقف کارت تولید)",
                    "status_type_id" => "7003",
                ],
                [ "id" => "039", "caption" => "در انتظار آماده سازی جهت لامل ریزی", "status_type_id" => "7003", ],
                [
                    "id"             => "040",
                    "caption"        => "در انتظار شروع استخراج چله (توقف کارت تولید)",
                    "status_type_id" => "7003",
                ],
                [ "id" => "041", "caption" => "  در حال استخراج چله (توقف کارت تولید)", "status_type_id" => "7003", ],

                [ "id" => "042", "caption" => "  در انتظار پایان بافت (کارت تولید جاری)", "status_type_id" => "7003", ],
                [ "id" => "043", "caption" => "در انتظار شروع تغییر کالیته", "status_type_id" => "7003", ],
                [
                    "id"             => "044",
                    "caption"        => "در انتظار شروع استخراج چله (جهت تغییر کالیته)",
                    "status_type_id" => "7003",
                ],
                [
                    "id"             => "045",
                    "caption"        => "در حال استخراج چله (جهت تغییر کالیته)",
                    "status_type_id" => "7003",
                ],
                [
                    "id"             => "047",
                    "caption"        => "در حال تغییر کالیته",
                    "status_type_id" => "7003",
                ],
                [
                    "id"             => "048",
                    "caption"        => "در حال راه اندازی شیفت",
                    "status_type_id" => "7003",
                ],
                [
                    "id"             => "049",
                    "caption"        => "در انتظار شروع شانه کشی",
                    "status_type_id" => "7003",
                ],
                [
                    "id"             => "050",
                    "caption"        => "در حال شانه کشی",
                    "status_type_id" => "7003",
                ],
                [
                    "id"             => "051",
                    "caption"        => "در انتظار شروع تغییر عرض ماشین",
                    "status_type_id" => "7003",
                ],
                [
                    "id"             => "052",
                    "caption"        => "در حال تغییر عرض ماشین",
                    "status_type_id" => "7003",
                ],

                [
                    "id"             => "053",
                    "caption"        => "در انتظار شروع نمونه گیری",
                    "status_type_id" => "7003",
                ],

                [
                    "id"             => "054",
                    "caption"        => "در حال نمونه گیری",
                    "status_type_id" => "7003",
                ],

            ]
        ]
    ];

    private $design_form_status = [
        "module_code" => "7004",
        "code"        => [
            [ "id" => "001", "caption" => "در انتظار شروع طراحی", "status_type_id" => "7004", "main_status_id" => "" ],
            [ "id" => "002", "caption" => "در حال  تبدیل طراحی", "status_type_id" => "7004", "main_status_id" => "" ],
            [ "id" => "003", "caption" => "در انتظار تحویل چله", "status_type_id" => "7004", "main_status_id" => "" ],
            [ "id" => "004", "caption" => "پایان طراحی", "status_type_id" => "7004", "main_status_id" => "" ],
            [ "id" => "005", "caption" => "در حال طراحی", "status_type_id" => "7004", "main_status_id" => "" ],
            [ "id" => "006", "caption" => "کنسل شده", "status_type_id" => "7004", "main_status_id" => "" ],
        ]
    ];
    private $fabric_raw_grading_status = [
        "module_code" => "7006",
        "code"        => [
            [ "id" => "001", "caption" => "معلق", "status_type_id" => "7006", "main_status_id" => "" ],
            [ "id" => "002", "caption" => "در انتظار بسته بندی", "status_type_id" => "7006", "main_status_id" => "" ],
            [ "id" => "003", "caption" => "بسته بندی شده", "status_type_id" => "7006", "main_status_id" => "" ],
            [ "id" => "004", "caption" => "ثبت و تایید", "status_type_id" => "7006", "main_status_id" => "" ],

        ]
    ];


// وضعیت فرم بسته بندی PackingForm
    public function getPackingFormStatus(){
        return  [
            "module_code"     => "7007",
            "code"            => [
                [ "id" => "001", "caption" => "در حال تکمیل", "status_type_id" => 7007, "main_status_id" => "" ],
                [
                    "id"             => "002",
                    "caption"        => "در انتظار تایید انبار",
                    "status_type_id" => 7007,
                    "main_status_id" => ""
                ],
                [ "id" => "003", "caption" => "تحویل شده به انبار", "status_type_id" => 7007, "main_status_id" => "" ],
                [
                    "id"             => "005",
                    "caption"        => "در انتظار تحویل به انبار",
                    "status_type_id" => 7007,
                    "main_status_id" => ""
                ],
                [ "id" => "006", "caption" => "معلق", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "007", "caption" => "تغییر یافته", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "008", "caption" => "در انتظار ارسال محصول", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "009", "caption" => "در انتظار تایید دریافت محصول", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "010", "caption" => "در انتظار مغایرت گیری", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "011", "caption" => "معلق - api", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "012", "caption" => "خارج شده از انبار", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "013", "caption" => "در انتظار تغییر بسته بندی", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "014", "caption" => "تحویل شده به مشتری", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "015", "caption" => "مرجوع شده", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "016", "caption" => "تحویل شده به پیمانکار", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "017", "caption" => "تحویل شده به ماشین", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "018", "caption" => "داخل بسته بندی بزرگتر ", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "019", "caption" => "مصرف شده", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "020", "caption" => "در انتظار تکمیل اطلاعات بسته بندی", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "021", "caption" => "ادغام شده", "status_type_id" => 7007, "main_status_id" => "" ],
//                [ "id" => "022", "caption" => "حذف شده", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "022", "caption" => "تحویل شده به تامین کننده", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "023", "caption" => "نزد مشتری (در انتظار دریافت)", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "024", "caption" => "در انتظار ثبت در سامانه جامع (تامین کننده/پیمانکار)", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "025", "caption" => "ثبت ناموفق در سامانه جامع (تامین کننده/پیمانکار)", "status_type_id" => 7007, "main_status_id" => "" ],
                [ "id" => "026", "caption" => "در انتظار کنترل کیفیت", "status_type_id" => 7007, "main_status_id" => "" ],

            ],
            "controller_info" => \App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingFormController::get_controller_info()

        ];
    }

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

        // Design Form Status
        foreach ( $this->design_form_status["code"] as $item ) {

            $status_id = $this->design_form_status["module_code"] .
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


        // Fabric Raw Grading
        foreach ( $this->fabric_raw_grading_status["code"] as $item ) {

            $status_id = $this->fabric_raw_grading_status["module_code"] .
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

        // Packing List
        foreach ( $this->getPackingFormStatus()["code"] as $item ) {

            $status_id = $this->getPackingFormStatus()["module_code"] .
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
        // Packing Form Button
        DB::table( "buttons" )->where( [ "status_type_id" => $this->getPackingFormStatus()["module_code"] ] )->delete();

        foreach ( $this->getPackingFormStatus()["controller_info"] as $key => $item ) {
            $id      = $this->getPackingFormStatus()["module_code"] . $key;
            $name    = $item["route"] . "index";
            $caption = $item["button"]["caption"];

            DB::table( "buttons" )->insert( [
                "id"             => $id,
                "name"           => $name,
                "caption"        => $caption,
                "status_type_id" => $this->getPackingFormStatus()["module_code"]
            ] );

        }

    }


}
