<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPortToSmartObjects extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        //
        if ( ! Schema::hasColumn( 'smart_objects', 'port' ) ) {
            Schema::table( 'smart_objects', function ( Blueprint $table ) {
                $table->integer( "port" );
            } );

        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'smart_objects', function ( Blueprint $table ) {
            //
        } );
    }
}
