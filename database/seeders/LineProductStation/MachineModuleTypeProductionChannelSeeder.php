<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineModuleTypeProductionChannelSeeder extends Seeder
{
    private $data = [

        //پارچه خام
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>1,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>2,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>3,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>4,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>5,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>6,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>7,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>8,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>9,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>10,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>11,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>12,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>15,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        [
            "machine_module_type_id"      => 2, //ماژول های ماشین ژاکارد
            "production_channel_type_id"=>16,
            "min_capacity"=>500,
            "max_capacity"=>4000
        ],
        //چله کشی
        [
            "machine_module_type_id"      => 3, //jacquard - ماژول های ماشین ژاکارد
            "production_channel_type_id"=>13,
            "min_capacity"=>1,
            "max_capacity"=>999999
        ],
        //تکمیل
        [
            "machine_module_type_id"      => 5, //special_production_fabric - SpecialProductionFabric - پارجه تکمیل ویژه دوره پیاده سازی
            "production_channel_type_id"=>14,
            "min_capacity"=>1,
            "max_capacity"=>999999
        ],



    ];
    private $table = 'machine_module_type_production_channel_type';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
                DB::table( $this->table )->insert( $item );
        }
    }
}
