<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMachineLogIdToCurrentMachineInputLogs extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'current_machine_input_logs', function ( Blueprint $table ) {
            //
            $table->foreignId( "machine_log_id" )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'current_machine_input_logs', function ( Blueprint $table ) {
            //
        } );
    }
}
