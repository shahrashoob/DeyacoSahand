<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFormIdInMachineAllocationMaterialConsumed extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_allocation_material_consumed', function ( Blueprint $table ) {
            //
            $table->renameColumn( "form_id", "form_id_delete" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_allocation_material_consumed', function ( Blueprint $table ) {
            //
        } );
    }
}
