<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveEffectIsSharedInMachineTypeInputBandGoodsKind extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_type_input_band_goods_kind', function ( Blueprint $table ) {
            //
            $table->dropColumn( "effect_is_shared" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_type_input_band_goods_kind', function ( Blueprint $table ) {
            //
        } );
    }
}
