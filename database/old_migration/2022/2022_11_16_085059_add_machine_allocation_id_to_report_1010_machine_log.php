<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMachineAllocationIdToReport1010MachineLog extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'report_1010_machine_logs', function ( Blueprint $table ) {
            //
            $table->foreignId( "machine_allocation_id" )->nullable()->comment( "تخصیص ماشین" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'report_1010_machine_log', function ( Blueprint $table ) {
            //
        } );
    }
}
