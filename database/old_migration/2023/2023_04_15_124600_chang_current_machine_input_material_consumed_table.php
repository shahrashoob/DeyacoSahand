<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangCurrentMachineInputMaterialConsumedTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //
        Schema::table( 'current_machine_input_material_consumed', function ( Blueprint $table ) {
            $table->dropColumn( "product_id" );
            $table->dropColumn( "material_id" );
            $table->dropColumn( "lot_number_id" );
            $table->dropColumn( "packing_form_item_id" );
            $table->dropColumn( "amount_consumed" );

            $table->renameColumn( "machine_log_id", "start_machine_log_id" );
            $table->foreignId( "end_machine_log_id" );
            $table->foreignId( "status_id" );
            $table->foreignId( "allocation_id" )->after("id");

            $table->rename("machine_allocation_material_consumed");
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
