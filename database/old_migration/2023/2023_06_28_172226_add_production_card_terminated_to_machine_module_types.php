<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductionCardTerminatedToMachineModuleTypes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_module_types', function ( Blueprint $table ) {
            //
            $table->foreignId( "production_card_terminated" )->comment( "وضعیت کارت تولید خاتمه یافته شده" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_module_types', function ( Blueprint $table ) {
            //
        } );
    }
}
