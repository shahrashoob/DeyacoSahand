<?php

namespace Database\Seeders\LineProductStation;

use App\Models\LineProduct\GoodsKind;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoodsKindSettingSeeder extends Seeder
{
    private $data = [

        [
            "id"              => 1,
            "code"      => "default_unit_ids",
            "caption"=>"پیش فرض های واحد اصلی کالا",
        ],
        [
            "id"              => 2,
            "code"      => "default_sub_unit_ids",
            "caption"=>"پیش فرض های واحد فرعی کالا",
        ],
        [
            "id"              => 3,
            "code"      => "default_sub_unit2_ids",
            "caption"=>"پیش فرض های واحد فرعی 2 کالا",
        ],
        [
            "id"              => 4,
            "code"      => "default_goods_type_ids",
            "caption"=>"پیش فرض نوع کالا",
        ],
        [
            "id"              => 5,
            "code"      => "default_consumed_goods_kinds",
            "caption"=>"پیش فرض رسته های کالایی برای کالاهای مصرفی",
        ],
        [
            "id"              => 6,
            "code"      => "default_line_ids",
            "caption"=>"پیش فرض خط تولید",
        ],
        [
            "id"              => 7,
            "code"      => "default_station_ids",
            "caption"=>"پیش فرض خط تولید",
        ],
        [
            "id"              => 8,
            "code"      => "default_unit_of_measure_type_ids_in_production",
            "caption"=>"ترتیب اهمیت واحد های کالا در ماشین",
        ],
        [
            "id"              => 9,
            "code"      => "default_unit_of_measure_type_ids_in_sale",
            "caption"=>"ترتیب اهمیت واحد های کالا در فروش",
        ]
    ];
    private $table = 'goods_kind_settings';

    public function run() {

        foreach (GoodsKind::all() as $item){
            $this->data[]=[
                "id"              => 50000+$item->id,
                "code"      => "default_bom_warehouse_ids_".$item->id,
                "caption"=>"پیش فرض انبار تحویل مواد اولیه در رسته کالایی ".$item->caption,
            ];
            $this->data[]=[
                "id"              => 51000+$item->id,
                "code"      => "default_productive_consume_warehouse_ids_".$item->id,
                "caption"=>"پیش فرض انبار مصرف کالای تولیدی در رسته کالایی ".$item->caption,
            ];
            $this->data[]=[
                "id"              => 52000+$item->id,
                "code"      => "default_sampling_consume_warehouse_ids_".$item->id,
                "caption"=>"پیش فرض انبار مصرف کالای نمونه گیری در رسته کالایی ".$item->caption,
            ];
        }
        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )->where( "id", $item["id"] )->update($item);
            }

        }
    }
}

