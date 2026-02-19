<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStationSubOperationIdToLineProductStation extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'line_product_station', function ( Blueprint $table ) {
            //
            $table->foreignId( "station_sub_operation_id" )->nullable()->comment( "عملیات فرعی" );
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
