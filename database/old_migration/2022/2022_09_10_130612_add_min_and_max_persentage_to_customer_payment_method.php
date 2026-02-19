<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinAndMaxPersentageToCustomerPaymentMethod extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'customer_payment_method', function ( Blueprint $table ) {
            //
            $table->foreignId( "min_percentage" )->after( "payment_method_type_id" )->
            comment( "حداقل درصد مجاز پرداخت در این روش" );

            $table->renameColumn( "percentage", "max_percentage" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'customer_payment_method', function ( Blueprint $table ) {
            //
        } );
    }
}
