<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaintenanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 100, "caption" => 'تغییر تراکم',
          "description_template" =>" مدیریت محترم بافندگی <br/> لطفا تراکم پود 1 و تراکم پود 2 ماشین را به ترتیب از #*value1*# به #*value2*# تغییر دهید. "
        ],
        [ "id" => 200, "caption" => 'رفع نقص ماشین',
          "description_template" =>" مدیریت تعمیرات نگهداری <br/> "." #*value1*#  بر روی ماشین #*value2*# رخ داده است، لطفا نسبت به رفع عیب اقدام مقتصی مبذول فرمایید. "
        ],

    ];
    private $table = 'maintenance_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
