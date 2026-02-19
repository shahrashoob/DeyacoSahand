<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderIdToProductRequestForms extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'product_request_forms', function ( Blueprint $table ) {
            //
            $table->foreignId( "order_id" )->nullable()->comment( "شماره سفارش" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'product_request_forms', function ( Blueprint $table ) {
            //
        } );
    }
}
