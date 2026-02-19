<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RawMaterialRequestAlgorithmTypeSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "الگوریتم 1: براساس موجودی انبارک و حداقل، حداکثر ظرفیت آن" ,"directory_namespace"=>"App\Models\LineProduct\Machine\RequestRawMaterialAlgorithm\Algorithm1WithInventoryAndMinMaxCapacity"],
        [ "id" => 2, "caption" => "الگوریتم 2: بدون در نظر گرفتن موجودی انبارک و بر اساس حداقل، حداکثر ظرفیت انبارک ","directory_namespace"=>"App\Models\LineProduct\Machine\RequestRawMaterialAlgorithm\Algorithm2WithMinMaxCapacity" ],
        [ "id" => 3, "caption" => "الگوریتم 3: بر اساس درخواست اپراتور ","directory_namespace"=>"App\Models\LineProduct\Machine\RequestRawMaterialAlgorithm\Algorithm3OperatorRequest" ],

        [
            "id" => 4,
//            "algorithm_type_id" => 100,
            "caption" => "الگوریتم 4: بدون در نظر گرفتن موجودی انبارک و به تفکیک تخصیص ",
            "directory_namespace" => "App\Models\LineProduct\Machine\RequestRawMaterialAlgorithm\Algorithm4WithMinMaxCapacityByBatch"
        ],

    ];
    /********** این جدول باید حذف شود *******/
    private $table = 'raw_material_request_algorithm_types';

    public function run() {

        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }


    }
}
