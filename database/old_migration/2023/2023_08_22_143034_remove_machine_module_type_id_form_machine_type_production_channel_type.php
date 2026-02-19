<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveMachineModuleTypeIdFormMachineTypeProductionChannelType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'machine_type_production_channel_type', function ( Blueprint $table ) {
            //
            $table->dropColumn( "machine_module_type_id" );
            $table->foreignId( "machine_type_id" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
