<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseTransactionStatusIdToProductRequestFormForm extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'product_request_form_form', function ( Blueprint $table ) {
            //
            $table->foreignId( "warehouse_transaction_status_id" )->comment( "وضعیت ثبت تراکنش های انبار برگ خروج " )
                  ->nullable();

        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'product_request_form_form', function ( Blueprint $table ) {
            //
        } );
    }
}
