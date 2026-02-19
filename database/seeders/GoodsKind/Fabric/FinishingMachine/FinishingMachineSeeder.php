<?php

namespace Database\Seeders\GoodsKind\Fabric\FinishingMachine;

use App\Models\LineProduct\Machine\MachineModuleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinishingMachineSeeder extends Seeder {
    /**
     * Run the database seeds.
    ماشین آلات عمومی تکمیل صنعت نساجی
     *     * @return array[]
     */
    public static function getInfo() {
        return [
            7301 => [
                "caption"         => "کارت تولید",
                "status_type_id"  => 7301,
                "controller_info" => \App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\ProductionCard\DashboardController::get_controller_info_for_permission()
            ],
            7302 => [
                "caption"        => "فرم تولید",
                "status_type_id" => 7302,
                //   "controller_info" => \App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\ProductionForm\DashboardController::get_controller_info_all()
            ],
            7303 => [
                "caption"         => " ماشین ها",
                "status_type_id"  => 7303,
                "controller_info" => \App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine\DashboardController::get_controller_info(),
                "code"            => [
                    7303001,

                    7303901,
                    7303902,
                    7303903,
                    7303904,
                    7303905,
                    7303906,
                    7303907,
                    7303908,
                ]
            ],

        ];
    }

    public static function getFullId() {
// این شناسه فقط برای ماشین استفاده می شود.
        // به عنوان یک نوع وضعیت در نظر گرفته می شود و فقط در جدول Buttons استفاده می شود.
        return "7303002";

    }

    public $machine_module_code = "finishing_machine";


    public function run() {

        $machine_module_type_id = MachineModuleType::where( "code", $this->machine_module_code )->first()->id;

        // Button List

        $info = $this->getInfo();
        DB::table( "buttons" )->where( [ "status_type_id" => $this->getFullId() ] )->delete();


        foreach ( $info[7303]["controller_info"] as $key => $item ) {
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
            ] )->whereNotIn( "production_status_id", $info[7303]["code"] )->delete();

        foreach ( $info[7303]["code"] as $item ) {

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
