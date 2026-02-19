<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContractorSupplyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => "ارسال پس از درخواست پیمانکار","status_id"=>1200],
        [ "id" => 2, "caption" => "ارسال شده از قبل","status_id"=>1200],
        [ "id" => 3, "caption" => "خرید از پیمانکار","status_id"=>1210],


    ];
    private $table = 'contractor_supply_types';

    public function run() {

        foreach ( $this->data as $item ) {

            DB::table( $this->table )->delete();
            foreach ( $this->data as $item ) {
                DB::table( $this->table )->insert( $item );
            }

        }
    }
}
