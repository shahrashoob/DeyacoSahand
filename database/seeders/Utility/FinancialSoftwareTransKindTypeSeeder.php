<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialSoftwareTransKindTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 10, "caption" => 'ثبت سند حسابداری',"caption_en"=>"accounting_document_status_id" ],
        [ "id" => 20, "caption" => 'ثبت تراکنش انبار',"caption_en"=>"warehouse_transaction_status_id" ],
        [ "id" => 30, "caption" => 'ثبت فاکتور فروش',"caption_en"=>"sale_invoice_status_id" ],

    ];
    private $table = 'financial_software_trans_kind_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
