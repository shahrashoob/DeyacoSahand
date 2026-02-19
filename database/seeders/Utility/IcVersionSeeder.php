<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IcVersionSeeder extends Seeder
{
    /****
     * به ازای هر جدول در سامانه مشخص می شود که آخرین ورژن جدول ها از چه نوع ورژنی است و اگر نیاز به بروز رسانی دارد بروز می شود.
     */
    private $data = [
        //
        ["id" => 1, "caption" => "مشخصه های رسته کالایی", "unit_table" => "goods_kind_properties", 'version' => -1],
        ["id" => 2, "caption" => "مشخصه های وابسته ", "unit_table" => "goods_kind_property_dependent_values", 'version' => -1],
        ["id" => 3, "caption" => "ایتم مشخصه ها کالا", "unit_table" => "goods_kind_property_options", 'version' => -1],
        ["id" => 4, "caption" => "واحد های خاص", "unit_table" => "special_units", 'version' => -1],
        ["id" => 5, "caption" => "انواع واحد های خاص", "unit_table" => "special_unit_types", 'version' => -1],
        ["id" => 6, "caption" => "درجه های رسته کالا", "unit_table" => "degrees", 'version' => -1],
//        ["id" => 7, "caption" => "طراحی کالا در رسته های کالا", "unit_table" => "product_creation_process_priority", 'version' => -1],
        ["id" =>8, "caption" => "نقص ها در رسته های کالا", "unit_table" => "goods_kind_product_fault", 'version' => -1],
        ["id" =>9, "caption" => "نقص های کالا", "unit_table" => "product_faults", 'version' => -1],
        ["id" =>10, "caption" => "نمود های بیرونی نقص های کالا", "unit_table" => "product_fault_signs", 'version' => -1],
    ];
    private $table = 'ic_versions';

    public function run()
    {
        foreach ($this->data as $item) {
            if (!DB::table($this->table)->where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            } else {
                $updateData = array_diff_key($item, array_flip(['version']));
                DB::table($this->table)->where("id", $item["id"])->update($updateData);
            }
        }
    }
}
