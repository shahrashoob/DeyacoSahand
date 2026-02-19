<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceDisplayedToCustomerWithTaxToCustomers extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'customers', function ( Blueprint $table ) {
            //
            $table->integer( "price_displayed_to_customer_with_tax" )->default( 1 )->
            comment( "قیمت نمایش داده شده به مشتری با ارزش افزوده باشد" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'customer_with_tax_to_customers', function ( Blueprint $table ) {
            //
        } );
    }
}
