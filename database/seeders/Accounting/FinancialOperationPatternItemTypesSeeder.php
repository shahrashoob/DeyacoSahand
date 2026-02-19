<?php

namespace Database\Seeders\Accounting;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialOperationPatternItemTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => 'حساب های مرتبط اصلی', ],
        [ "id" => 2, "caption" => 'حساب های مرتبط ارزش افزوده', ],
        [ "id" => 3, "caption" => 'حساب های مرتبط تخفیف', ],

    ];
    private $table = 'financial_operation_pattern_item_types';

    public function run() {
        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }
        }

    }
}
