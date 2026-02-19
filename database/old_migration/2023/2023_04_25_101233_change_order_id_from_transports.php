<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeOrderIdFromTransports extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'transports', function ( Blueprint $table ) {
            //
            $table->integer( "series" )->nullable()->change();
            $table->string( "order_code" )->nullable()->change();
            $table->foreignId( "order_id" )->nullable()->change();
            $table->string( "customer_caption" )->nullable()->change();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'transports', function ( Blueprint $table ) {
            //
        } );
    }
}
