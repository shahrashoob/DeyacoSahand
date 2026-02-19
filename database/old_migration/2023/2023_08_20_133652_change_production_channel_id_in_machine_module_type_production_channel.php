<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProductionChannelIdInMachineModuleTypeProductionChannel extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_module_type_production_channel', function ( Blueprint $table ) {
            //
            $table->renameColumn( "production_channel_id", "production_channel_type_id" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_module_type_production_channel', function ( Blueprint $table ) {
            //
        } );
    }
}
