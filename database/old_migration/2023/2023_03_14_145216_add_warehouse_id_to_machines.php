<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdToMachines extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machines', function ( Blueprint $table ) {
            //
            $table->foreignId( "warehouse_id" )->comment( " انبارک ماشین" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machines', function ( Blueprint $table ) {
            //
        } );
    }
}
