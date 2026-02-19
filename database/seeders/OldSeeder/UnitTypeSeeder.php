<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id"             => 1,
            "caption"        => ' واحد اصلی ',
            "active_status_id"=>1200
        ],
        [ "id"             => 2,
            "caption"        => 'واحد فرعی',
            "active_status_id"=>1200
        ],
        [ "id"             => 3,
            "caption"        => "واحد فرعی 2",
            "active_status_id"=>1200
        ],
        [ "id"             => 4,
            "caption"        => "بسته بندی",
            "active_status_id"=>1210
        ],
    ];
    private $table = 'unit_types';

    public function run() {

        foreach ( $this->data as $item ) {
            if (!DB::table($this->table)->where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            } else {
                if($item["id"]==4){
                    unset($item["active_status_id"]);
                }
                DB::table($this->table)->where("id", $item["id"])->update($item);
            }
        }


    }
}
