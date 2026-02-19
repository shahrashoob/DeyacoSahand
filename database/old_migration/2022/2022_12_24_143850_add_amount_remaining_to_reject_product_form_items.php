<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountRemainingToRejectProductFormItems extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'reject_product_form_items', function ( Blueprint $table ) {
            //
            $table->float( "amount_remaining" )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'reject_product_form_items', function ( Blueprint $table ) {
            //
        } );
    }
}
