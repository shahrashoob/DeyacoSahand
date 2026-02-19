<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveMachineIdFromProductRequestForms extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'product_request_forms', function ( Blueprint $table ) {
            //
//            $table->dropColumn("machine_id");
            $table->renameColumn( "machine_id", "machine_id_removed" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'product_request_forms', function ( Blueprint $table ) {
            //
        } );
    }
}
