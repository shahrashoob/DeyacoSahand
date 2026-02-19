<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMachineAllocationMaterialConsumedTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //
        Schema::table( 'machine_allocation_material_consumed', function ( Blueprint $table ) {
            $table->dropColumn( "current_machine_input_id" );
            $table->dropColumn( "current_machine_input_log_id" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }
}
