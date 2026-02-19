<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLastLayerPackingFormIdToMasterPackingFormId extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'warehouse_product', function ( Blueprint $table ) {
            //
            $table->renameColumn( "last_layer_packing_form_id", "master_packing_form_id" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'master_packing_form_id', function ( Blueprint $table ) {
            //
        } );
    }
}
