<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStationOperationTypeIdToStationOperations extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'station_operations', function ( Blueprint $table ) {
            //
            $table->foreignId( "station_operation_type_id" )->comment( "نوع عملیات:بچ، کانتینیوس" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'station_operations', function ( Blueprint $table ) {
            //
        } );
    }
}
