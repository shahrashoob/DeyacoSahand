<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActualConsumptionStatusIdToMachineAllocationModificationForm extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_allocation_modification_form', function ( Blueprint $table ) {
            //
            $table->foreignId( "actual_consumption_status_id" )->comment( "وضعیت محاسبه مصرف واقعی" )->default( 6021301 );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_allocation_modification_form', function ( Blueprint $table ) {
            //
        } );
    }
}
