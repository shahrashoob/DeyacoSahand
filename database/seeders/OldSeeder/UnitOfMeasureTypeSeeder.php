<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitOfMeasureTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id"             => 1,
            "caption"        => ' واحد اصلی',
        ],
        [ "id"             => 2,
            "caption"        => ' واحد فرعی',
        ],
        [ "id"             => 3,
            "caption"        => ' واحد فرعی 2',
        ],


        [ "id"             => 12,
            "caption"        => 'واحد اصلی - واحد فرعی',
        ],
        [ "id"             => 13,
            "caption"        => 'واحد اصلی - واحد فرعی 2',
        ],

        [ "id"             => 21,
            "caption"        => ' واحد فرعی - واحد اصلی',
        ],
        [ "id"             => 31,
            "caption"        => 'واحد فرعی 2 - واحد اصلی',
        ],
        [ "id"             => 123,
            "caption"        => ' واحد اصلی - واحد فرعی - واحد فرعی 2 ',
        ],
        [ "id"             => 132,
            "caption"        => "واحد اصلی - واحد فرعی 2 - واحد فرعی",
        ],
        [ "id"             => 213,
            "caption"        => 'واحد فرعی - واحد اصلی - واحد فرعی 2',
        ],
        [ "id"             => 231,
            "caption"        => 'واحد فرعی - واحد فرعی 2 - واحد اصلی',
        ],
        [ "id"             => 321,
            "caption"        => "واحد فرعی 2 - واحد فرعی - واحد اصلی",
        ],
        [ "id"             => 312,
            "caption"        => "واحد فرعی 2 - واحد اصلی - واحد فرعی",
        ],
    ];
    private $table = 'unit_of_measure_types';

    public function run() {

        foreach ( $this->data as $item ) {
            if (!DB::table($this->table)->where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            } else {
                DB::table($this->table)->where("id", $item["id"])->update($item);
            }
        }


    }
}
