<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCarrierGroupIdToCarrierTypes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'carrier_types', function ( Blueprint $table ) {
            //
            $table->foreignId( "carrier_group_id" )->comment( "گروه حامل" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'carrier_types', function ( Blueprint $table ) {
            //
        } );
    }
}
