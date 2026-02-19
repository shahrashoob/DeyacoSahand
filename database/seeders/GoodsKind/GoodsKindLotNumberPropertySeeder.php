<?php

namespace Database\Seeders\GoodsKind;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoodsKindLotNumberPropertySeeder extends Seeder
{
    private $data = [
        //
        [
            "id" => 1,
            "caption" => "کیلوگرم بر واحد کالا",
            "field_type_id" => 1,
            "special_unit_id" => 13,
            "min_value" => 0,
            "max_value" => 1000,
            "priority_number" => 1,
        ],
    ];

    private $data_goods_kind_lot_number = [
        //
        [
            "id" => 1,
            "goods_kind_id" => 5,
            "lot_number_property_id" => 1,
        ],
        [
            "id" => 2,
            "goods_kind_id" => 4,
            "lot_number_property_id" => 1,
        ],
    ];
    private $table = 'lot_number_properties';
    private $table_goods_kind_lot_number = 'goods_kind_lot_number_properties';

    public function run()
    {

        foreach ($this->data as $item) {

            if (!DB::table($this->table)->
            where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            }

        }
        foreach ($this->data_goods_kind_lot_number as $item) {

            if (!DB::table($this->table_goods_kind_lot_number)->
            where("id", $item["id"])->first()) {
                DB::table($this->table_goods_kind_lot_number)->insert($item);
            }

        }
    }
}
