<?php

namespace Database\Seeders\Accounting;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialOperationPatternTypeSeeder extends Seeder
{
    private $data = [
        //
        [ "id" => 1,"financial_credit_or_debit"=>"1", "caption" => 'فروش', ],
        [ "id" => 2,"financial_credit_or_debit"=>"2", "caption" => 'کالا', ],

    ];
    private $table = 'financial_operation_pattern_types';

    public function run() {
        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }
        }

    }
}
