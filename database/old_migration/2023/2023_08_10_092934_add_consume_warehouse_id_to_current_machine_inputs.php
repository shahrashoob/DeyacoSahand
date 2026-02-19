<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsumeWarehouseIdToCurrentMachineInputs extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'current_machine_inputs', function ( Blueprint $table ) {
            //
            $table->foreignId( "consume_warehouse_id" )->nullable()->comment( "انبار مصرف ماده اولیه" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'current_machine_inputs', function ( Blueprint $table ) {
            //
        } );
    }
}
