<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCanUsedMaterialWithDifferentLotPerProductionCardToMachineTypeInputBands extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_type_input_bands', function ( Blueprint $table ) {
            //
            $table->integer( "can_used_material_with_different_lot_per_production_card" )->default( 1 )->
            comment( "آبا برای هر کارت تولید امکان مصرف ماده با لات های مختلف وجود دارد؟" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_type_input_bands', function ( Blueprint $table ) {
            //
        } );
    }
}
