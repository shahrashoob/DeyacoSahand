<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCapacityFromProductionChannels extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if ( Schema::hasColumn( 'production_channels', 'capacity' ) ) {

            Schema::table( 'production_channels', function ( Blueprint $table ) {
                //
                $table->dropColumn( "capacity" );
            } );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'production_channels', function ( Blueprint $table ) {
            //
        } );
    }
}
