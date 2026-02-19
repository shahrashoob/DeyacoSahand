<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowingRawMaterialsToBeInjectedManuallyToMachineTypeInputBandGoodsKind extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_type_input_band_goods_kind', function ( Blueprint $table ) {
            //
            $table->integer( "allowing_raw_materials_to_be_injected_manually" )->default( 0 )->
            comment( "امکان تزریق مواد اولیه به صورت دستی وجود دارد؟ " );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'be_injected_manually_to_machine_type_input_band_goods_kind', function ( Blueprint $table ) {
            //
        } );
    }
}
