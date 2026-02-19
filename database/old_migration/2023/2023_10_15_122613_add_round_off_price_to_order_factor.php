<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoundOffPriceToOrderFactor extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'order_factor', function ( Blueprint $table ) {
            //
            $table->double( "round_off_price", 15, 6 )->default( 0 )->after( "special_off_price" )->
            comment( "تخصیف رندن کردن تا x رقم " );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'order_factor', function ( Blueprint $table ) {
            //
        } );
    }
}
