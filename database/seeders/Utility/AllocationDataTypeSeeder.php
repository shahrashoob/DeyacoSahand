<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllocationDataTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 100, "caption" => 'مقدار چله در زمان تخصیص ماشین' ],
        [ "id" => 200, "caption" => 'اطلاعات حالت های تخصیص' ],
        [ "id" => 300, "caption" => 'اطلاعات مقایسه با تخصیص قبلی' ],
        [ "id" => 400, "caption" => 'تنظیمات ستاب و عملیات' ],

    ];
    private $table = 'allocation_data_types';

    public function run() {

        foreach ( $this->data as $item ) {
            if(! DB::table($this->table)->where("id", $item["id"])->first()) {
                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )->where("id", $item["id"])->update($item);
            }
        }

    }
}
