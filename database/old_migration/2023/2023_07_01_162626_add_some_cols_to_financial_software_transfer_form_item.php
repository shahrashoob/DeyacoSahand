<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColsToFinancialSoftwareTransferFormItem extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'financial_software_trans_kind_form', function ( Blueprint $table ) {
            //
            $table->rename( "financial_software_transfer_form_item" );
        } );
        Schema::table( 'financial_software_transfer_form_item', function ( Blueprint $table ) {
            //
            $table->foreignId( "financial_software_transfer_form_id" )->after( "form_id" )->comment( "فرم انتقال به نرم افزار مالی" );
            $table->foreignId( "warehouse_product_id" )->after( "form_id" );
            $table->foreignId( "form_item_id" )->after( "form_id" );

            $table->double( "amount", 15, 6 )->after( "sale_invoice_status_id" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'financial_software_transfer_form_item', function ( Blueprint $table ) {
            //
        } );
    }
}
