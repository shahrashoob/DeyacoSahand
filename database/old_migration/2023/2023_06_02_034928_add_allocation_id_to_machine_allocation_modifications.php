<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllocationIdToMachineAllocationModifications extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_allocation_modifications', function ( Blueprint $table ) {
            //
            $table->foreignId( "allocation_id" )->comment( " تخصیص جاری ماشین" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_allocation_modifications', function ( Blueprint $table ) {
            //
        } );
    }
}
