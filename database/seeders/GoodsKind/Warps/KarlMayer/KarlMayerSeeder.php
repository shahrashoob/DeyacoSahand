<?php

namespace Database\Seeders\GoodsKind\Warps\KarlMayer;

use App\Models\LineProduct\Machine\MachineModuleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KarlMayerSeeder extends Seeder {
    /**
     * Run the database seeds.
     * وضعیت های تولید ماشین در ماژول های چله کشی Matthys
     * @return array[]
     */
    public static function getInfo() {
        return [
            7201 => [
                "caption"         => "کارت تولید",
                "status_type_id"  => 7001,
                "controller_info" => \App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer\ProductionCard\DashboardController::get_controller_info_for_permission()
            ],
            7202 => [
                "caption"        => "فرم تولید",
                "status_type_id" => 7052,
                //   "controller_info" =>
            ],
            7203 => [
                "caption"         => " ماشین ها",
                "status_type_id"  => 7003,
                "controller_info" => \App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer\Machine\DashboardController::get_controller_info(),
                "code"            => [
                    7203001,
                    7203003,
                    7203004,
                    7203005,
                ]
            ],

        ];
    }

    public static function getFullId() {
// این شناسه فقط برای ماشین استفاده می شود.
        // به عنوان یک نوع وضعیت در نظر گرفته می شود و فقط در جدول Buttons استفاده می شود.
        return "7203002";

    }

    public $machine_module_code = "karl_mayer";


    public function run() {

        $machine_module_type_id = MachineModuleType::where( "code", $this->machine_module_code )->first()->id;

        // Button List

        $info = $this->getInfo();
        DB::table( "buttons" )->where( [ "status_type_id" => $this->getFullId() ] )->delete();


        foreach ( $info[7203]["controller_info"] as $key => $item ) {
            $id      = $this->getFullId() . $key;
            $name    = $item["route"] . "index";
            $caption = $item["button"]["caption"];

            DB::table( "buttons" )->insert( [
                "id"             => $id,
                "name"           => $name,
                "caption"        => $caption,
                "status_type_id" => $this->getFullId()
            ] );

        }


        DB::table( "machine_status" )->where(
            [
                "machine_module_type_id" => $machine_module_type_id
            ] )->whereNotIn( "production_status_id", $info[7203]["code"] )->delete();

        foreach ( $info[7203]["code"] as $item ) {

            // افزودن به جدول وضعیت های خاص ماژول
            if ( ! DB::table( "machine_status" )->where(
                [
                    "production_status_id"   => $item,
                    "machine_module_type_id" => $machine_module_type_id
                ] )->exists() ) {

                DB::table( "machine_status" )->insert(
                    [
                        "production_status_id"   => $item,
                        "machine_module_type_id" => $machine_module_type_id
                    ]
                );
            }


        }
    }

}
