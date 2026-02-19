<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinancialSoftwareTransKindFomrIdToFinancialSoftwareTransKindLog extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'financial_software_trans_kind_log', function ( Blueprint $table ) {
            //
            $table->foreignId( "financial_software_trans_kind_form_id" )->
            comment( "به ازای هر فرم، نتیجه تراکنش ها مختلف را نمایش می هد." );

            $table->foreignId( "financial_software_trans_kind_type_id" );
            $table->longText( "result" );

            $table->dropColumn( "accounting_document_status_id" );
            $table->dropColumn( "warehouse_transaction_status_id" );
            $table->dropColumn( "sale_invoice_status_id" );
            $table->dropColumn( "accounting_document_result" );
            $table->dropColumn( "warehouse_transaction_result" );
            $table->dropColumn( "sale_invoice_result" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'financial_software_trans_kind_log', function ( Blueprint $table ) {
            //
        } );
    }
}
