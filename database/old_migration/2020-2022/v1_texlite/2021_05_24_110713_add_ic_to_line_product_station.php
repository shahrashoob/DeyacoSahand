<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIcToLineProductStation extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'line_product_station', function ( Blueprint $table ) {
            $table->integer( "ic" )->nullable()->comment( "مرکز هزینه" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'line_product_station', function ( Blueprint $table ) {
            //
        } );
    }
}
