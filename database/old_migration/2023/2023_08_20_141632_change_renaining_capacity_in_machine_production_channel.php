<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRenainingCapacityInMachineProductionChannel extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_production_channel', function ( Blueprint $table ) {
            //
            $table->renameColumn( "remaining_capacity", "capacity" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_production_channel', function ( Blueprint $table ) {
            //
        } );
    }
}
