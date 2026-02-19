<?php

namespace Database\Seeders\GoodsKind\FabricRaw\Jacquard;

use App\Models\LineProduct\Machine\MachineModuleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JacquardSeeder extends Seeder {
    /**
     * Run the database seeds.
     * وضعیت های تولید ماشین در ماژول های ژاکارد
     * @return array[]
     */
    public static function getInfo() {
        return [
            7001 => [
                "caption"         => "کارت تولید",
                "status_type_id"  => 7001,
                "controller_info" => \App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\ProductionCard\DashboardController::get_controller_info_for_permission()
            ],
            7002 => [
                "caption"        => "فرم تولید",
                "status_type_id" => 7052,
                //   "controller_info" => \App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\ProductionForm\DashboardController::get_controller_info_all()
            ],
            7003 => [
                "caption"         => " ماشین ها",
                "status_type_id"  => 7003,
                "controller_info" => \App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\DashboardController::get_controller_info(),
                "code"            => [
                    7003006,
                    7003013,
                    7003014,
                    7003016,
                    7003017,
                    7003018,
                    7003019,
                    7003021,
                    7003022,
                    7003023,
                    7003024,
                    7003025,
                    7003031,
                    7003032,
                    7003042,
                    7003043,
                    7003044,
                    7003045,
                    7003047,
                    7003048,
                    7003049,
                    7003050,
                    7003051,
                    7003052,
                    7003053,
                    7003054,
                ]
            ],

        ];
    }

    public static function getFullId() {
// این شناسه فقط برای ماشین استفاده می شود.
        // به عنوان یک نوع وضعیت در نظر گرفته می شود و فقط در جدول Buttons استفاده می شود.
        return "7003002";

    }

    public $machine_module_code = "jacquard";


    public function run() {

        $machine_module_type_id = MachineModuleType::where( "code", $this->machine_module_code )->first()->id;

        // Button List

        $info = $this->getInfo();
        DB::table( "buttons" )->where( [ "status_type_id" => $this->getFullId() ] )->delete();


        foreach ( $info[7003]["controller_info"] as $key => $item ) {
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
            ] )->whereNotIn( "production_status_id", $info[7003]["code"] )->delete();

        foreach ( $info[7003]["code"] as $item ) {

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
