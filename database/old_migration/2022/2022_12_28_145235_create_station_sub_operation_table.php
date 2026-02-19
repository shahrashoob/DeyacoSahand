<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStationSubOperationTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'station_sub_operations', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "station_operation_id" );
            $table->string( "caption" );
            $table->timestamps();
        } );
        DB::statement( "ALTER TABLE `station_sub_operations` comment 'زیر عملیات برای هر عملیات در ایستگاه کاری'" );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'station_sub_operation' );
    }
}
