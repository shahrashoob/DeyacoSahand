<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJsonDataIdToMachineAllocationModificationForm extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_allocation_modification_form', function ( Blueprint $table ) {
            //
            $table->foreignId( "json_data_id" )->comment( "لاگ محاسبه مقدار واقعی" )->nullable();
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
